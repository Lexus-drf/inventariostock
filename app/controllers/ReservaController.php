<?php
/** Apartados y encargos (M3, RF10–RF11). */
class ReservaController extends Controller
{
    public function index(): void
    {
        $estado = in_array($_GET['estado'] ?? '', ['ACTIVA', 'COMPLETADA', 'ANULADA'], true) ? $_GET['estado'] : 'ACTIVA';
        $this->render('reservas/index', [
            'estado' => $estado, 'reservas' => Reserva::listar($estado), 'ubicaciones' => Producto::ubicaciones(),
        ], 'Apartados y encargos');
    }

    public function crear(): void
    {
        if (es_post()) {
            $d = [
                'tipo'             => ($_POST['tipo'] ?? '') === 'ENCARGO' ? 'ENCARGO' : 'APARTADO',
                'variante_id'      => entero($_POST['variante_id'] ?? 0),
                'ubicacion_id'     => entero($_POST['ubicacion_id'] ?? 0),
                'descripcion'      => texto($_POST['descripcion'] ?? ''),
                'cantidad'         => entero($_POST['cantidad'] ?? 1),
                'precio'           => decimal($_POST['precio'] ?? ''),
                'adelanto'         => decimal($_POST['adelanto'] ?? '0'),
                'cliente_nombre'   => texto($_POST['cliente_nombre'] ?? '', 100),
                'cliente_telefono' => texto($_POST['cliente_telefono'] ?? '', 20),
                'fecha_limite'     => preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha_limite'] ?? '') ? $_POST['fecha_limite'] : null,
            ];
            try {
                Reserva::crear($d, Auth::id());
                flash('success', $d['tipo'] === 'APARTADO'
                    ? 'Apartado registrado. Ese par ya no figura como disponible.'
                    : 'Encargo registrado.');
                redirect('reservas');
            } catch (NegocioException $e) {
                flash('danger', $e->getMessage());
                guardar_old($_POST);
                redirect('reservas/crear', ['tipo' => $d['tipo']]);
            }
        }
        $this->render('reservas/form', [
            'tipo' => ($_GET['tipo'] ?? '') === 'ENCARGO' ? 'ENCARGO' : 'APARTADO',
            'ubicaciones' => Producto::ubicaciones(),
        ], 'Nuevo apartado o encargo');
    }

    public function completar(): void
    {
        try {
            $op = Reserva::completar(entero($_POST['id'] ?? 0), Auth::id(), entero($_POST['ubicacion_id'] ?? 0) ?: null);
            flash('success', 'Reserva completada y venta registrada.');
            redirect('ventas/ver', ['id' => $op]);
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
            redirect('reservas');
        }
    }

    public function anular(): void
    {
        try {
            Reserva::anular(entero($_POST['id'] ?? 0));
            flash('success', 'Reserva anulada. El stock apartado vuelve a estar disponible y se borraron los datos del cliente.');
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
        }
        redirect('reservas');
    }

    /** Para encargos: indica qué artículo del catálogo llegó para el cliente. */
    public function asignar(): void
    {
        $vid = entero($_POST['variante_id'] ?? 0);
        if ($vid) {
            Reserva::asignarVariante(entero($_POST['id'] ?? 0), $vid, entero($_POST['ubicacion_id'] ?? 1, 1));
            flash('success', 'Artículo asignado al encargo. Ya puede completarlo cuando el cliente recoja.');
        } else {
            flash('danger', 'Seleccione el artículo que llegó.');
        }
        redirect('reservas');
    }
}
