<?php $nuevo = !$p; $foto = $p ? foto_producto($p['foto']) : null; ?>
<div class="page-head">
    <div>
        <h1><?= $nuevo ? 'Nuevo producto' : 'Editar ' . e($p['nombre']) ?></h1>
        <p><?= $nuevo ? 'Registre el modelo y genere sus variantes por color y talla.' : 'Datos generales del modelo.' ?></p>
    </div>
</div>
<?php if (!$categorias || !$marcas): ?>
    <div class="alert alert-warning">Primero registre al menos una categoría y una marca en <a href="<?= url('catalogo') ?>">Categorías y marcas</a>.</div>
<?php endif; ?>
<form method="post" action="<?= url('productos/guardar') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) ($p['id'] ?? 0) ?>">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="nombre">Nombre del modelo</label>
                        <input class="form-control" id="nombre" name="nombre" required maxlength="120" placeholder="Ej.: Air Force 1 '07" value="<?= e(old('nombre', $p['nombre'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="categoria_id">Categoría</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Elegir…</option>
                            <?php $sel = old('categoria_id', $p['categoria_id'] ?? ''); foreach ($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $sel == $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="marca_id">Marca</label>
                        <select class="form-select" id="marca_id" name="marca_id" required>
                            <option value="">Elegir…</option>
                            <?php $sel = old('marca_id', $p['marca_id'] ?? ''); foreach ($marcas as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= $sel == $m['id'] ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="precio_venta">Precio de venta (S/)</label>
                        <input class="form-control" id="precio_venta" name="precio_venta" type="number" min="0" step="0.10" required inputmode="decimal" value="<?= e(old('precio_venta', $p['precio_venta'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="costo">Costo de compra (S/)</label>
                        <input class="form-control" id="costo" name="costo" type="number" min="0" step="0.10" inputmode="decimal" value="<?= e(old('costo', $p['costo'] ?? '')) ?>">
                        <div class="form-text">Solo lo ve el administrador.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="descripcion">Descripción (opcional)</label>
                        <input class="form-control" id="descripcion" name="descripcion" maxlength="255" value="<?= e(old('descripcion', $p['descripcion'] ?? '')) ?>">
                    </div>
                </div>
            </div>
            <?php if ($nuevo): ?>
            <div class="card card-body mt-3">
                <h2 class="h6">Variantes: colores y tallas</h2>
                <p class="small text-muted-2">Se crea una variante por cada combinación, cada una con su código interno. Para accesorios sin talla, escriba <strong>Única</strong>.</p>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label" for="colores">Colores (separados por comas)</label>
                        <input class="form-control" id="colores" name="colores" placeholder="Blanco, Negro" value="<?= e(old('colores')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="tallas">Tallas</label>
                        <input class="form-control" id="tallas" name="tallas" placeholder="38-43  o  S, M, L  o  Única" value="<?= e(old('tallas')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="stock_minimo">Stock mínimo</label>
                        <input class="form-control" id="stock_minimo" name="stock_minimo" type="number" min="0" value="<?= e(old('stock_minimo', 1)) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="card card-body">
                <label class="form-label" for="foto">Foto del modelo</label>
                <div class="mb-2 text-center">
                    <?php if ($foto): ?><img id="vistaFoto" class="foto foto-lg" src="<?= e($foto) ?>" alt="">
                    <?php else: ?><img id="vistaFoto" class="foto foto-lg d-none" alt=""><div id="sinFoto" class="foto-vacia foto-lg mx-auto"><i class="bi bi-camera"></i></div><?php endif; ?>
                </div>
                <input class="form-control" id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" capture="environment">
                <div class="form-text">JPG, PNG o WEBP de hasta <?= (int) config('app.foto_max_mb') ?> MB. Desde el celular puede tomarla con la cámara.</div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary btn-lg">Guardar producto</button>
        <a class="btn btn-outline-secondary btn-lg" href="<?= $nuevo ? url('productos') : url('productos/ver', ['id' => $p['id']]) ?>">Cancelar</a>
    </div>
</form>
<script>
document.getElementById('foto').addEventListener('change', ev => {
    const f = ev.target.files[0]; if (!f) return;
    const img = document.getElementById('vistaFoto');
    img.src = URL.createObjectURL(f); img.classList.remove('d-none');
    document.getElementById('sinFoto')?.classList.add('d-none');
});
</script>
