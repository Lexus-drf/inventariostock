<?php
/**
 * Arranque de la aplicación: configuración, sesión segura y carga de clases.
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');

$GLOBALS['config'] = require BASE_PATH . '/config/config.php';
date_default_timezone_set(config('app.zona_horaria'));

// Carga automática: busca la clase en core/, models/ y controllers/.
spl_autoload_register(function (string $clase): void {
    foreach (['core', 'models', 'controllers'] as $carpeta) {
        $archivo = APP_PATH . "/$carpeta/$clase.php";
        if (is_file($archivo)) {
            require $archivo;
            return;
        }
    }
});

require APP_PATH . '/core/helpers.php';

// Sesión con cookies seguras.
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
      || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
session_name('INVSTOCK');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

/** Lee un valor de configuración con notación "seccion.clave". */
function config(string $clave, mixed $defecto = null): mixed
{
    $valor = $GLOBALS['config'];
    foreach (explode('.', $clave) as $parte) {
        if (!is_array($valor) || !array_key_exists($parte, $valor)) {
            return $defecto;
        }
        $valor = $valor[$parte];
    }
    return $valor;
}
