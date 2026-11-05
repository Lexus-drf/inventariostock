<?php
$tot = ['modelos' => 0, 'unidades' => 0, 'valor_costo' => 0, 'valor_venta' => 0];
foreach ($categorias as $c) { foreach ($tot as $k => $_) { $tot[$k] += $c[$k]; } }
?>
<div class="page-head">
    <div><h1>Reportes</h1><p>Unidades en stock y su valor a precio de costo y de venta.</p></div>
    <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
</div>
<?php require __DIR__ . '/_tabs.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card kpi"><i class="bi bi-box2"></i><div class="valor"><?= (int) $tot['unidades'] ?></div><div class="etiqueta">Unidades en stock</div></div></div>
    <div class="col-6 col-md-3"><div class="card kpi"><i class="bi bi-tags"></i><div class="valor"><?= (int) $tot['modelos'] ?></div><div class="etiqueta">Modelos activos</div></div></div>
    <div class="col-6 col-md-3"><div class="card kpi"><i class="bi bi-wallet2"></i><div class="valor"><?= soles($tot['valor_costo']) ?></div><div class="etiqueta">Valor a costo</div></div></div>
    <div class="col-6 col-md-3"><div class="card kpi"><i class="bi bi-graph-up-arrow"></i><div class="valor"><?= soles($tot['valor_venta']) ?></div><div class="etiqueta">Valor a precio de venta</div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Por categoría</div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Categoría</th><th class="num">Modelos</th><th class="num">Unidades</th><th class="num">Valor a costo</th><th class="num">Valor a venta</th></tr></thead>
                    <tbody>
                    <?php foreach ($categorias as $c): ?>
                        <tr><td><?= e($c['categoria']) ?></td><td class="num"><?= (int) $c['modelos'] ?></td><td class="num"><?= (int) $c['unidades'] ?></td>
                            <td class="num"><?= soles($c['valor_costo']) ?></td><td class="num"><?= soles($c['valor_venta']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr class="fw-bold"><td>Total</td><td class="num"><?= (int) $tot['modelos'] ?></td><td class="num"><?= (int) $tot['unidades'] ?></td><td class="num"><?= soles($tot['valor_costo']) ?></td><td class="num"><?= soles($tot['valor_venta']) ?></td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Por ubicación</div>
            <ul class="list-group list-group-flush">
            <?php foreach ($ubicaciones as $u): ?>
                <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-geo-alt"></i> <?= e($u['ubicacion']) ?></span>
                    <span class="text-end"><strong><?= (int) $u['unidades'] ?></strong> u.<div class="small text-muted-2"><?= soles($u['valor_costo']) ?> a costo</div></span></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
