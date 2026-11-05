<div class="page-head">
    <div>
        <h1>Traslado entre ubicaciones</h1>
        <p>Cuando se lleva mercadería de la vivienda al local o al revés. El stock total no cambia.</p>
    </div>
</div>
<form class="card card-body" style="max-width:720px" method="post" action="<?= url('inventario/traslado') ?>" id="formTraslado">
    <?= csrf_field() ?>
    <label class="form-label">Artículo</label>
    <div class="picker mb-3" data-picker data-name="variante_id" data-required id="pkTraslado"></div>
    <div class="row g-3">
        <div class="col-sm-4">
            <label class="form-label" for="origen">Desde</label>
            <select class="form-select" id="origen" name="origen">
                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>" <?= $u['id'] == 2 ? 'selected' : '' ?>><?= e($u['nombre']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4">
            <label class="form-label" for="destino">Hacia</label>
            <select class="form-select" id="destino" name="destino">
                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>" <?= $u['id'] == 1 ? 'selected' : '' ?>><?= e($u['nombre']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4">
            <label class="form-label" for="cantidad">Cantidad</label>
            <input class="form-control" id="cantidad" type="number" name="cantidad" min="1" value="1" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="nota">Nota (opcional)</label>
            <input class="form-control" id="nota" name="nota" maxlength="255" placeholder="Ej.: para exhibir en vitrina">
        </div>
    </div>
    <button class="btn btn-primary btn-lg mt-3"><i class="bi bi-truck"></i> Registrar traslado</button>
</form>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const f = document.getElementById('formTraslado');
    f.origen.addEventListener('change', () => { if (f.origen.value === f.destino.value) f.destino.selectedIndex = (f.origen.selectedIndex + 1) % f.destino.options.length; });
    f.destino.addEventListener('change', () => { if (f.origen.value === f.destino.value) f.origen.selectedIndex = (f.destino.selectedIndex + 1) % f.origen.options.length; });
});
</script>
