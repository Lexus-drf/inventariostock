<?php
/**
 * Ventas (RF07) y cambios de talla o modelo (RF08).
 * La venta en curso se arma en la sesión y se registra de una sola vez.
 */
class VentaController extends Controller
{
    public function carrito(): void
    {
        $items = [];
        $total = 0.0;
        foreach ($_SESSION['carrito'] ?? [] as $clave => $it) {
            $v = Producto::variante($it['variante_id']);
            if (!$v) {
                unset($_SESSION['carrito'][$clave]);
                continue;
            }
            $ub = $v['stock'][$it['ubicacion_id']] ?? ['ubicacion' => '', 'disponible' => 0];
            $items[$clave] = $it + [
                'nombre' => $v['nombre'], 'marca' => $v['marca'], 'color' => $v['color'], 'talla' => $v['talla'],
                'codigo' => $v['codigo'], 'foto' => $v['foto'], 'producto_id' => $v['producto_id'],
                'ubicacion' => $ub['ubicacion'] ?: Database::value('SELECT nombre FROM ubicacion WHERE id = ?', [$it['ubicacion_id']]),
                'disponible' => (int) ($ub['disponible'] ?? 0),
            ];
            $total += $it['cantidad'] * $it['precio'];
        }
        $this->render('ventas/carrito', ['items' => $items, 'total' => $total], 'Venta');
    }

    public function agregar(): void
    {
        if (!es_post()) {
            redirect('ventas/carrito');
        }
        $vid = entero($_POST['variante_id'] ?? 0);
        $uid = entero($_POST['ubicacion_id'] ?? 0);
        $cant = entero($_POST['cantidad'] ?? 0);
        $precio = decimal($_POST['precio'] ?? '');
        $v = Producto::variante($vid);

        if (!$v || !$uid || $cant <= 0 || $precio === null || $precio < 0) {
            flash('danger', 'Revise el artículo, la ubicación, la cantidad y el precio.');
            $this->volver();
        }
        $clave = "$vid-$uid";
        $enCarrito = $_SESSION['carrito'][$clave]['cantidad'] ?? 0;
        $disp = Inventario::disponible($vid, $uid);
        if ($enCarrito + $cant > $disp) {
            flash('danger', "Solo hay $disp disponible(s) de {$v['nombre']} {$v['color']} talla {$v['talla']} en esa ubicación"
                . ($enCarrito ? " y ya tiene $enCarrito en la venta." : '.'));
            $this->volver();
        }
        $_SESSION['carrito'][$clave] = [
            'variante_id' => $vid, 'ubicacion_id' => $uid,
            'cantidad' => $enCarrito + $cant, 'precio' => $precio,
        ];
        flash('success', "Agregado: {$v['nombre']} {$v['color']} talla {$v['talla']}.");
        if (($_POST['ir'] ?? '') === 'seguir') {
            $this->volver();
        }
        redirect('ventas/carrito');
    }

    public function actualizar(): void
    {
        $clave = (string) ($_POST['clave'] ?? '');
        if (es_post() && isset($_SESSION['carrito'][$clave])) {
            $cant = entero($_POST['cantidad'] ?? 0);
            $precio = decimal($_POST['precio'] ?? '');
            if ($cant <= 0) {
                unset($_SESSION['carrito'][$clave]);
            } elseif ($precio !== null && $precio >= 0) {
                $_SESSION['carrito'][$clave]['cantidad'] = $cant;
                $_SESSION['carrito'][$clave]['precio'] = $precio;
            }
        }
        redirect('ventas/carrito');
    }

    public function quitar(): void
    {
        if (es_post()) {
            unset($_SESSION['carrito'][(string) ($_POST['clave'] ?? '')]);
        }
        redirect('ventas/carrito');
    }

    public function vaciar(): void
    {
        if (es_post()) {
            unset($_SESSION['carrito']);
        }
        redirect('ventas/carrito');
    }

    public function confirmar(): void
    {
        if (!es_post() || empty($_SESSION['carrito'])) {
            redirect('ventas/carrito');
        }
        try {
            $op = Inventario::vender(Auth::id(), array_values($_SESSION['carrito']), texto($_POST['nota'] ?? '') ?: null);
            unset($_SESSION['carrito']);
            flash('success', 'Venta registrada. El stock se actualizó automáticamente.');
            redirect('ventas/ver', ['id' => $op]);
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
            redirect('ventas/carrito');
        }
    }

    /** Detalle de una operación registrada (comprobante interno, no es un documento tributario). */
    public function ver(): void
    {
        $id = entero($_GET['id'] ?? 0);
        $op = Database::one('SELECT o.*, u.nombre AS usuario FROM operacion o JOIN usuario u ON u.id = o.usuario_id WHERE o.id = ?', [$id]);
        if (!$op) {
            $this->noEncontrado('La operación');
        }
        $detalle = Database::all(
            'SELECT mv.*, p.nombre, v.color, v.talla, v.codigo, ub.nombre AS ubicacion
               FROM movimiento mv JOIN variante v ON v.id = mv.variante_id JOIN producto p ON p.id = v.producto_id
               JOIN ubicacion ub ON ub.id = mv.ubicacion_id
              WHERE mv.operacion_id = ? ORDER BY mv.id', [$id]);
        $this->render('ventas/ver', ['op' => $op, 'detalle' => $detalle], 'Operación N.° ' . $id);
    }

    public function cambio(): void
    {
        if (es_post()) {
            $devuelve = [
                'variante_id'  => entero($_POST['devuelve_id'] ?? 0),
                'ubicacion_id' => entero($_POST['devuelve_ubicacion'] ?? 0),
                'cantidad'     => entero($_POST['devuelve_cantidad'] ?? 1),
                'precio'       => decimal($_POST['devuelve_precio'] ?? '') ?? -1,
            ];
            $lleva = [
                'variante_id'  => entero($_POST['lleva_id'] ?? 0),
                'ubicacion_id' => entero($_POST['lleva_ubicacion'] ?? 0),
                'cantidad'     => entero($_POST['lleva_cantidad'] ?? 1),
                'precio'       => decimal($_POST['lleva_precio'] ?? '') ?? -1,
            ];
            try {
                if (!$devuelve['variante_id'] || !$lleva['variante_id']) {
                    throw new NegocioException('Seleccione el artículo que devuelve y el que lleva.');
                }
                if ($devuelve['precio'] < 0 || $lleva['precio'] < 0) {
                    throw new NegocioException('Indique precios válidos.');
                }
                [$op, $dif] = Inventario::cambiar(Auth::id(), $devuelve, $lleva, texto($_POST['nota'] ?? '') ?: null);
                flash('success', 'Cambio registrado. ' . ($dif > 0 ? 'Cobrar al cliente ' . soles($dif) . '.'
                    : ($dif < 0 ? 'Devolver al cliente ' . soles(-$dif) . '.' : 'Sin diferencia de precio.')));
                redirect('ventas/ver', ['id' => $op]);
            } catch (NegocioException $e) {
                flash('danger', $e->getMessage());
                guardar_old($_POST);
                redirect('ventas/cambio');
            }
        }
        $this->render('ventas/cambio', ['ubicaciones' => Producto::ubicaciones()], 'Cambio de talla o modelo');
    }
}
