<?php $admin = Auth::esAdmin(); $foto = foto_producto($p['foto']); ?>
<div class="page-head">
    <div>
        <h1><?= e($p['nombre']) ?> <?php if (!$p['activo']): ?><span class="badge text-bg-secondary align-middle fs-6">Inactivo</span><?php endif; ?></h1>
        <p><?= e($p['marca']) ?> · <?= e($p['categoria']) ?></p>
    </div>
    <?php if ($admin): ?>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="<?= url('productos/editar', ['id' => $p['id']]) ?>"><i class="bi bi-pencil"></i> Editar</a>
        <a class="btn btn-primary" href="<?= url('inventario/ingreso', ['producto' => $p['id']]) ?>"><i class="bi bi-box-arrow-in-down"></i> Registrar ingreso</a>
    </div>
    <?php endif; ?>
</div>

<div class="row g-3">
    <div class="col-md-4 col-xl-3">
        <div class="card card-body text-center">
            <?php if ($foto): ?><img class="foto foto-lg mx-auto" id="fotoPrincipal" src="<?= e($foto) ?>" alt="<?= e($p['nombre']) ?>">
            <?php else: ?><div class="foto-vacia foto-lg mx-auto"><i class="bi bi-image"></i></div><?php endif; ?>
            <?php $miniaturas = array_filter(array_map('foto_producto', $p['fotos_color'])); ?>
            <?php if ($foto && $miniaturas): ?>
            <div class="miniaturas mt-2" role="group" aria-label="Fotos por color">
                <button type="button" class="miniatura activa" data-src="<?= e($foto) ?>" title="Foto principal"><img src="<?= e($foto) ?>" alt="Principal"></button>
                <?php foreach ($miniaturas as $color => $src): ?>
                    <button type="button" class="miniatura" data-src="<?= e($src) ?>" title="<?= e($color) ?>"><img src="<?= e($src) ?>" alt="<?= e($color) ?>"><span><?= e($color) ?></span></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="precio fs-3 mt-3"><?= soles($p['precio_venta']) ?></div>
            <?php if ($admin): ?>
                <?php if ($p['costo'] > 0): ?>
                <div class="small text-muted-2">Costo: <?= soles($p['costo']) ?> · Margen: <?= $p['precio_venta'] > 0 ? number_format(100 * ($p['precio_venta'] - $p['costo']) / $p['precio_venta'], 0) : 0 ?> %</div>
                <?php else: ?>
                <div class="small text-warning-emphasis">Costo sin registrar</div>
                <?php endif; ?>
                <?php if ($p['precio_venta'] <= 0): ?><div class="small text-danger fw-semibold">Falta el precio de venta</div><?php endif; ?>
            <?php endif; ?>
            <?php if ($p['descripcion']): ?><p class="small mt-2 mb-0"><?= e($p['descripcion']) ?></p><?php endif; ?>
            <?php if ($admin): ?>
            <form class="mt-3" method="post" action="<?= url('productos/activar') ?>" data-confirmar="<?= $p['activo'] ? '¿Desactivar el producto? Dejará de aparecer en las búsquedas; su historial se conserva.' : '¿Activar el producto?' ?>">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button class="btn btn-sm btn-link text-<?= $p['activo'] ? 'danger' : 'success' ?>"><?= $p['activo'] ? 'Desactivar producto' : 'Activar producto' ?></button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-8 col-xl-9">
        <div class="card">
            <div class="card-header">Variantes y existencias</div>
            <?php if (!$p['variantes']): ?>
                <div class="vacio"><i class="bi bi-grid-3x3-gap"></i>Este producto aún no tiene variantes.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr>
                        <th>Color</th><th>Talla</th><th>Código</th>
                        <?php foreach ($ubicaciones as $ub): ?><th class="num"><?= e($ub['nombre']) ?></th><?php endforeach; ?>
                        <th class="num">Apartado</th><th class="num">Disponible</th><th class="num">Mín.</th><th></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($p['variantes'] as $v):
                        $res = array_sum(array_column($v['stock'], 'reservado'));
                        $ubic = [];
                        foreach ($ubicaciones as $ub) {
                            $s = $v['stock'][$ub['id']] ?? ['disponible' => 0, 'reservado' => 0];
                            $ubic[] = ['id' => (int) $ub['id'], 'nombre' => $ub['nombre'], 'disponible' => (int) $s['disponible'], 'reservado' => (int) $s['reservado']];
                        } ?>
                        <tr class="<?= $v['activo'] ? '' : 'text-muted-2' ?>">
                            <td class="text-nowrap"><?php if ($fc = foto_producto($p['fotos_color'][$v['color']] ?? null)): ?><img class="foto-color" src="<?= e($fc) ?>" alt=""><?php endif; ?><?= e($v['color']) ?></td>
                            <td class="fw-semibold"><?= e($v['talla']) ?></td>
                            <td class="small text-nowrap"><?= e($v['codigo']) ?><?= $v['activo'] ? '' : ' · inactiva' ?></td>
                            <?php foreach ($ubicaciones as $ub): ?><td class="num"><?= (int) ($v['stock'][$ub['id']]['cantidad'] ?? 0) ?></td><?php endforeach; ?>
                            <td class="num"><?= $res ?: '—' ?></td>
                            <td class="num"><span class="stock-pill <?= clase_stock((int) $v['disponible_total'], (int) $v['stock_minimo']) ?>"><?= (int) $v['disponible_total'] ?></span></td>
                            <td class="num">
                                <?php if ($admin): ?>
                                <form method="post" action="<?= url('productos/variante') ?>" class="d-inline-flex">
                                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $v['id'] ?>">
                                    <input class="form-control form-control-sm text-end" style="width:64px" type="number" min="0" name="stock_minimo" value="<?= (int) $v['stock_minimo'] ?>" onchange="this.form.submit()" aria-label="Stock mínimo">
                                </form>
                                <?php else: ?><?= (int) $v['stock_minimo'] ?><?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <?php if ($v['activo'] && $p['activo'] && $v['disponible_total'] > 0): ?>
                                <button type="button" class="btn btn-sm btn-accent" data-agregar='<?= e(json_encode([
                                    'variante_id' => (int) $v['id'], 'nombre' => $p['nombre'], 'color' => $v['color'], 'talla' => $v['talla'],
                                    'codigo' => $v['codigo'], 'precio' => (float) $p['precio_venta'], 'ubicaciones' => $ubic], JSON_UNESCAPED_UNICODE)) ?>'>Vender</button>
                                <?php endif; ?>
                                <a class="btn btn-sm btn-outline-secondary" href="<?= url('productos/kardex', ['id' => $v['id']]) ?>" title="Historial de movimientos">Kardex</a>
                                <?php if ($admin): ?>
                                <form class="d-inline" method="post" action="<?= url('productos/variante') ?>">
                                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $v['id'] ?>"><input type="hidden" name="alternar" value="1">
                                    <button class="btn btn-sm btn-link text-muted-2 px-1" title="<?= $v['activo'] ? 'Desactivar variante' : 'Activar variante' ?>"><i class="bi bi-<?= $v['activo'] ? 'eye-slash' : 'eye' ?>"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($admin && $p['colores']): ?>
        <div class="card mt-3">
            <div class="card-header">Fotos por color</div>
            <div class="card-body">
                <p class="small text-muted-2 mb-3">Suba una foto de cada color para que el vendedor reconozca el modelo exacto. Si un color no tiene foto, se muestra la foto principal.</p>
                <div class="fotos-color">
                <?php foreach ($p['colores'] as $color): $fc = foto_producto($p['fotos_color'][$color] ?? null); ?>
                    <div class="foto-color-item">
                        <?php if ($fc): ?><img class="foto" src="<?= e($fc) ?>" alt="<?= e($color) ?>">
                        <?php else: ?><div class="foto-vacia"><i class="bi bi-camera"></i></div><?php endif; ?>
                        <div class="fw-semibold small mt-1"><?= e($color) ?></div>
                        <form method="post" action="<?= url('productos/fotoColor') ?>" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="producto_id" value="<?= $p['id'] ?>"><input type="hidden" name="color" value="<?= e($color) ?>">
                            <label class="btn btn-sm btn-outline-primary w-100 mt-1">
                                <i class="bi bi-upload"></i> <?= $fc ? 'Cambiar' : 'Subir foto' ?>
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="d-none" onchange="this.form.submit()">
                            </label>
                        </form>
                        <?php if ($fc): ?>
                        <form method="post" action="<?= url('productos/quitarFotoColor') ?>" data-confirmar="¿Quitar la foto del color <?= e($color) ?>?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="producto_id" value="<?= $p['id'] ?>"><input type="hidden" name="color" value="<?= e($color) ?>">
                            <button class="btn btn-sm btn-link text-danger w-100 p-0 mt-1">Quitar</button>
                        </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($admin): ?>
        <form class="card card-body mt-3" method="post" action="<?= url('productos/variantes') ?>">
            <?= csrf_field() ?><input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
            <h2 class="h6">Agregar colores o tallas</h2>
            <div class="row g-2 align-items-end">
                <div class="col-sm-5"><label class="form-label small">Colores</label><input class="form-control" name="colores" placeholder="Rojo, Verde" required></div>
                <div class="col-sm-3"><label class="form-label small">Tallas</label><input class="form-control" name="tallas" placeholder="38-43" required></div>
                <div class="col-sm-2"><label class="form-label small">Mínimo</label><input class="form-control" type="number" min="0" name="stock_minimo" value="1"></div>
                <div class="col-sm-2"><button class="btn btn-outline-primary w-100">Agregar</button></div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require APP_PATH . '/views/ventas/_modal_agregar.php'; ?>
