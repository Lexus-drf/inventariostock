<?php
/**
 * Funciones de apoyo usadas en controladores y vistas.
 */

/** Escapa texto para mostrarlo en HTML (evita XSS). */
function e(mixed $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** Arma la URL de una ruta: url('productos/ver', ['id' => 3]). */
function url(string $ruta = 'inicio', array $params = []): string
{
    $query = http_build_query(array_merge(['r' => $ruta], $params));
    return 'index.php?' . $query;
}

function asset(string $ruta): string
{
    $archivo = BASE_PATH . '/assets/' . $ruta;
    $version = is_file($archivo) ? filemtime($archivo) : 1;
    return 'assets/' . $ruta . '?v=' . $version;
}

function redirect(string $ruta, array $params = []): void
{
    header('Location: ' . url($ruta, $params));
    exit;
}

/** Mensajes que se muestran una sola vez (después de redirigir). */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function flashes(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}

/** Token contra falsificación de formularios (CSRF). */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function es_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function soles(mixed $monto): string
{
    return 'S/ ' . number_format((float) $monto, 2, '.', ' ');
}

function fecha(?string $valor, bool $conHora = true): string
{
    if (!$valor) {
        return '—';
    }
    $t = strtotime($valor);
    return $conHora ? date('d/m/Y H:i', $t) : date('d/m/Y', $t);
}

function ahora(): string
{
    // $GLOBALS['ahora'] solo se usa al generar los datos de demostración.
    return $GLOBALS['ahora'] ?? date('Y-m-d H:i:s');
}

/** Valor enviado en un formulario (para volver a mostrarlo si hubo error). */
function old(string $campo, mixed $defecto = ''): mixed
{
    return $_SESSION['old'][$campo] ?? $defecto;
}

function guardar_old(array $datos): void
{
    $_SESSION['old'] = $datos;
}

function limpiar_old(): void
{
    unset($_SESSION['old']);
}

function entero(mixed $valor, int $defecto = 0): int
{
    return filter_var($valor, FILTER_VALIDATE_INT) !== false ? (int) $valor : $defecto;
}

function decimal(mixed $valor): ?float
{
    $valor = str_replace(',', '.', trim((string) $valor));
    return is_numeric($valor) ? round((float) $valor, 2) : null;
}

function texto(mixed $valor, int $max = 255): string
{
    return mb_substr(trim((string) $valor), 0, $max);
}

/** Etiquetas legibles para los tipos de movimiento del kardex. */
function etiqueta_movimiento(string $tipo): string
{
    return [
        'INGRESO'           => 'Ingreso',
        'VENTA'             => 'Venta',
        'DEVOLUCION_CAMBIO' => 'Cambio (devuelve)',
        'SALIDA_CAMBIO'     => 'Cambio (lleva)',
        'AJUSTE_ENTRADA'    => 'Ajuste (+)',
        'AJUSTE_SALIDA'     => 'Ajuste (−)',
        'TRASLADO_SALIDA'   => 'Traslado (sale)',
        'TRASLADO_ENTRADA'  => 'Traslado (llega)',
    ][$tipo] ?? $tipo;
}

function foto_producto(?string $foto): ?string
{
    return $foto && is_file(BASE_PATH . '/uploads/productos/' . $foto)
        ? 'uploads/productos/' . rawurlencode($foto)
        : null;
}

/** Fecha larga en español: "domingo, 11 de octubre de 2026". */
function strftime_es(int $t): string
{
    $dias  = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto',
              'septiembre', 'octubre', 'noviembre', 'diciembre'];
    return $dias[(int) date('w', $t)] . ', ' . date('j', $t) . ' de ' . $meses[(int) date('n', $t) - 1] . ' de ' . date('Y', $t);
}

/** Clase de color según el stock frente al mínimo. */
function clase_stock(int $disponible, int $minimo): string
{
    return $disponible <= 0 ? 'cero' : ($disponible <= $minimo ? 'bajo' : 'ok');
}

/** Talla abreviada para listas: "T40", o el texto tal cual si no es numérica ("Única", "M"). */
function talla_corta(string $talla): string
{
    return ctype_digit($talla) ? 'T' . $talla : $talla;
}
