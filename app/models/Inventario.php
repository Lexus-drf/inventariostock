<?php
/**
 * Lógica de inventario (M4). Toda operación:
 *  - se ejecuta en una transacción (se guarda completa o no se guarda),
 *  - bloquea las filas de stock que toca (evita ventas simultáneas del mismo par),
 *  - valida que el stock disponible alcance y nunca quede negativo,
 *  - registra cada entrada o salida en el kardex con usuario, fecha y hora.
 */
final class Inventario
{
    /** Stock que no está separado por un apartado activo. */
    public static function disponible(int $varianteId, int $ubicacionId, ?int $excluirReserva = null): int
    {
        $stock = (int) Database::value(
            'SELECT cantidad FROM stock WHERE variante_id = ? AND ubicacion_id = ?',
            [$varianteId, $ubicacionId]);
        return $stock - self::reservado($varianteId, $ubicacionId, $excluirReserva);
    }

    public static function reservado(int $varianteId, int $ubicacionId, ?int $excluirReserva = null): int
    {
        return (int) Database::value(
            "SELECT COALESCE(SUM(cantidad), 0) FROM reserva
              WHERE estado = 'ACTIVA' AND tipo = 'APARTADO'
                AND variante_id = ? AND ubicacion_id = ? AND id <> ?",
            [$varianteId, $ubicacionId, $excluirReserva ?? 0]);
    }

    /**
     * Venta de uno o varios artículos.
     * $items: [['variante_id', 'ubicacion_id', 'cantidad', 'precio'], ...]
     */
    public static function vender(int $usuarioId, array $items, ?string $nota = null, ?int $reservaId = null): int
    {
        if (!$items) {
            throw new NegocioException('Agregue al menos un artículo a la venta.');
        }
        return Database::transaction(function () use ($usuarioId, $items, $nota, $reservaId) {
            $total = 0.0;
            foreach ($items as $i) {
                self::validarCantidad($i['cantidad']);
                if ($i['precio'] < 0) {
                    throw new NegocioException('El precio no puede ser negativo.');
                }
                $total += $i['cantidad'] * $i['precio'];
            }
            $op = self::operacion('VENTA', $usuarioId, $total, $nota);
            foreach (self::ordenar($items) as $i) {
                self::salida($op, 'VENTA', $i['variante_id'], $i['ubicacion_id'], $i['cantidad'],
                    $usuarioId, precio: $i['precio'], excluirReserva: $reservaId);
            }
            return $op;
        });
    }

    /**
     * Ingreso de mercadería (por ejemplo, la compra de un viaje a Tacna).
     * $items: [['variante_id', 'ubicacion_id', 'cantidad', 'costo'], ...]
     */
    public static function ingresar(int $usuarioId, array $items, ?string $nota = null): int
    {
        if (!$items) {
            throw new NegocioException('Agregue al menos un artículo al ingreso.');
        }
        return Database::transaction(function () use ($usuarioId, $items, $nota) {
            $op = self::operacion('INGRESO', $usuarioId, 0, $nota);
            foreach (self::ordenar($items) as $i) {
                self::validarCantidad($i['cantidad']);
                self::entrada($op, 'INGRESO', $i['variante_id'], $i['ubicacion_id'], $i['cantidad'],
                    $usuarioId, costo: $i['costo'] ?? null);
            }
            return $op;
        });
    }

    /**
     * Cambio de talla o de modelo: el cliente devuelve un par y se lleva otro.
     * Devuelve [operacion_id, diferencia] (positiva: cobra; negativa: devuelve dinero).
     */
    public static function cambiar(int $usuarioId, array $devuelve, array $lleva, ?string $nota = null): array
    {
        if ($devuelve['variante_id'] === $lleva['variante_id'] && $devuelve['ubicacion_id'] === $lleva['ubicacion_id']) {
            throw new NegocioException('El artículo que lleva debe ser distinto del que devuelve.');
        }
        return Database::transaction(function () use ($usuarioId, $devuelve, $lleva, $nota) {
            self::validarCantidad($devuelve['cantidad']);
            self::validarCantidad($lleva['cantidad']);
            $diferencia = round($lleva['cantidad'] * $lleva['precio'] - $devuelve['cantidad'] * $devuelve['precio'], 2);
            $op = self::operacion('CAMBIO', $usuarioId, $diferencia, $nota);
            self::entrada($op, 'DEVOLUCION_CAMBIO', $devuelve['variante_id'], $devuelve['ubicacion_id'],
                $devuelve['cantidad'], $usuarioId, precio: $devuelve['precio'], motivo: $nota);
            self::salida($op, 'SALIDA_CAMBIO', $lleva['variante_id'], $lleva['ubicacion_id'],
                $lleva['cantidad'], $usuarioId, precio: $lleva['precio'], motivo: $nota);
            return [$op, $diferencia];
        });
    }

    /** Ajuste por merma, daño o diferencia de conteo. $cantidad positiva suma, negativa resta. */
    public static function ajustar(int $usuarioId, int $varianteId, int $ubicacionId, int $cantidad, string $motivo): int
    {
        if ($cantidad === 0) {
            throw new NegocioException('La cantidad del ajuste no puede ser cero.');
        }
        if (trim($motivo) === '') {
            throw new NegocioException('Indique el motivo del ajuste.');
        }
        return Database::transaction(function () use ($usuarioId, $varianteId, $ubicacionId, $cantidad, $motivo) {
            $op = self::operacion('AJUSTE', $usuarioId, 0, $motivo);
            if ($cantidad > 0) {
                self::entrada($op, 'AJUSTE_ENTRADA', $varianteId, $ubicacionId, $cantidad, $usuarioId, motivo: $motivo);
            } else {
                self::salida($op, 'AJUSTE_SALIDA', $varianteId, $ubicacionId, -$cantidad, $usuarioId, motivo: $motivo);
            }
            return $op;
        });
    }

    /** Traslado entre el local y la vivienda: el stock total no cambia. */
    public static function trasladar(int $usuarioId, int $varianteId, int $origen, int $destino, int $cantidad, ?string $nota = null): int
    {
        if ($origen === $destino) {
            throw new NegocioException('El origen y el destino deben ser distintos.');
        }
        return Database::transaction(function () use ($usuarioId, $varianteId, $origen, $destino, $cantidad, $nota) {
            self::validarCantidad($cantidad);
            $op = self::operacion('TRASLADO', $usuarioId, 0, $nota);
            foreach (self::ordenar([
                ['variante_id' => $varianteId, 'ubicacion_id' => $origen, 'sale' => true],
                ['variante_id' => $varianteId, 'ubicacion_id' => $destino, 'sale' => false],
            ]) as $p) {
                $p['sale']
                    ? self::salida($op, 'TRASLADO_SALIDA', $varianteId, $origen, $cantidad, $usuarioId, motivo: $nota)
                    : self::entrada($op, 'TRASLADO_ENTRADA', $varianteId, $destino, $cantidad, $usuarioId, motivo: $nota);
            }
            return $op;
        });
    }

    // ------------------------------------------------------------------

    private static function operacion(string $tipo, int $usuarioId, float $total, ?string $nota): int
    {
        return Database::insert(
            'INSERT INTO operacion (tipo, fecha, usuario_id, total, nota) VALUES (?, ?, ?, ?, ?)',
            [$tipo, ahora(), $usuarioId, $total, $nota ?: null]);
    }

    private static function entrada(int $op, string $tipo, int $varianteId, int $ubicacionId, int $cantidad,
                                    int $usuarioId, ?float $precio = null, ?float $costo = null, ?string $motivo = null): void
    {
        self::verificarVariante($varianteId, $ubicacionId);
        Database::query(
            'INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)',
            [$varianteId, $ubicacionId, $cantidad]);
        self::registrar($op, $tipo, 'E', $varianteId, $ubicacionId, $cantidad, $usuarioId, $precio, $costo, $motivo);
    }

    private static function salida(int $op, string $tipo, int $varianteId, int $ubicacionId, int $cantidad,
                                   int $usuarioId, ?float $precio = null, ?string $motivo = null, ?int $excluirReserva = null): void
    {
        self::verificarVariante($varianteId, $ubicacionId);
        // Bloquea la fila: otra venta del mismo par espera a que esta termine.
        $stock = (int) Database::value(
            'SELECT cantidad FROM stock WHERE variante_id = ? AND ubicacion_id = ? FOR UPDATE',
            [$varianteId, $ubicacionId]);
        $disponible = $stock - self::reservado($varianteId, $ubicacionId, $excluirReserva);

        if ($cantidad > $disponible) {
            $v = self::describir($varianteId);
            $ub = Database::value('SELECT nombre FROM ubicacion WHERE id = ?', [$ubicacionId]);
            $detalle = $disponible <= 0 ? 'no hay stock disponible' : "solo hay $disponible disponible(s)";
            throw new NegocioException("No se puede registrar: de $v en $ub $detalle.");
        }
        Database::query(
            'UPDATE stock SET cantidad = cantidad - ? WHERE variante_id = ? AND ubicacion_id = ?',
            [$cantidad, $varianteId, $ubicacionId]);
        self::registrar($op, $tipo, 'S', $varianteId, $ubicacionId, $cantidad, $usuarioId, $precio, null, $motivo);
    }

    private static function registrar(int $op, string $tipo, string $sentido, int $varianteId, int $ubicacionId,
                                      int $cantidad, int $usuarioId, ?float $precio, ?float $costo, ?string $motivo): void
    {
        $saldo = (int) Database::value('SELECT COALESCE(SUM(cantidad), 0) FROM stock WHERE variante_id = ?', [$varianteId]);
        Database::query(
            'INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo,
                                     precio_unitario, costo_unitario, motivo, usuario_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$op, ahora(), $tipo, $sentido, $varianteId, $ubicacionId, $cantidad, $saldo,
             $precio, $costo, $motivo ?: null, $usuarioId]);
    }

    private static function verificarVariante(int $varianteId, int $ubicacionId): void
    {
        if (!Database::value('SELECT 1 FROM variante WHERE id = ?', [$varianteId])) {
            throw new NegocioException('El artículo seleccionado no existe.');
        }
        if (!Database::value('SELECT 1 FROM ubicacion WHERE id = ?', [$ubicacionId])) {
            throw new NegocioException('La ubicación seleccionada no existe.');
        }
    }

    private static function validarCantidad(mixed $cantidad): void
    {
        if (!is_int($cantidad) || $cantidad <= 0) {
            throw new NegocioException('La cantidad debe ser un número entero mayor que cero.');
        }
        if ($cantidad > 9999) {
            throw new NegocioException('La cantidad es demasiado grande.');
        }
    }

    /** Orden fijo de bloqueo para evitar bloqueos cruzados entre transacciones. */
    private static function ordenar(array $items): array
    {
        usort($items, fn($a, $b) => [$a['variante_id'], $a['ubicacion_id']] <=> [$b['variante_id'], $b['ubicacion_id']]);
        return $items;
    }

    public static function describir(int $varianteId): string
    {
        $v = Database::one(
            'SELECT p.nombre, v.color, v.talla, v.codigo FROM variante v JOIN producto p ON p.id = v.producto_id WHERE v.id = ?',
            [$varianteId]);
        return $v ? "{$v['nombre']} {$v['color']} talla {$v['talla']} ({$v['codigo']})" : "artículo #$varianteId";
    }
}
