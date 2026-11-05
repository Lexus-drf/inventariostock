<?php $esEncargo = $tipo === 'ENCARGO'; ?>
<div class="page-head">
    <div>
        <h1><?= $esEncargo ? 'Nuevo encargo' : 'Nuevo apartado' ?></h1>
        <p><?= $esEncargo ? 'Para un artículo que no hay en stock y se traerá en el próximo viaje.' : 'Separa un par que hay en stock; deja de figurar como disponible.' ?></p>
    </div>
</div>
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link <?= $esEncargo ? '' : 'active' ?>" href="<?= url('reservas/crear') ?>">Apartado</a></li>
    <li class="nav-item"><a class="nav-link <?= $esEncargo ? 'active' : '' ?>" href="<?= url('reservas/crear', ['tipo' => 'ENCARGO']) ?>">Encargo</a></li>
</ul>
<form class="card card-body" style="max-width:760px" method="post" action="<?= url('reservas/crear') ?>" id="formReserva">
    <?= csrf_field() ?>
    <input type="hidden" name="tipo" value="<?= $tipo ?>">
    <label class="form-label">Artículo <?= $esEncargo ? '(si ya existe en el catálogo)' : '' ?></label>
    <div class="picker mb-3" data-picker data-name="variante_id" <?= $esEncargo ? '' : 'data-required' ?> id="pkReserva"></div>
    <?php if ($esEncargo): ?>
        <div class="mb-3">
            <label class="form-label" for="descripcion">O descríbalo</label>
            <input class="form-control" id="descripcion" name="descripcion" maxlength="255" placeholder="Ej.: Nike Air Max 90 negras, talla 41" value="<?= e(old('descripcion')) ?>">
        </div>
    <?php endif; ?>
    <div class="row g-3">
        <?php if (!$esEncargo): ?>
        <div class="col-sm-4">
            <label class="form-label" for="ubicacion_id">Ubicación del par</label>
            <select class="form-select" id="ubicacion_id" name="ubicacion_id">
                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['nombre']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="col-4 col-sm-2">
            <label class="form-label" for="cantidad">Cant.</label>
            <input class="form-control" id="cantidad" type="number" name="cantidad" min="1" value="<?= e(old('cantidad', 1)) ?>" required>
        </div>
        <div class="col-8 col-sm-3">
            <label class="form-label" for="precio">Precio unit. S/</label>
            <input class="form-control" id="precio" type="number" name="precio" min="0" step="0.10" required value="<?= e(old('precio')) ?>">
        </div>
        <div class="col-sm-3">
            <label class="form-label" for="adelanto">Adelanto S/</label>
            <input class="form-control" id="adelanto" type="number" name="adelanto" min="0" step="0.10" value="<?= e(old('adelanto', '0')) ?>">
        </div>
        <div class="col-sm-5">
            <label class="form-label" for="cliente_nombre">Nombre del cliente</label>
            <input class="form-control" id="cliente_nombre" name="cliente_nombre" required maxlength="100" value="<?= e(old('cliente_nombre')) ?>">
        </div>
        <div class="col-sm-4">
            <label class="form-label" for="cliente_telefono">Teléfono</label>
            <input class="form-control" id="cliente_telefono" name="cliente_telefono" required inputmode="tel" pattern="[0-9 +]{6,20}" value="<?= e(old('cliente_telefono')) ?>">
        </div>
        <div class="col-sm-3">
            <label class="form-label" for="fecha_limite"><?= $esEncargo ? 'Fecha estimada' : 'Recoge hasta' ?></label>
            <input class="form-control" id="fecha_limite" type="date" name="fecha_limite" value="<?= e(old('fecha_limite', date('Y-m-d', strtotime('+7 days')))) ?>">
        </div>
    </div>
    <div class="alert alert-light border small mt-3 mb-3"><i class="bi bi-lock"></i> Del cliente solo se guarda el nombre y el teléfono, y se borran al completar o anular la reserva.</div>
    <button class="btn btn-accent btn-lg">Registrar <?= $esEncargo ? 'encargo' : 'apartado' ?></button>
</form>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const f = document.getElementById('formReserva');
    document.getElementById('pkReserva').addEventListener('picker:elegido', ev => {
        f.precio.value = (+ev.detail.precio_venta).toFixed(2);
        const conStock = ev.detail.stock.find(s => s.disponible > 0);
        if (conStock && f.ubicacion_id) f.ubicacion_id.value = conStock.ubicacion_id;
    });
});
</script>
