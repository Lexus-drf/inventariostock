<?php
/**
 * Cuentas de usuario y roles (RF02).
 */
final class Usuario
{
    public static function listar(): array
    {
        return Database::all('SELECT id, nombre, usuario, rol, activo, ultimo_acceso, creado_en FROM usuario ORDER BY activo DESC, rol, nombre');
    }

    public static function obtener(int $id): ?array
    {
        return Database::one('SELECT id, nombre, usuario, rol, activo FROM usuario WHERE id = ?', [$id]);
    }

    public static function guardar(array $d, ?int $id = null): int
    {
        $d['nombre']  = texto($d['nombre'] ?? '', 100);
        $d['usuario'] = mb_strtolower(texto($d['usuario'] ?? '', 40));
        $d['rol']     = ($d['rol'] ?? '') === 'admin' ? 'admin' : 'vendedor';
        $clave        = (string) ($d['clave'] ?? '');

        if ($d['nombre'] === '' || !preg_match('/^[a-z0-9._-]{3,40}$/', $d['usuario'])) {
            throw new NegocioException('Indique el nombre y un usuario de 3 a 40 caracteres (letras, números, punto o guion).');
        }
        if (Database::value('SELECT 1 FROM usuario WHERE usuario = ? AND id <> ?', [$d['usuario'], $id ?? 0])) {
            throw new NegocioException('Ese nombre de usuario ya existe.');
        }
        if (!$id || $clave !== '') {
            self::validarClave($clave);
        }

        if ($id) {
            if ($id === Auth::id() && $d['rol'] !== 'admin') {
                throw new NegocioException('No puede quitarse a sí mismo el rol de administrador.');
            }
            Database::query('UPDATE usuario SET nombre = ?, usuario = ?, rol = ? WHERE id = ?',
                [$d['nombre'], $d['usuario'], $d['rol'], $id]);
            if ($clave !== '') {
                self::cambiarClave($id, $clave);
            }
            return $id;
        }
        return Database::insert('INSERT INTO usuario (nombre, usuario, password_hash, rol, creado_en) VALUES (?, ?, ?, ?, ?)',
            [$d['nombre'], $d['usuario'], password_hash($clave, PASSWORD_DEFAULT), $d['rol'], ahora()]);
    }

    public static function validarClave(string $clave): void
    {
        if (mb_strlen($clave) < 6) {
            throw new NegocioException('La contraseña debe tener al menos 6 caracteres.');
        }
    }

    public static function cambiarClave(int $id, string $clave): void
    {
        self::validarClave($clave);
        Database::query('UPDATE usuario SET password_hash = ?, intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?',
            [password_hash($clave, PASSWORD_DEFAULT), $id]);
    }

    public static function alternarActivo(int $id): void
    {
        if ($id === Auth::id()) {
            throw new NegocioException('No puede desactivar su propia cuenta.');
        }
        Database::query('UPDATE usuario SET activo = 1 - activo WHERE id = ?', [$id]);
    }
}
