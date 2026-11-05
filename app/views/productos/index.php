<div class="page-head">
    <div>
        <h1>Productos</h1>
        <p>Modelos registrados con sus variantes y existencias.</p>
    </div>
    <a class="btn btn-primary" href="<?= url('productos/crear') ?>"><i class="bi bi-plus-lg"></i> Nuevo producto</a>
</div>
<form class="card card-body mb-3" method="get" action="index.php">
    <input type="hidden" name="r" value="productos">
    <div class="row g-2 align-items-center">
        <div class="col-md-5"><input class="form-control" type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Modelo o marca"></div>
        <div class="col-md-3">
            <select class="form-select" name="categoria">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $c): ?><option value="<?= $c['id'] ?>" <?= $f['categoria'] == $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" id="inac" name="inactivos" value="1" <?= $f['inactivos'] ? 'checked' : '' ?>><label class="form-check-label" for="inac">Ver inactivos</label></div></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filtrar</button></div>
    </div>
</form>
<div class="card">
    <?php if (!$productos): ?>
        <div class="vacio"><i class="bi bi-box-seam"></i>No hay productos con ese filtro.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th></th><th>Modelo</th><th>Categoría</th><th class="num">Precio</th><th class="num">Costo</th><th class="num">Variantes</th><th class="num">Stock</th></tr></thead>
            <tbody>
            <?php foreach ($productos as $p): $foto = foto_producto($p['foto']); ?>
                <tr class="<?= $p['activo'] ? '' : 'text-muted-2' ?>" style="cursor:pointer" onclick="location.href='<?= url('productos/ver', ['id' => $p['id']]) ?>'">
                    <td style="width:56px"><?php if ($foto): ?><img class="foto" style="width:44px;height:44px" src="<?= e($foto) ?>" alt="" loading="lazy"><?php else: ?><div class="foto-vacia" style="width:44px;height:44px;font-size:1rem"><i class="bi bi-image"></i></div><?php endif; ?></td>
                    <td><a class="fw-semibold text-decoration-none" href="<?= url('productos/ver', ['id' => $p['id']]) ?>"><?= e($p['nombre']) ?></a><div class="small text-muted-2"><?= e($p['marca']) ?><?= $p['activo'] ? '' : ' · inactivo' ?></div></td>
                    <td><?= e($p['categoria']) ?></td>
                    <td class="num"><?= soles($p['precio_venta']) ?></td>
                    <td class="num text-muted-2"><?= soles($p['costo']) ?></td>
                    <td class="num"><?= (int) $p['n_variantes'] ?></td>
                    <td class="num"><span class="stock-pill <?= $p['stock'] > 0 ? 'ok' : 'cero' ?>"><?= (int) $p['stock'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
