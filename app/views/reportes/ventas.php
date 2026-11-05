<div class="page-head">
    <div><h1>Reportes</h1><p>Ventas por día y por semana. Incluye la diferencia cobrada o devuelta en los cambios.</p></div>
    <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
</div>
<?php require __DIR__ . '/_tabs.php'; ?>

<form class="card card-body mb-3 no-print" method="get" action="index.php">
    <input type="hidden" name="r" value="reportes">
    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-4"><label class="form-label small">Desde</label><input class="form-control" type="date" name="desde" value="<?= e($desde) ?>"></div>
        <div class="col-6 col-md-4"><label class="form-label small">Hasta</label><input class="form-control" type="date" name="hasta" value="<?= e($hasta) ?>"></div>
        <div class="col-md-4"><button class="btn btn-primary w-100">Ver periodo</button></div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card kpi"><i class="bi bi-cash-coin"></i><div class="valor"><?= soles($totales['importe']) ?></div><div class="etiqueta">Importe del periodo</div></div></div>
    <div class="col-6 col-md-4"><div class="card kpi"><i class="bi bi-receipt"></i><div class="valor"><?= $totales['ventas'] ?></div><div class="etiqueta">Ventas registradas</div></div></div>
    <div class="col-6 col-md-4"><div class="card kpi"><i class="bi bi-box2"></i><div class="valor"><?= $totales['pares'] ?></div><div class="etiqueta">Artículos vendidos</div></div></div>
</div>

<div class="card mb-3">
    <div class="card-header">Ventas por día · <?= fecha($desde, false) ?> al <?= fecha($hasta, false) ?></div>
    <div class="card-body"><div class="chart-box"><canvas id="graficoDias"></canvas></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Detalle por día</div>
            <div class="table-responsive" style="max-height:420px">
                <table class="table table-sm">
                    <thead><tr><th>Día</th><th class="num">Ventas</th><th class="num">Artículos</th><th class="num">Importe</th></tr></thead>
                    <tbody>
                    <?php foreach (array_reverse($dias) as $d): ?>
                        <tr><td><?= fecha($d['dia'], false) ?></td><td class="num"><?= $d['ventas'] ?></td><td class="num"><?= $d['pares'] ?></td><td class="num"><?= soles($d['importe']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Ventas por semana (últimas 8)</div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Semana</th><th class="num">Ventas</th><th class="num">Artículos</th><th class="num">Importe</th></tr></thead>
                    <tbody>
                    <?php foreach (array_reverse($semanas) as $s): ?>
                        <tr><td><?= date('d/m', strtotime($s['semana'])) ?> – <?= date('d/m', strtotime($s['hasta'])) ?></td>
                            <td class="num"><?= $s['ventas'] ?></td><td class="num"><?= $s['pares'] ?></td><td class="num"><?= soles($s['importe']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('vendor/chartjs/chart.umd.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const d = <?= json_encode($dias) ?>;
    graficoVentas('graficoDias', d.map(x => x.dia), d.map(x => x.importe), d.map(x => x.pares));
});
</script>
