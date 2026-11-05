<?php
/**
 * Reportes y consultas para el administrador (M6, RF13).
 * Las ventas incluyen las ventas directas y la diferencia cobrada o
 * devuelta en los cambios de talla.
 */
final class Reporte
{
    /** Resumen del día para la pantalla de inicio. */
    public static function resumenDia(string $dia): array
    {
        $r = Database::one(
            "SELECT COUNT(CASE WHEN tipo = 'VENTA' THEN 1 END) AS ventas,
                    COALESCE(SUM(CASE WHEN tipo IN ('VENTA','CAMBIO') THEN total END), 0) AS importe
               FROM operacion WHERE fecha >= ? AND fecha < ?",
            [$dia . ' 00:00:00', date('Y-m-d', strtotime("$dia +1 day")) . ' 00:00:00']);
        $r['pares'] = (int) Database::value(
            "SELECT COALESCE(SUM(cantidad), 0) FROM movimiento WHERE tipo = 'VENTA' AND fecha >= ? AND fecha < ?",
            [$dia . ' 00:00:00', date('Y-m-d', strtotime("$dia +1 day")) . ' 00:00:00']);
        return $r;
    }

    /** Ventas por día en un rango (incluye días sin ventas). */
    public static function ventasPorDia(string $desde, string $hasta): array
    {
        $filas = Database::all(
            "SELECT DATE(fecha) AS dia,
                    COUNT(CASE WHEN tipo = 'VENTA' THEN 1 END) AS ventas,
                    SUM(total) AS importe
               FROM operacion
              WHERE tipo IN ('VENTA','CAMBIO') AND fecha >= ? AND fecha < ?
              GROUP BY DATE(fecha)",
            [$desde . ' 00:00:00', date('Y-m-d', strtotime("$hasta +1 day")) . ' 00:00:00']);
        $pares = Database::all(
            "SELECT DATE(fecha) AS dia, SUM(cantidad) AS pares FROM movimiento
              WHERE tipo = 'VENTA' AND fecha >= ? AND fecha < ? GROUP BY DATE(fecha)",
            [$desde . ' 00:00:00', date('Y-m-d', strtotime("$hasta +1 day")) . ' 00:00:00']);
        $porDia = array_column($filas, null, 'dia');
        $paresDia = array_column($pares, 'pares', 'dia');

        $res = [];
        for ($d = strtotime($desde); $d <= strtotime($hasta); $d = strtotime('+1 day', $d)) {
            $k = date('Y-m-d', $d);
            $res[] = [
                'dia'     => $k,
                'ventas'  => (int) ($porDia[$k]['ventas'] ?? 0),
                'importe' => (float) ($porDia[$k]['importe'] ?? 0),
                'pares'   => (int) ($paresDia[$k] ?? 0),
            ];
        }
        return $res;
    }

    /** Ventas agrupadas por semana (lunes a domingo). */
    public static function ventasPorSemana(int $semanas = 8): array
    {
        $lunes = strtotime('monday this week');
        $inicio = strtotime('-' . ($semanas - 1) . ' weeks', $lunes);
        $dias = self::ventasPorDia(date('Y-m-d', $inicio), date('Y-m-d', strtotime('+6 days', $lunes)));
        $res = [];
        foreach ($dias as $d) {
            $t = strtotime($d['dia']);
            $k = date('Y-m-d', strtotime('monday this week', $t));
            $res[$k] ??= ['semana' => $k, 'hasta' => date('Y-m-d', strtotime('+6 days', strtotime($k))),
                          'ventas' => 0, 'importe' => 0.0, 'pares' => 0];
            $res[$k]['ventas']  += $d['ventas'];
            $res[$k]['importe'] += $d['importe'];
            $res[$k]['pares']   += $d['pares'];
        }
        return array_values($res);
    }

    /** Existencias y valorización del inventario por categoría. */
    public static function valorizacion(): array
    {
        return Database::all(
            'SELECT c.nombre AS categoria,
                    COUNT(DISTINCT p.id) AS modelos,
                    COALESCE(SUM(s.cantidad), 0) AS unidades,
                    COALESCE(SUM(s.cantidad * p.costo), 0) AS valor_costo,
                    COALESCE(SUM(s.cantidad * p.precio_venta), 0) AS valor_venta
               FROM categoria c
               JOIN producto p ON p.categoria_id = c.id AND p.activo = 1
               JOIN variante v ON v.producto_id = p.id
               LEFT JOIN stock s ON s.variante_id = v.id
              GROUP BY c.id, c.nombre
              ORDER BY valor_costo DESC');
    }

    public static function existenciasPorUbicacion(): array
    {
        return Database::all(
            'SELECT u.nombre AS ubicacion, COALESCE(SUM(s.cantidad), 0) AS unidades,
                    COALESCE(SUM(s.cantidad * p.costo), 0) AS valor_costo
               FROM ubicacion u
               LEFT JOIN stock s ON s.ubicacion_id = u.id
               LEFT JOIN variante v ON v.id = s.variante_id
               LEFT JOIN producto p ON p.id = v.producto_id
              GROUP BY u.id, u.nombre ORDER BY u.id');
    }

    /** Rotación: unidades vendidas por modelo en el periodo, frente a su stock actual. */
    public static function rotacion(string $desde, string $hasta, int $limite = 50): array
    {
        return Database::all(
            "SELECT p.id, p.nombre, m.nombre AS marca, c.nombre AS categoria,
                    COALESCE(ven.vendidas, 0) AS vendidas,
                    COALESCE(ven.importe, 0) AS importe,
                    COALESCE(st.stock, 0) AS stock
               FROM producto p
               JOIN marca m ON m.id = p.marca_id
               JOIN categoria c ON c.id = p.categoria_id
               LEFT JOIN (SELECT v.producto_id, SUM(mv.cantidad) AS vendidas,
                                 SUM(mv.cantidad * mv.precio_unitario) AS importe
                            FROM movimiento mv JOIN variante v ON v.id = mv.variante_id
                           WHERE mv.tipo = 'VENTA' AND mv.fecha >= ? AND mv.fecha < ?
                           GROUP BY v.producto_id) ven ON ven.producto_id = p.id
               LEFT JOIN (SELECT v.producto_id, SUM(s.cantidad) AS stock
                            FROM stock s JOIN variante v ON v.id = s.variante_id
                           GROUP BY v.producto_id) st ON st.producto_id = p.id
              WHERE p.activo = 1
              ORDER BY vendidas DESC, p.nombre
              LIMIT " . (int) $limite,
            [$desde . ' 00:00:00', date('Y-m-d', strtotime("$hasta +1 day")) . ' 00:00:00']);
    }

    /** Historial de operaciones (ventas, ingresos, cambios...) con su detalle. */
    public static function operaciones(array $f, int $limite = 100): array
    {
        $where = ['o.fecha >= ?', 'o.fecha < ?'];
        $params = [$f['desde'] . ' 00:00:00', date('Y-m-d', strtotime($f['hasta'] . ' +1 day')) . ' 00:00:00'];
        if (!empty($f['tipo'])) {
            $where[] = 'o.tipo = ?';
            $params[] = $f['tipo'];
        }
        if (!empty($f['usuario_id'])) {
            $where[] = 'o.usuario_id = ?';
            $params[] = (int) $f['usuario_id'];
        }
        $ops = Database::all(
            'SELECT o.*, u.nombre AS usuario FROM operacion o JOIN usuario u ON u.id = o.usuario_id
              WHERE ' . implode(' AND ', $where) . ' ORDER BY o.fecha DESC, o.id DESC LIMIT ' . (int) $limite, $params);
        if (!$ops) {
            return [];
        }
        $ids = implode(',', array_map('intval', array_column($ops, 'id')));
        $movs = Database::all(
            "SELECT mv.operacion_id, mv.tipo, mv.sentido, mv.cantidad, mv.precio_unitario, mv.costo_unitario,
                    p.nombre, v.color, v.talla, v.codigo, ub.nombre AS ubicacion
               FROM movimiento mv
               JOIN variante v ON v.id = mv.variante_id
               JOIN producto p ON p.id = v.producto_id
               JOIN ubicacion ub ON ub.id = mv.ubicacion_id
              WHERE mv.operacion_id IN ($ids) ORDER BY mv.id");
        $porOp = [];
        foreach ($movs as $m) {
            $porOp[$m['operacion_id']][] = $m;
        }
        foreach ($ops as &$o) {
            $o['detalle'] = $porOp[$o['id']] ?? [];
        }
        return $ops;
    }

    /** Movimientos para exportar a CSV (respaldo y cotejo en la semana en paralelo). */
    public static function movimientosDesde(string $desde): array
    {
        return Database::all(
            'SELECT mv.id, mv.fecha, mv.tipo, mv.sentido, v.codigo, p.nombre AS producto, v.color, v.talla,
                    ub.nombre AS ubicacion, mv.cantidad, mv.saldo, mv.precio_unitario, mv.costo_unitario,
                    mv.motivo, us.nombre AS usuario, mv.operacion_id
               FROM movimiento mv
               JOIN variante v ON v.id = mv.variante_id
               JOIN producto p ON p.id = v.producto_id
               JOIN ubicacion ub ON ub.id = mv.ubicacion_id
               JOIN usuario us ON us.id = mv.usuario_id
              WHERE mv.fecha >= ?
              ORDER BY mv.id', [$desde . ' 00:00:00']);
    }
}
