<?php
/**
 * Configuración de InventarioStock.
 *
 * Los datos de conexión a MySQL NO se escriben aquí: se leen del archivo
 * ".env" que está en la carpeta principal del proyecto (no se sube a Git).
 * Para crearlo, copie ".env.example" como ".env" y complete sus valores.
 */

/** Lee el archivo .env (formato CLAVE=valor) y devuelve sus valores. */
function leer_env(string $archivo): array
{
    $valores = [];
    if (!is_file($archivo)) {
        return $valores;
    }
    foreach (file($archivo, FILE_IGNORE_NEW_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }
        [$clave, $valor] = array_map('trim', explode('=', $linea, 2));
        $valores[$clave] = trim($valor, "\"'");
    }
    return $valores;
}

$archivoEnv = dirname(__DIR__) . '/.env';
if (!is_file($archivoEnv)) {
    http_response_code(500);
    exit('Falta el archivo .env. Copie ".env.example" como ".env" en la carpeta del proyecto y complete los datos de MySQL.');
}
$env = leer_env($archivoEnv);

$config = [
    'db' => [
        'host' => $env['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($env['DB_PORT'] ?? 3306),
        'name' => $env['DB_NAME'] ?? 'inventariostock',
        'user' => $env['DB_USER'] ?? '',
        'pass' => $env['DB_PASS'] ?? '',
    ],
    'app' => [
        'nombre'           => 'InventarioStock',
        'tienda'           => 'Tienda de calzado y artículos deportivos',
        'zona_horaria'     => 'America/Lima',
        'minutos_sesion'   => 30,      // cierre de sesión por inactividad
        'intentos_login'   => 5,       // intentos fallidos antes de bloquear
        'minutos_bloqueo'  => 10,
        'foto_max_mb'      => 3,
    ],
];

// Permite sobrescribir valores sin tocar este archivo (opcional).
if (is_file(__DIR__ . '/config.local.php')) {
    $config = array_replace_recursive($config, require __DIR__ . '/config.local.php');
}

return $config;
