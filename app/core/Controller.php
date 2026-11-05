<?php
/**
 * Controlador base: muestra vistas y controla permisos.
 */
abstract class Controller
{
    /** Las rutas públicas (sin sesión) se listan aquí en cada controlador. */
    protected array $publicas = [];

    public function antesDe(string $accion): void
    {
        if (!in_array($accion, $this->publicas, true) && !Auth::sesionValida()) {
            redirect('auth/login');
        }
        // Todo envío de formulario debe traer el token CSRF.
        if (es_post() && !csrf_valido()) {
            http_response_code(419);
            flash('danger', 'El formulario expiró. Vuelva a intentarlo.');
            $this->volver();
        }
    }

    protected function soloAdmin(): void
    {
        if (!Auth::esAdmin()) {
            http_response_code(403);
            $this->render('errores/403', [], 'Acceso restringido');
            exit;
        }
    }

    protected function render(string $vista, array $datos = [], string $titulo = ''): void
    {
        extract($datos, EXTR_SKIP);
        $tituloPagina = $titulo;
        ob_start();
        require APP_PATH . "/views/$vista.php";
        $contenido = ob_get_clean();
        require APP_PATH . '/views/layout/' . (Auth::usuario() ? 'app.php' : 'simple.php');
        limpiar_old();
    }

    protected function json(mixed $datos): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Vuelve a la página anterior (o al inicio). */
    protected function volver(): void
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        if ($ref && str_contains($ref, 'index.php')) {
            header('Location: ' . $ref);
            exit;
        }
        redirect('inicio');
    }

    protected function noEncontrado(string $que = 'El registro'): void
    {
        http_response_code(404);
        $this->render('errores/404', ['que' => $que], 'No encontrado');
        exit;
    }
}
