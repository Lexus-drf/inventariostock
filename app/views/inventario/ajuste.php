<div class="page-head">
    <div>
        <h1>Ajuste de stock</h1>
        <p>Para mermas, pares dañados o diferencias encontradas en el conteo físico. Queda registrado en el kardex con su motivo.</p>
    </div>
</div>
<form class="card card-body" style="max-width:720px" method="post" action="<?= url('inventario/ajuste') ?>"
      data-confirmar="¿Registrar el ajuste? No se podrá borrar; si se equivoca, registre otro ajuste.">
    <?= csrf_field() ?>
    <label class="form-label">Artículo</label>
    <div class="picker mb-3" data-picker data-name="variante_id" data-required></div>
    <div class="row g-3">
        <div class="col-sm-4">
            <label class="form-label" for="ubicacion_id">Ubicación</label>
            <select class="form-select" id="ubicacion_id" name="ubicacion_id">
                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['nombre']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4">
            <label class="form-label">Tipo</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="sentido" id="resta" value="resta" checked>
                <label class="btn btn-outline-danger" for="resta">− Restar</label>
                <input type="radio" class="btn-check" name="sentido" id="suma" value="suma">
                <label class="btn btn-outline-success" for="suma">+ Sumar</label>
            </div>
        </div>
        <div class="col-sm-4">
            <label class="form-label" for="cantidad">Cantidad</label>
            <input class="form-control" id="cantidad" type="number" name="cantidad" min="1" value="1" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="motivo">Motivo</label>
            <input class="form-control" id="motivo" name="motivo" required maxlength="255" placeholder="Ej.: par dañado en exhibición / diferencia en conteo del 12/10">
        </div>
    </div>
    <button class="btn btn-primary btn-lg mt-3">Registrar ajuste</button>
</form>
