<?php
/** Cambio de la propia contraseña. */
class CuentaController extends Controller
{
    public function index(): void
    {
        if (es_post()) {
            $actual = (string) ($_POST['actual'] ?? '');
            $nueva = (string) ($_POST['nueva'] ?? '');
            $hash = (string) Database::value('SELECT password_hash FROM usuario WHERE id = ?', [Auth::id()]);
            try {
                if (!password_verify($actual, $hash)) {
                    throw new NegocioException('La contraseña actual no es correcta.');
                }
                if ($nueva !== (string) ($_POST['confirmar'] ?? '')) {
                    throw new NegocioException('La confirmación no coincide con la nueva contraseña.');
                }
                Usuario::cambiarClave(Auth::id(), $nueva);
                flash('success', 'Contraseña actualizada.');
                redirect('inicio');
            } catch (NegocioException $e) {
                flash('danger', $e->getMessage());
                redirect('cuenta');
            }
        }
        $this->render('cuenta/index', [], 'Cambiar contraseña');
    }
}
