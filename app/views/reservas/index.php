<?php $estados = ['ACTIVA' => 'Activos', 'COMPLETADA' => 'Completados', 'ANULADA' => 'Anulados']; ?>
<div class="page-head">
    <div>
        <h1>Apartados y encargos</h1>
        <p>Los apartados separan un par del stock disponible hasta que el cliente lo recoja.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-accent" href="<?= url('reservas/crear') ?>"><i class="bi bi-bookmark-plus"></i> Nuevo apartado</a>
        <a class="btn btn-outline-primary" href="<?= url('reservas/crear', ['tipo' => 'ENCARGO']) ?>">Nuevo encargo</a>
    </div>
</div>
<ul class="nav nav-pills mb-3">
    <?php foreach ($estados as $k => $t): ?>
        <li class="nav-item"><a class="nav-link <?= $estado === $k ? 'active' : '' ?>" href="<?= url('reservas', ['estado' => $k]) ?>"><?= $t ?></a></li>
    <?php endforeach; ?>
</ul>

<?php if (!$reservas): ?>
    <div class="card"><div class="vacio"><i class="bi bi-bookmark"></i>No hay registros en esta lista.</div></div>
<?php else: ?>
<div class="row g-3">
<?php foreach ($reservas as $r):
    $total = $r['precio'] * $r['cantidad'];
    $vencida = $r['estado'] === 'ACTIVA' && $r['fecha_limite'] && $r['fecha_limite'] < date('Y-m-d'); ?>
    <div class="col-md-6 col-xxl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge text-bg-<?= $r['tipo'] === 'APARTADO' ? 'primary' : 'secondary' ?>"><?= $r['tipo'] === 'APARTADO' ? 'Apartado' : 'Encargo' ?> N.° <?= $r['id'] ?></span>
                    <?php if ($vencida): ?><span class="stock-pill cero">Plazo vencido</span>
                    <?php elseif ($r['fecha_limite'] && $r['estado'] === 'ACTIVA'): ?><span class="small text-muted-2">Hasta el <?= fecha($r['fecha_limite'], false) ?></span><?php endif; ?>
                </div>
                <div class="fw-semibold"><?= e($r['producto'] ? $r['producto'] : $r['descripcion']) ?></div>
                <?php if ($r['producto']): ?>
                    <div class="small text-muted-2"><?= e($r['color']) ?> · talla <?= e($r['talla']) ?> · <?= e($r['codigo']) ?><?= $r['ubicacion'] ? ' · ' . e($r['ubicacion']) : '' ?></div>
                <?php elseif ($r['tipo'] === 'ENCARGO' && $r['estado'] === 'ACTIVA'): ?>
                    <div class="small text-warning-emphasis">Pendiente de llegada</div>
                <?php endif; ?>
                <?php if ($r['estado'] === 'ACTIVA'): ?>
                    <div class="mt-2"><i class="bi bi-person"></i> <?= e($r['cliente_nombre']) ?> · <a href="tel:<?= e(preg_replace('/\s+/', '', $r['cliente_telefono'])) ?>"><?= e($r['cliente_telefono']) ?></a></div>
                <?php endif; ?>
                <div class="d-flex justify-content-between mt-2 small">
                    <span>Total: <strong><?= soles($total) ?></strong> (<?= (int) $r['cantidad'] ?> u.)</span>
                    <span>Adelanto: <?= soles($r['adelanto']) ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted-2">Registró <?= e($r['usuario']) ?> · <?= fecha($r['fecha']) ?></span>
                    <span class="fw-bold">Saldo: <?= soles($total - $r['adelanto']) ?></span>
                </div>
                <?php if ($r['estado'] !== 'ACTIVA'): ?>
                    <div class="small text-muted-2 mt-1"><?= $r['estado'] === 'COMPLETADA' ? 'Completado' : 'Anulado' ?> el <?= fecha($r['cerrado_en']) ?>
                        <?php if ($r['operacion_id']): ?> · <a href="<?= url('ventas/ver', ['id' => $r['operacion_id']]) ?>">ver venta</a><?php endif; ?></div>
                <?php endif; ?>
            </div>
            <?php if ($r['estado'] === 'ACTIVA'): ?>
            <div class="card-footer bg-white">
                <?php if ($r['tipo'] === 'ENCARGO' && !$r['variante_id']): ?>
                    <form method="post" action="<?= url('reservas/asignar') ?>" class="mb-2">
                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <label class="form-label small mb-1">¿Ya llegó? Elija el artículo del catálogo:</label>
                        <div class="picker mb-2" data-picker data-name="variante_id" data-required></div>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" name="ubicacion_id" aria-label="Ubicación">
                                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['nombre']) ?></option><?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-outline-primary text-nowrap">Asignar</button>
                        </div>
                    </form>
                <?php endif; ?>
                <div class="d-flex gap-2">
                    <?php if ($r['variante_id']): ?>
                    <form class="flex-fill" method="post" action="<?= url('reservas/completar') ?>"
                          data-confirmar="¿El cliente recogió y pagó el saldo de <?= soles($total - $r['adelanto']) ?>? Se registrará la venta.">
                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button class="btn btn-sm btn-accent w-100"><i class="bi bi-check2"></i> Entregar y cobrar saldo</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" action="<?= url('reservas/anular') ?>" data-confirmar="¿Anular esta reserva? El par vuelve a estar disponible.">
                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger">Anular</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
