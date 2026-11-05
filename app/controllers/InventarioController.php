<?php
/**
 * Movimientos de inventario (M4): ingresos (RF06), ajustes (RF09),
 * traslados entre ubicaciones e historial de operaciones.
 */
class InventarioController extends Controller
{
    public function antesDe(string $accion): void
    {
        parent::antesDe($accion);
        if (in_array($accion, ['ingreso', 'ajuste', 'exportar'], true)) {
            $this->soloAdmin();
        }
    }

    public function ingreso(): void
    {
        if (es_post()) {
            $items = [];
            foreach ((array) ($_POST['items'] ?? []) as $fila) {
                $cant = entero($fila['cantidad'] ?? 0);
                $vid = entero($fila['variante_id'] ?? 0);
                if ($cant <= 0 || !$vid) {
                    continue;
                }
                $items[] = [
                    'variante_id'  => $vid,
                    'ubicacion_id' => entero($fila['ubicacion_id'] ?? 1, 1),
                    'cantidad'     => $cant,
                    'costo'        => decimal($fila['costo'] ?? ''),
                ];
            }
            try {
                $op = Inventario::ingresar(Auth::id(), $items, texto($_POST['nota'] ?? '') ?: null);
                if (!empty($_POST['actualizar_costo'])) {
                    foreach ($items as $i) {
                        if ($i['costo'] !== null) {
                            Database::query('UPDATE producto p JOIN variante v ON v.producto_id = p.id SET p.costo = ? WHERE v.id = ?',
                                [$i['costo'], $i['variante_id']]);
                        }
                    }
                }
                flash('success', 'Ingreso registrado: ' . array_sum(array_column($items, 'cantidad')) . ' unidad(es) sumadas al stock.');
                redirect('ventas/ver', ['id' => $op]);
            } catch (NegocioException $e) {
                flash('danger', $e->getMessage());
                redirect('inventario/ingreso', array_filter(['producto' => entero($_POST['producto'] ?? 0)]));
            }
        }
        $producto = !empty($_GET['producto']) ? Producto::obtener(entero($_GET['producto'])) : null;
        $this->render('inventario/ingreso', [
            'producto' => $producto, 'ubicaciones' => Producto::ubicaciones(),
        ], 'Ingreso de mercadería');
    }

    public function ajuste(): void
    {
        if (es_post()) {
            try {
                $cant = entero($_POST['cantidad'] ?? 0);
                $cant = ($_POST['sentido'] ?? '') === 'resta' ? -abs($cant) : abs($cant);
                $op = Inventario::ajustar(Auth::id(), entero($_POST['variante_id'] ?? 0), entero($_POST['ubicacion_id'] ?? 0),
                    $cant, texto($_POST['motivo'] ?? ''));
                flash('success', 'Ajuste registrado en el kardex.');
                redirect('ventas/ver', ['id' => $op]);
            } catch (NegocioException $e) {
                flash('danger', $e->getMessage());
                redirect('inventario/ajuste');
            }
        }
        $this->render('inventario/ajuste', ['ubicaciones' => Producto::ubicaciones()], 'Ajuste de stock');
    }

    public function traslado(): void
    {
        if (es_post()) {
            try {
                $op = Inventario::trasladar(Auth::id(), entero($_POST['variante_id'] ?? 0), entero($_POST['origen'] ?? 0),
                    entero($_POST['destino'] ?? 0), entero($_POST['cantidad'] ?? 0), texto($_POST['nota'] ?? '') ?: null);
                flash('success', 'Traslado registrado.');
                redirect('ventas/ver', ['id' => $op]);
            } catch (NegocioException $e) {
                flash('danger', $e->getMessage());
                redirect('inventario/traslado');
            }
        }
        $this->render('inventario/traslado', ['ubicaciones' => Producto::ubicaciones()], 'Traslado');
    }

    public function historial(): void
    {
        $f = [
            'desde'      => $this->fechaValida($_GET['desde'] ?? '', date('Y-m-d', strtotime('-6 days'))),
            'hasta'      => $this->fechaValida($_GET['hasta'] ?? '', date('Y-m-d')),
            'tipo'       => in_array($_GET['tipo'] ?? '', ['VENTA', 'INGRESO', 'CAMBIO', 'AJUSTE', 'TRASLADO'], true) ? $_GET['tipo'] : '',
            'usuario_id' => entero($_GET['usuario_id'] ?? 0),
        ];
        $this->render('inventario/historial', [
            'f' => $f,
            'ops' => Reporte::operaciones($f, 200),
            'usuarios' => Database::all('SELECT id, nombre FROM usuario ORDER BY nombre'),
        ], 'Historial de operaciones');
    }

    private function fechaValida(string $valor, string $defecto): string
    {
        $d = DateTime::createFromFormat('Y-m-d', $valor);
        return $d && $d->format('Y-m-d') === $valor ? $valor : $defecto;
    }
}
