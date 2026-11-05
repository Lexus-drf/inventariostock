<?php
/**
 * InventarioStock — punto de entrada único (front controller).
 * Todas las páginas pasan por aquí: index.php?r=controlador/accion
 */
require __DIR__ . '/app/core/bootstrap.php';

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

$rutas = [
    'auth'       => 'AuthController',
    'inicio'     => 'InicioController',
    'buscar'     => 'BuscarController',
    'productos'  => 'ProductoController',
    'catalogo'   => 'CatalogoController',
    'ventas'     => 'VentaController',
    'inventario' => 'InventarioController',
    'reservas'   => 'ReservaController',
    'reportes'   => 'ReporteController',
    'usuarios'   => 'UsuarioController',
    'cuenta'     => 'CuentaController',
];

$ruta = (string) ($_GET['r'] ?? 'inicio');
[$nombre, $accion] = array_pad(explode('/', $ruta, 2), 2, 'index');
$accion = $accion !== '' ? $accion : 'index';

try {
    if (!isset($rutas[$nombre]) || !preg_match('/^[a-z][a-zA-Z]*$/', $accion)) {
        throw new Error('ruta');
    }
    $controlador = new $rutas[$nombre]();
    if (!method_exists($controlador, $accion) || !(new ReflectionMethod($controlador, $accion))->isPublic()
        || $accion === 'antesDe') {
        throw new Error('ruta');
    }
    $controlador->antesDe($accion);
    $controlador->$accion();
} catch (Error $e) {
    if ($e->getMessage() !== 'ruta') {
        throw $e;
    }
    http_response_code(404);
    echo 'Página no encontrada. <a href="index.php">Volver al inicio</a>';
} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    $mensaje = str_contains($e->getMessage(), '[2002]') || str_contains($e->getMessage(), '[1049]')
        ? 'No se pudo conectar con la base de datos. Verifique que MySQL esté iniciado en XAMPP y que la base "inventariostock" esté importada.'
        : 'Ocurrió un error con la base de datos. La operación no se guardó.';
    require APP_PATH . '/views/errores/500.php';
}
