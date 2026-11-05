<?php
$admin = Auth::esAdmin();
$tipos = ['VENTA' => 'Ventas', 'CAMBIO' => 'Cambios', 'INGRESO' => 'Ingresos', 'AJUSTE' => 'Ajustes', 'TRASLADO' => 'Traslados'];
$colores = ['VENTA' => 'success', 'CAMBIO' => 'info', 'INGRESO' => 'primary', 'AJUSTE' => 'warning', 'TRASLADO' => 'secondary'];
?>
<div class="page-head">
    <div><h1>Historial de operaciones</h1><p>Todo lo registrado en el sistema, con usuario, fecha y hora.</p></div>
    <?php if ($admin): ?>
        <a class="btn btn-outline-primary" href="<?= url('reportes/exportar', ['desde' => $f['desde']]) ?>"><i class="bi bi-filetype-csv"></i> Exportar movimientos</a>
    <?php endif; ?>
</div>
<form class="card card-body mb-3" method="get" action="index.php">
    <input type="hidden" name="r" value="inventario/historial">
    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-3"><label class="form-label small">Desde</label><input class="form-control" type="date" name="desde" value="<?= e($f['desde']) ?>"></div>
        <div class="col-6 col-md-3"><label class="form-label small">Hasta</label><input class="form-control" type="date" name="hasta" value="<?= e($f['hasta']) ?>"></div>
        <div class="col-6 col-md-2"><label class="form-label small">Tipo</label>
            <select class="form-select" name="tipo"><option value="">Todos</option>
                <?php foreach ($tipos as $k => $t): ?><option value="<?= $k ?>" <?= $f['tipo'] === $k ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-6 col-md-2"><label class="form-label small">Usuario</label>
            <select class="form-select" name="usuario_id"><option value="">Todos</option>
                <?php foreach ($usuarios as $u): ?><option value="<?= $u['id'] ?>" <?= $f['usuario_id'] == $u['id'] ? 'selected' : '' ?>><?= e($u['nombre']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filtrar</button></div>
    </div>
</form>
<div class="card">
    <?php if (!$ops): ?>
        <div class="vacio"><i class="bi bi-clock-history"></i>No hay operaciones en ese periodo.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>N.°</th><th>Fecha y hora</th><th>Tipo</th><th>Detalle</th><th>Usuario</th><th class="num">Importe</th></tr></thead>
            <tbody>
            <?php foreach ($ops as $o): ?>
                <tr>
                    <td><a href="<?= url('ventas/ver', ['id' => $o['id']]) ?>"><?= (int) $o['id'] ?></a></td>
                    <td class="text-nowrap"><?= fecha($o['fecha']) ?></td>
                    <td><span class="badge text-bg-<?= $colores[$o['tipo']] ?>"><?= e(rtrim($tipos[$o['tipo']], 's')) ?></span></td>
                    <td class="small">
                        <?php foreach (array_slice($o['detalle'], 0, 3) as $d): ?>
                            <div><span class="<?= $d['sentido'] === 'E' ? 'entrada' : 'salida' ?>"><?= $d['sentido'] === 'E' ? '+' : '−' ?><?= (int) $d['cantidad'] ?></span>
                                <?= e($d['nombre']) ?> <?= e($d['color']) ?> <?= e(talla_corta($d['talla'])) ?> <span class="text-muted-2">(<?= e($d['ubicacion']) ?>)</span></div>
                        <?php endforeach; ?>
                        <?php if (count($o['detalle']) > 3): ?><div class="text-muted-2">y <?= count($o['detalle']) - 3 ?> más…</div><?php endif; ?>
                        <?php if ($o['nota']): ?><div class="text-muted-2 fst-italic"><?= e($o['nota']) ?></div><?php endif; ?>
                    </td>
                    <td><?= e($o['usuario']) ?></td>
                    <td class="num"><?= in_array($o['tipo'], ['VENTA', 'CAMBIO']) ? soles($o['total']) : '' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
