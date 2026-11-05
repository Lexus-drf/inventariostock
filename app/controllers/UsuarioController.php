<?php
/** Gestión de cuentas y roles (RF02). Solo administrador. */
class UsuarioController extends Controller
{
    public function antesDe(string $accion): void
    {
        parent::antesDe($accion);
        $this->soloAdmin();
    }

    public function index(): void
    {
        $this->render('usuarios/index', ['usuarios' => Usuario::listar()], 'Usuarios');
    }

    public function crear(): void
    {
        $this->render('usuarios/form', ['usuario' => null], 'Nuevo usuario');
    }

    public function editar(): void
    {
        $u = Usuario::obtener(entero($_GET['id'] ?? 0)) ?? $this->noEncontrado('El usuario');
        $this->render('usuarios/form', ['usuario' => $u], 'Editar usuario');
    }

    public function guardar(): void
    {
        $id = entero($_POST['id'] ?? 0) ?: null;
        try {
            Usuario::guardar($_POST, $id);
            flash('success', $id ? 'Usuario actualizado.' : 'Usuario creado. Ya puede iniciar sesión.');
            redirect('usuarios');
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
            guardar_old($_POST);
            $id ? redirect('usuarios/editar', ['id' => $id]) : redirect('usuarios/crear');
        }
    }

    public function alternar(): void
    {
        try {
            Usuario::alternarActivo(entero($_POST['id'] ?? 0));
            flash('success', 'Estado del usuario actualizado.');
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
        }
        redirect('usuarios');
    }
}
