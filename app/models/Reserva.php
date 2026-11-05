<?php
/**
 * Apartados y encargos (M3, RF10–RF11).
 *  - Apartado: separa un par que hay en stock; deja de figurar como disponible.
 *  - Encargo: pedido de un artículo que no hay; al completarse se vende si ya llegó.
 * Del cliente solo se guarda nombre y teléfono, y se borran al cerrar la reserva.
 */
final class Reserva
{
    public static function crear(array $d, int $usuarioId): int
    {
        $tipo = $d['tipo'] === 'ENCARGO' ? 'ENCARGO' : 'APARTADO';
        if ($d['cantidad'] <= 0) {
            throw new NegocioException('La cantidad debe ser mayor que cero.');
        }
        if ($d['precio'] === null || $d['precio'] < 0) {
            throw new NegocioException('Indique un precio válido.');
        }
        if ($d['adelanto'] === null || $d['adelanto'] < 0 || $d['adelanto'] > $d['precio'] * $d['cantidad']) {
            throw new NegocioException('El adelanto debe estar entre 0 y el total de la reserva.');
        }
        if ($d['cliente_nombre'] === '' || !preg_match('/^[0-9 +]{6,20}$/', $d['cliente_telefono'])) {
            throw new NegocioException('Indique el nombre y un teléfono válido del cliente.');
        }
        if ($tipo === 'APARTADO' && (!$d['variante_id'] || !$d['ubicacion_id'])) {
            throw new NegocioException('Seleccione el artículo y la ubicación del par que se aparta.');
        }
        if ($tipo === 'ENCARGO' && !$d['variante_id'] && $d['descripcion'] === '') {
            throw new NegocioException('Seleccione el artículo del catálogo o describa lo que encarga el cliente.');
        }

        return Database::transaction(function () use ($d, $tipo, $usuarioId) {
            if ($tipo === 'APARTADO') {
                // Bloquea el stock para que nadie venda el mismo par mientras se aparta.
                Database::value('SELECT cantidad FROM stock WHERE variante_id = ? AND ubicacion_id = ? FOR UPDATE',
                    [$d['variante_id'], $d['ubicacion_id']]);
                $disp = Inventario::disponible($d['variante_id'], $d['ubicacion_id']);
                if ($d['cantidad'] > $disp) {
                    throw new NegocioException('No hay stock disponible suficiente para apartar ('
                        . max(0, $disp) . ' disponible). Puede registrarlo como encargo.');
                }
            }
            return Database::insert(
                'INSERT INTO reserva (tipo, variante_id, ubicacion_id, descripcion, cantidad, precio, adelanto,
                                      cliente_nombre, cliente_telefono, fecha, fecha_limite, usuario_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$tipo, $d['variante_id'] ?: null, $d['ubicacion_id'] ?: null, $d['descripcion'] ?: null,
                 $d['cantidad'], $d['precio'], $d['adelanto'], $d['cliente_nombre'], $d['cliente_telefono'],
                 ahora(), $d['fecha_limite'] ?: null, $usuarioId]);
        });
    }

    /** El cliente recoge y paga el saldo: se registra la venta y se cierra la reserva. */
    public static function completar(int $id, int $usuarioId, ?int $ubicacionId = null): int
    {
        $r = self::obtener($id);
        if (!$r || $r['estado'] !== 'ACTIVA') {
            throw new NegocioException('La reserva no existe o ya fue cerrada.');
        }
        if (!$r['variante_id']) {
            throw new NegocioException('Antes de completar el encargo, asígnele el artículo del catálogo que llegó.');
        }
        $ubicacion = $r['tipo'] === 'APARTADO' ? (int) $r['ubicacion_id'] : ($ubicacionId ?: (int) ($r['ubicacion_id'] ?: 1));

        return Database::transaction(function () use ($r, $usuarioId, $ubicacion) {
            $op = Inventario::vender($usuarioId, [[
                'variante_id'  => (int) $r['variante_id'],
                'ubicacion_id' => $ubicacion,
                'cantidad'     => (int) $r['cantidad'],
                'precio'       => (float) $r['precio'],
            ]], ($r['tipo'] === 'APARTADO' ? 'Apartado' : 'Encargo') . " N.° {$r['id']} (adelanto " . soles($r['adelanto']) . ')',
                (int) $r['id']);
            self::cerrar((int) $r['id'], 'COMPLETADA', $op);
            return $op;
        });
    }

    public static function anular(int $id): void
    {
        $r = self::obtener($id);
        if (!$r || $r['estado'] !== 'ACTIVA') {
            throw new NegocioException('La reserva no existe o ya fue cerrada.');
        }
        self::cerrar($id, 'ANULADA', null);
    }

    /** Asigna el artículo del catálogo a un encargo (cuando llega la mercadería). */
    public static function asignarVariante(int $id, int $varianteId, int $ubicacionId): void
    {
        Database::query("UPDATE reserva SET variante_id = ?, ubicacion_id = ? WHERE id = ? AND tipo = 'ENCARGO' AND estado = 'ACTIVA'",
            [$varianteId, $ubicacionId, $id]);
    }

    private static function cerrar(int $id, string $estado, ?int $operacionId): void
    {
        // Privacidad: los datos del cliente se eliminan al cerrar la reserva.
        Database::query('UPDATE reserva SET estado = ?, cerrado_en = ?, operacion_id = ?,
                                cliente_nombre = NULL, cliente_telefono = NULL
                          WHERE id = ?', [$estado, ahora(), $operacionId, $id]);
    }

    public static function obtener(int $id): ?array
    {
        return Database::one(
            'SELECT r.*, p.nombre AS producto, p.id AS producto_id, v.color, v.talla, v.codigo,
                    u.nombre AS ubicacion, us.nombre AS usuario
               FROM reserva r
               LEFT JOIN variante v ON v.id = r.variante_id
               LEFT JOIN producto p ON p.id = v.producto_id
               LEFT JOIN ubicacion u ON u.id = r.ubicacion_id
               JOIN usuario us ON us.id = r.usuario_id
              WHERE r.id = ?', [$id]);
    }

    public static function listar(string $estado = 'ACTIVA', int $limite = 200): array
    {
        $estado = in_array($estado, ['ACTIVA', 'COMPLETADA', 'ANULADA'], true) ? $estado : 'ACTIVA';
        return Database::all(
            'SELECT r.*, p.nombre AS producto, v.color, v.talla, v.codigo, u.nombre AS ubicacion, us.nombre AS usuario
               FROM reserva r
               LEFT JOIN variante v ON v.id = r.variante_id
               LEFT JOIN producto p ON p.id = v.producto_id
               LEFT JOIN ubicacion u ON u.id = r.ubicacion_id
               JOIN usuario us ON us.id = r.usuario_id
              WHERE r.estado = ?
              ORDER BY ' . ($estado === 'ACTIVA' ? 'r.fecha_limite IS NULL, r.fecha_limite, r.fecha' : 'r.cerrado_en DESC') . '
              LIMIT ' . (int) $limite, [$estado]);
    }

    public static function contarActivas(): int
    {
        return (int) Database::value("SELECT COUNT(*) FROM reserva WHERE estado = 'ACTIVA'");
    }
}
