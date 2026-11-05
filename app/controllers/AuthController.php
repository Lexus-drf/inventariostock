<?php
/** Inicio y cierre de sesión. */
class AuthController extends Controller
{
    protected array $publicas = ['login'];

    public function login(): void
    {
        if (Auth::usuario() && Auth::sesionValida()) {
            redirect('inicio');
        }
        $error = null;
        if (es_post()) {
            $resultado = Auth::intentar(texto($_POST['usuario'] ?? '', 40), (string) ($_POST['clave'] ?? ''));
            if ($resultado === true) {
                redirect('inicio');
            }
            $error = $resultado;
            guardar_old(['usuario' => $_POST['usuario'] ?? '']);
        }
        $this->render('auth/login', ['error' => $error], 'Iniciar sesión');
    }

    public function salir(): void
    {
        if (es_post()) {
            Auth::salir();
            session_start();
            flash('success', 'Sesión cerrada correctamente.');
        }
        redirect('auth/login');
    }
}
