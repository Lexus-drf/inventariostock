<?php $admin = Auth::esAdmin(); ?>
<div class="page-head">
    <div>
        <h1>Kardex · <?= e($v['nombre']) ?></h1>
        <p><?= e($v['color']) ?> · talla <?= e($v['talla']) ?> · <?= e($v['codigo']) ?> · stock actual: <strong><?= array_sum(array_column($v['stock'], 'cantidad')) ?></strong></p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= url('productos/ver', ['id' => $v['producto_id']]) ?>"><i class="bi bi-arrow-left"></i> Volver al producto</a>
</div>
<div class="alert alert-light border small"><i class="bi bi-shield-check"></i> Cada movimiento guarda quién lo hizo y cuándo, y no se puede borrar ni modificar. Las correcciones se registran como ajustes.</div>
<div class="card">
    <?php if (!$movs): ?>
        <div class="vacio"><i class="bi bi-journal"></i>Sin movimientos registrados.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>Fecha y hora</th><th>Movimiento</th><th>Ubicación</th><th class="num">Entrada</th><th class="num">Salida</th><th class="num">Saldo</th>
                <th class="num">Precio</th><?php if ($admin): ?><th class="num">Costo</th><?php endif; ?><th>Usuario</th><th>Detalle</th></tr></thead>
            <tbody>
            <?php foreach ($movs as $m): ?>
                <tr>
                    <td class="text-nowrap"><a href="<?= url('ventas/ver', ['id' => $m['operacion_id']]) ?>" class="text-decoration-none"><?= fecha($m['fecha']) ?></a></td>
                    <td><?= e(etiqueta_movimiento($m['tipo'])) ?></td>
                    <td><?= e($m['ubicacion']) ?></td>
                    <td class="num entrada"><?= $m['sentido'] === 'E' ? '+' . (int) $m['cantidad'] : '' ?></td>
                    <td class="num salida"><?= $m['sentido'] === 'S' ? '−' . (int) $m['cantidad'] : '' ?></td>
                    <td class="num fw-semibold"><?= (int) $m['saldo'] ?></td>
                    <td class="num"><?= $m['precio_unitario'] !== null ? soles($m['precio_unitario']) : '' ?></td>
                    <?php if ($admin): ?><td class="num"><?= $m['costo_unitario'] !== null ? soles($m['costo_unitario']) : '' ?></td><?php endif; ?>
                    <td><?= e($m['usuario']) ?></td>
                    <td class="small text-muted-2"><?= e($m['motivo'] ?: $m['nota']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
