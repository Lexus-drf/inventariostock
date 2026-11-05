<div class="page-head">
    <div><h1>Reportes</h1><p>Qué modelos se venden más y cuáles no se mueven, para decidir qué comprar en el próximo viaje.</p></div>
    <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
</div>
<?php require __DIR__ . '/_tabs.php'; ?>
<form class="card card-body mb-3 no-print" method="get" action="index.php">
    <input type="hidden" name="r" value="reportes/rotacion">
    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-4"><label class="form-label small">Desde</label><input class="form-control" type="date" name="desde" value="<?= e($desde) ?>"></div>
        <div class="col-6 col-md-4"><label class="form-label small">Hasta</label><input class="form-control" type="date" name="hasta" value="<?= e($hasta) ?>"></div>
        <div class="col-md-4"><button class="btn btn-primary w-100">Ver periodo</button></div>
    </div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>#</th><th>Modelo</th><th>Categoría</th><th class="num">Vendidas</th><th class="num">Importe</th><th class="num">Stock actual</th><th>Lectura</th></tr></thead>
            <tbody>
            <?php foreach ($filas as $i => $f):
                $lectura = $f['vendidas'] == 0 ? ['Sin ventas en el periodo', 'cero']
                    : ($f['stock'] <= $f['vendidas'] / 2 ? ['Alta rotación: reponer', 'bajo'] : ['Rotación normal', 'ok']); ?>
                <tr>
                    <td class="text-muted-2"><?= $i + 1 ?></td>
                    <td><a class="fw-semibold text-decoration-none" href="<?= url('productos/ver', ['id' => $f['id']]) ?>"><?= e($f['nombre']) ?></a><div class="small text-muted-2"><?= e($f['marca']) ?></div></td>
                    <td><?= e($f['categoria']) ?></td>
                    <td class="num fw-semibold"><?= (int) $f['vendidas'] ?></td>
                    <td class="num"><?= soles($f['importe']) ?></td>
                    <td class="num"><?= (int) $f['stock'] ?></td>
                    <td><span class="stock-pill <?= $lectura[1] ?>"><?= $lectura[0] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
