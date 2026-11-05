<?php
/**
 * Autenticación y permisos por rol (RF01).
 * - Contraseñas cifradas con password_hash.
 * - Bloqueo temporal tras varios intentos fallidos.
 * - Cierre de sesión por inactividad.
 */
final class Auth
{
    public static function intentar(string $usuario, string $clave): string|bool
    {
        $u = Database::one('SELECT * FROM usuario WHERE usuario = ?', [$usuario]);
        $mensajeGenerico = 'Usuario o contraseña incorrectos.';

        if (!$u || !$u['activo']) {
            password_verify($clave, '$2y$10$abcdefghijklmnopqrstuuJ3eW1v2bCkq0yYc6t5vVZ6WcJgYpG2e'); // tiempo constante
            return $mensajeGenerico;
        }
        if ($u['bloqueado_hasta'] && strtotime($u['bloqueado_hasta']) > time()) {
            $min = (int) ceil((strtotime($u['bloqueado_hasta']) - time()) / 60);
            return "Cuenta bloqueada por intentos fallidos. Intente en $min minuto(s).";
        }
        if (!password_verify($clave, $u['password_hash'])) {
            $intentos = $u['intentos_fallidos'] + 1;
            $bloqueo  = null;
            if ($intentos >= config('app.intentos_login')) {
                $bloqueo  = date('Y-m-d H:i:s', time() + 60 * config('app.minutos_bloqueo'));
                $intentos = 0;
            }
            Database::query('UPDATE usuario SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id = ?',
                [$intentos, $bloqueo, $u['id']]);
            return $mensajeGenerico;
        }

        if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
            Database::query('UPDATE usuario SET password_hash = ? WHERE id = ?',
                [password_hash($clave, PASSWORD_DEFAULT), $u['id']]);
        }
        Database::query('UPDATE usuario SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = ? WHERE id = ?',
            [ahora(), $u['id']]);

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'      => (int) $u['id'],
            'nombre'  => $u['nombre'],
            'usuario' => $u['usuario'],
            'rol'     => $u['rol'],
        ];
        $_SESSION['ultima_actividad'] = time();
        return true;
    }

    public static function salir(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function id(): int
    {
        return (int) (self::usuario()['id'] ?? 0);
    }

    public static function esAdmin(): bool
    {
        return (self::usuario()['rol'] ?? '') === 'admin';
    }

    /** Verifica sesión activa y la renueva; devuelve false si expiró. */
    public static function sesionValida(): bool
    {
        if (!self::usuario()) {
            return false;
        }
        $limite = 60 * config('app.minutos_sesion');
        if (time() - ($_SESSION['ultima_actividad'] ?? 0) > $limite) {
            self::salir();
            session_start();
            flash('warning', 'Su sesión se cerró por inactividad. Ingrese nuevamente.');
            return false;
        }
        // Si el administrador desactivó la cuenta, la sesión termina.
        $activo = Database::value('SELECT activo FROM usuario WHERE id = ?', [self::id()]);
        if (!$activo) {
            self::salir();
            session_start();
            return false;
        }
        $_SESSION['ultima_actividad'] = time();
        return true;
    }
}
