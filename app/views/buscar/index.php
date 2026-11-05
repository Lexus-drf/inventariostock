<div class="page-head">
    <div>
        <h1>Buscar productos</h1>
        <p>Toque una talla para agregarla a la venta.</p>
    </div>
</div>

<form class="card card-body mb-3" method="get" action="index.php">
    <input type="hidden" name="r" value="buscar">
    <div class="row g-2">
        <div class="col-12 col-lg-5">
            <input class="form-control form-control-lg" type="search" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Modelo, marca, color, talla o código" autofocus>
        </div>
        <div class="col-6 col-lg-2">
            <select class="form-select form-select-lg" name="categoria" aria-label="Categoría">
                <option value="">Categoría</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filtros['categoria'] == $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <select class="form-select form-select-lg" name="marca" aria-label="Marca">
                <option value="">Marca</option>
                <?php foreach ($marcas as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $filtros['marca'] == $m['id'] ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-4 col-lg-1">
            <input class="form-control form-control-lg" name="talla" value="<?= e($filtros['talla']) ?>" placeholder="Talla" inputmode="numeric">
        </div>
        <div class="col-8 col-lg-2 d-flex gap-2">
            <button class="btn btn-primary btn-lg flex-fill"><i class="bi bi-search"></i> Buscar</button>
        </div>
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="conStock" name="con_stock" value="1" <?= $filtros['con_stock'] ? 'checked' : '' ?> onchange="this.form.submit()">
                <label class="form-check-label" for="conStock">Mostrar solo lo que tiene stock disponible</label>
            </div>
        </div>
    </div>
</form>

<?php if (!$hayFiltro): ?>
    <div class="card"><div class="vacio"><i class="bi bi-search"></i>Escriba lo que busca el cliente o elija una categoría.</div></div>
<?php elseif (!$productos): ?>
    <div class="card"><div class="vacio"><i class="bi bi-emoji-frown"></i>No se encontraron productos con esa búsqueda.</div></div>
<?php else: ?>
    <p class="text-muted-2 small mb-2"><?= count($productos) ?> modelo(s) encontrado(s)</p>
    <div class="row g-3">
    <?php foreach ($productos as $p):
        $foto = foto_producto($p['foto']);
        // Si el resultado es de un solo color y ese color tiene foto, se muestra esa.
        $coloresHallados = array_unique(array_column($p['variantes'], 'color'));
        if (count($coloresHallados) === 1 && ($fc = foto_producto($p['fotos_color'][reset($coloresHallados)] ?? null))) {
            $foto = $fc;
        } ?>
        <div class="col-12 col-md-6 col-xxl-4">
            <article class="producto-card">
                <div class="producto-top">
                    <?php if ($foto): ?>
                        <img class="foto" src="<?= e($foto) ?>" alt="<?= e($p['nombre']) ?>" loading="lazy" data-principal="<?= e($foto) ?>">
                    <?php else: ?>
                        <div class="foto-vacia"><i class="bi bi-image"></i></div>
                    <?php endif; ?>
                    <div class="flex-fill">
                        <a class="producto-nombre" href="<?= url('productos/ver', ['id' => $p['id']]) ?>"><?= e($p['nombre']) ?></a>
                        <div class="producto-meta"><?= e($p['marca']) ?> · <?= e($p['categoria']) ?></div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span class="precio"><?= soles($p['precio_venta']) ?></span>
                            <span class="stock-pill <?= $p['disponible_total'] > 0 ? 'ok' : 'cero' ?>"><?= (int) $p['disponible_total'] ?> disp.</span>
                        </div>
                    </div>
                </div>
                <div class="variantes">
                    <?php
                    $porColor = [];
                    foreach ($p['variantes'] as $v) { $porColor[$v['color']][] = $v; }
                    foreach ($porColor as $color => $vs): ?>
                        <?php $fc = foto_producto($p['fotos_color'][$color] ?? null); ?>
                        <div class="color-grupo">
                            <div class="color-nombre">
                                <?php if ($fc): ?><img class="foto-color" src="<?= e($fc) ?>" alt="" loading="lazy" data-ver-foto="<?= e($fc) ?>" title="Ver foto del color <?= e($color) ?>"><?php endif; ?>
                                <?= e($color) ?>
                            </div>
                            <div class="tallas">
                            <?php foreach ($vs as $v):
                                $disp = (int) $v['disponible_total'];
                                $cls = $disp <= 0 ? 'agotado' : ($disp <= (int) $v['stock_minimo'] ? 'bajo' : 'ok');
                                $ubic = [];
                                foreach ($ubicaciones as $ub) {
                                    $s = $v['stock'][$ub['id']] ?? ['disponible' => 0, 'reservado' => 0];
                                    $ubic[] = ['id' => (int) $ub['id'], 'nombre' => $ub['nombre'], 'disponible' => (int) $s['disponible'], 'reservado' => (int) $s['reservado']];
                                }
                                $donde = implode(' · ', array_map(fn($x) => "{$x['nombre']}: {$x['disponible']}", $ubic)); ?>
                                <button type="button" class="talla <?= $cls ?>" <?= $disp <= 0 ? 'disabled' : '' ?>
                                        title="<?= e($donde) ?>"
                                        data-agregar='<?= e(json_encode([
                                            'variante_id' => (int) $v['variante_id'],
                                            'nombre' => $p['nombre'], 'color' => $v['color'], 'talla' => $v['talla'],
                                            'codigo' => $v['codigo'], 'precio' => (float) $p['precio_venta'], 'ubicaciones' => $ubic,
                                        ], JSON_UNESCAPED_UNICODE)) ?>'>
                                    <b><?= e($v['talla']) ?></b>
                                    <span><?= $disp <= 0 ? 'agotado' : $donde ?></span>
                                </button>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require APP_PATH . '/views/ventas/_modal_agregar.php'; ?>
