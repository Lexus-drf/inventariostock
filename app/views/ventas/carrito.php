<div class="page-head">
    <div>
        <h1>Venta en curso</h1>
        <p>Revise los artículos y registre la venta. El stock se descuenta al confirmar.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= url('buscar') ?>"><i class="bi bi-plus-lg"></i> Agregar artículos</a>
</div>

<?php if (!$items): ?>
    <div class="card"><div class="vacio">
        <i class="bi bi-bag"></i>
        <p>La venta está vacía.</p>
        <a class="btn btn-accent" href="<?= url('buscar') ?>"><i class="bi bi-search"></i> Buscar productos</a>
    </div></div>
<?php else: ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <ul class="list-group list-group-flush">
            <?php foreach ($items as $clave => $it): $foto = foto_producto($it['foto']); ?>
                <li class="list-group-item py-3">
                    <div class="d-flex gap-3">
                        <?php if ($foto): ?><img class="foto" style="width:64px;height:64px" src="<?= e($foto) ?>" alt="">
                        <?php else: ?><div class="foto-vacia" style="width:64px;height:64px;font-size:1.4rem"><i class="bi bi-image"></i></div><?php endif; ?>
                        <div class="flex-fill">
                            <div class="fw-semibold"><?= e($it['nombre']) ?></div>
                            <div class="small text-muted-2"><?= e($it['color']) ?> · talla <?= e($it['talla']) ?> · <?= e($it['codigo']) ?> · sale de <strong><?= e($it['ubicacion']) ?></strong></div>
                            <?php if ($it['cantidad'] > $it['disponible']): ?>
                                <div class="small text-danger mt-1"><i class="bi bi-exclamation-circle"></i> Ahora solo hay <?= $it['disponible'] ?> disponible(s).</div>
                            <?php endif; ?>
                            <form class="row g-2 mt-1 align-items-end" method="post" action="<?= url('ventas/actualizar') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="clave" value="<?= e($clave) ?>">
                                <div class="col-4 col-sm-3">
                                    <label class="form-label small mb-0">Cant.</label>
                                    <input class="form-control" type="number" name="cantidad" min="0" max="<?= $it['disponible'] ?>" value="<?= (int) $it['cantidad'] ?>" onchange="this.form.submit()">
                                </div>
                                <div class="col-5 col-sm-4">
                                    <label class="form-label small mb-0">Precio S/</label>
                                    <input class="form-control" type="number" name="precio" min="0" step="0.10" value="<?= number_format($it['precio'], 2, '.', '') ?>" onchange="this.form.submit()">
                                </div>
                                <div class="col-3 col-sm-5 text-end fw-semibold num"><?= soles($it['cantidad'] * $it['precio']) ?></div>
                            </form>
                        </div>
                        <form method="post" action="<?= url('ventas/quitar') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="clave" value="<?= e($clave) ?>">
                            <button class="btn btn-sm btn-outline-danger" aria-label="Quitar"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <form class="mt-2" method="post" action="<?= url('ventas/vaciar') ?>" data-confirmar="¿Vaciar la venta en curso?">
            <?= csrf_field() ?>
            <button class="btn btn-link text-danger px-0"><i class="bi bi-x-circle"></i> Vaciar venta</button>
        </form>
    </div>
    <div class="col-lg-4">
        <form class="card card-body" method="post" action="<?= url('ventas/confirmar') ?>">
            <?= csrf_field() ?>
            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <span class="card-title-sm">Total</span>
                <span class="fs-3 fw-bold num"><?= soles($total) ?></span>
            </div>
            <div class="small text-muted-2 mb-3"><?= array_sum(array_column($items, 'cantidad')) ?> artículo(s)</div>
            <label class="form-label" for="nota">Nota (opcional)</label>
            <input class="form-control mb-3" id="nota" name="nota" maxlength="255" placeholder="Ej.: pagó con Yape">
            <button class="btn btn-accent btn-lg w-100"><i class="bi bi-check2-circle"></i> Registrar venta</button>
        </form>
    </div>
</div>
<?php endif; ?>
