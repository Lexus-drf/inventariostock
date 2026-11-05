<?php
/**
 * Conexión única a MySQL mediante PDO.
 * Las consultas preparadas evitan la inyección SQL.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $db  = config('db');
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            self::$pdo->exec("SET time_zone = '" . date('P') . "'");
        }
        return self::$pdo;
    }

    /** Ejecuta una consulta con parámetros y devuelve el statement. */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $fila = self::query($sql, $params)->fetch();
        return $fila === false ? null : $fila;
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $valor = self::query($sql, $params)->fetchColumn();
        return $valor === false ? null : $valor;
    }

    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int) self::pdo()->lastInsertId();
    }

    /**
     * Ejecuta un bloque dentro de una transacción: si algo falla,
     * se deshace todo (la operación se guarda completa o no se guarda).
     */
    public static function transaction(callable $bloque): mixed
    {
        $pdo = self::pdo();
        if ($pdo->inTransaction()) {
            return $bloque(); // ya forma parte de una transacción mayor
        }
        $pdo->beginTransaction();
        try {
            $resultado = $bloque();
            $pdo->commit();
            return $resultado;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
