<?php
$titulos = ['VENTA' => 'Venta', 'INGRESO' => 'Ingreso de mercadería', 'CAMBIO' => 'Cambio de talla o modelo',
            'AJUSTE' => 'Ajuste de stock', 'TRASLADO' => 'Traslado'];
$admin = Auth::esAdmin();
?>
<div class="page-head">
    <div>
        <h1><?= e($titulos[$op['tipo']] ?? $op['tipo']) ?> N.° <?= (int) $op['id'] ?></h1>
        <p><?= fecha($op['fecha']) ?> · registrado por <?= e($op['usuario']) ?></p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
        <a class="btn btn-primary" href="<?= url($op['tipo'] === 'VENTA' ? 'buscar' : 'inicio') ?>"><?= $op['tipo'] === 'VENTA' ? 'Nueva venta' : 'Volver al inicio' ?></a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Artículo</th><th>Ubicación</th><th>Movimiento</th><th class="num">Cant.</th>
                <?php if (in_array($op['tipo'], ['VENTA', 'CAMBIO'])): ?><th class="num">Precio</th><th class="num">Importe</th><?php endif; ?>
                <?php if ($op['tipo'] === 'INGRESO' && $admin): ?><th class="num">Costo</th><?php endif; ?>
                <th class="num">Saldo</th></tr></thead>
            <tbody>
            <?php foreach ($detalle as $d): ?>
                <tr>
                    <td><strong><?= e($d['nombre']) ?></strong><div class="small text-muted-2"><?= e($d['color']) ?> · talla <?= e($d['talla']) ?> · <?= e($d['codigo']) ?></div></td>
                    <td><?= e($d['ubicacion']) ?></td>
                    <td class="<?= $d['sentido'] === 'E' ? 'entrada' : 'salida' ?>"><?= e(etiqueta_movimiento($d['tipo'])) ?></td>
                    <td class="num <?= $d['sentido'] === 'E' ? 'entrada' : 'salida' ?>"><?= $d['sentido'] === 'E' ? '+' : '−' ?><?= (int) $d['cantidad'] ?></td>
                    <?php if (in_array($op['tipo'], ['VENTA', 'CAMBIO'])): ?>
                        <td class="num"><?= soles($d['precio_unitario']) ?></td>
                        <td class="num"><?= soles($d['precio_unitario'] * $d['cantidad']) ?></td>
                    <?php endif; ?>
                    <?php if ($op['tipo'] === 'INGRESO' && $admin): ?><td class="num"><?= $d['costo_unitario'] !== null ? soles($d['costo_unitario']) : '—' ?></td><?php endif; ?>
                    <td class="num"><?= (int) $d['saldo'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-body border-top d-flex flex-wrap justify-content-between gap-2">
        <div class="text-muted-2"><?= $op['nota'] ? 'Nota: ' . e($op['nota']) : '' ?></div>
        <?php if ($op['tipo'] === 'VENTA'): ?>
            <div class="fs-4 fw-bold">Total: <?= soles($op['total']) ?></div>
        <?php elseif ($op['tipo'] === 'CAMBIO'): ?>
            <div class="fs-5 fw-bold"><?= $op['total'] > 0 ? 'Cobrado al cliente: ' . soles($op['total']) : ($op['total'] < 0 ? 'Devuelto al cliente: ' . soles(-$op['total']) : 'Sin diferencia de precio') ?></div>
        <?php endif; ?>
    </div>
</div>
<p class="small text-muted-2 mt-2">Constancia interna de InventarioStock. No es un comprobante de pago.</p>
