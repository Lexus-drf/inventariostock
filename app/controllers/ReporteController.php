<?php
/** Reportes, alertas y exportación (M5, M6). Solo administrador. */
class ReporteController extends Controller
{
    public function antesDe(string $accion): void
    {
        parent::antesDe($accion);
        $this->soloAdmin();
    }

    public function index(): void
    {
        [$desde, $hasta] = $this->rango(13);
        $dias = Reporte::ventasPorDia($desde, $hasta);
        $this->render('reportes/ventas', [
            'desde' => $desde, 'hasta' => $hasta, 'dias' => $dias,
            'semanas' => Reporte::ventasPorSemana(8),
            'totales' => [
                'ventas'  => array_sum(array_column($dias, 'ventas')),
                'importe' => array_sum(array_column($dias, 'importe')),
                'pares'   => array_sum(array_column($dias, 'pares')),
            ],
        ], 'Reporte de ventas');
    }

    public function existencias(): void
    {
        $this->render('reportes/existencias', [
            'categorias'  => Reporte::valorizacion(),
            'ubicaciones' => Reporte::existenciasPorUbicacion(),
        ], 'Existencias y valorización');
    }

    public function rotacion(): void
    {
        [$desde, $hasta] = $this->rango(29);
        $this->render('reportes/rotacion', [
            'desde' => $desde, 'hasta' => $hasta, 'filas' => Reporte::rotacion($desde, $hasta, 100),
        ], 'Rotación de productos');
    }

    public function alertas(): void
    {
        $this->render('reportes/alertas', ['alertas' => Producto::alertas()], 'Alertas de stock');
    }

    /** Exporta los movimientos a CSV (se abre en Excel). */
    public function exportar(): void
    {
        [$desde] = $this->rango(30);
        $filas = Reporte::movimientosDesde($desde);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="movimientos_desde_' . $desde . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel muestre bien las tildes
        fputcsv($out, ['N.°', 'Fecha', 'Tipo', 'E/S', 'Código', 'Producto', 'Color', 'Talla', 'Ubicación',
                       'Cantidad', 'Saldo', 'Precio unit.', 'Costo unit.', 'Motivo', 'Usuario', 'Operación'], ';');
        foreach ($filas as $f) {
            $f['tipo'] = etiqueta_movimiento($f['tipo']);
            fputcsv($out, array_values($f), ';');
        }
        fclose($out);
        exit;
    }

    private function rango(int $diasAtras): array
    {
        $valida = function (string $v, string $def): string {
            $d = DateTime::createFromFormat('Y-m-d', $v);
            return $d && $d->format('Y-m-d') === $v ? $v : $def;
        };
        $desde = $valida((string) ($_GET['desde'] ?? ''), date('Y-m-d', strtotime("-$diasAtras days")));
        $hasta = $valida((string) ($_GET['hasta'] ?? ''), date('Y-m-d'));
        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }
        if ((strtotime($hasta) - strtotime($desde)) / 86400 > 366) {
            $desde = date('Y-m-d', strtotime("$hasta -366 days"));
        }
        return [$desde, $hasta];
    }
}
