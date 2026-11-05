<div class="page-head">
    <div><h1>Categorías y marcas</h1><p>Las inactivas dejan de ofrecerse al crear productos; los productos existentes se conservan.</p></div>
</div>
<div class="row g-3">
<?php foreach (['categoria' => ['Categorías', $categoria], 'marca' => ['Marcas', $marca]] as $tabla => [$titulo, $filas]): ?>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><?= $titulo ?></div>
            <div class="card-body border-bottom">
                <form class="d-flex gap-2" method="post" action="<?= url('catalogo/guardar') ?>">
                    <?= csrf_field() ?><input type="hidden" name="tabla" value="<?= $tabla ?>">
                    <input class="form-control" name="nombre" maxlength="60" required placeholder="Nueva <?= $tabla === 'categoria' ? 'categoría' : 'marca' ?>">
                    <button class="btn btn-primary text-nowrap"><i class="bi bi-plus-lg"></i> Agregar</button>
                </form>
            </div>
            <ul class="list-group list-group-flush">
            <?php foreach ($filas as $f): ?>
                <li class="list-group-item d-flex align-items-center gap-2 <?= $f['activo'] ? '' : 'text-muted-2' ?>">
                    <form class="d-flex gap-2 flex-fill" method="post" action="<?= url('catalogo/guardar') ?>">
                        <?= csrf_field() ?><input type="hidden" name="tabla" value="<?= $tabla ?>"><input type="hidden" name="id" value="<?= $f['id'] ?>">
                        <input class="form-control form-control-sm border-0 bg-transparent px-1" name="nombre" value="<?= e($f['nombre']) ?>" maxlength="60" onchange="this.form.submit()" aria-label="Nombre">
                    </form>
                    <span class="small text-muted-2 text-nowrap"><?= (int) $f['productos'] ?> producto(s)</span>
                    <form method="post" action="<?= url('catalogo/alternar') ?>">
                        <?= csrf_field() ?><input type="hidden" name="tabla" value="<?= $tabla ?>"><input type="hidden" name="id" value="<?= $f['id'] ?>">
                        <button class="btn btn-sm btn-link text-<?= $f['activo'] ? 'danger' : 'success' ?> text-nowrap"><?= $f['activo'] ? 'Desactivar' : 'Activar' ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endforeach; ?>
</div>
