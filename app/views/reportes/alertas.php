<div class="page-head">
    <div><h1>Alertas de stock mínimo</h1><p>Variantes que llegaron a su stock mínimo o se agotaron. Sirve como lista de compras para el próximo viaje.</p></div>
    <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir lista</button>
</div>
<?php require __DIR__ . '/_tabs.php'; ?>
<div class="card">
    <?php if (!$alertas): ?>
        <div class="vacio"><i class="bi bi-check2-circle"></i>No hay alertas: todo el stock está por encima del mínimo.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Modelo</th><th>Color</th><th>Talla</th><th>Código</th><th class="num">Stock</th><th class="num">Mínimo</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($alertas as $a): ?>
                <tr>
                    <td><a class="fw-semibold text-decoration-none" href="<?= url('productos/ver', ['id' => $a['producto_id']]) ?>"><?= e($a['nombre']) ?></a><div class="small text-muted-2"><?= e($a['marca']) ?></div></td>
                    <td><?= e($a['color']) ?></td>
                    <td class="fw-semibold"><?= e($a['talla']) ?></td>
                    <td class="small"><?= e($a['codigo']) ?></td>
                    <td class="num"><span class="stock-pill <?= $a['total'] == 0 ? 'cero' : 'bajo' ?>"><?= (int) $a['total'] ?></span></td>
                    <td class="num"><?= (int) $a['stock_minimo'] ?></td>
                    <td class="text-end no-print"><a class="btn btn-sm btn-outline-primary" href="<?= url('inventario/ingreso', ['producto' => $a['producto_id']]) ?>">Registrar ingreso</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
