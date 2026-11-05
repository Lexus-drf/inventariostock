<div class="page-head"><div><h1>Cambiar contraseña</h1></div></div>
<form class="card card-body" style="max-width:480px" method="post" action="<?= url('cuenta') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="actual">Contraseña actual</label>
        <input class="form-control" id="actual" name="actual" type="password" required autocomplete="current-password">
    </div>
    <div class="mb-3">
        <label class="form-label" for="nueva">Nueva contraseña</label>
        <input class="form-control" id="nueva" name="nueva" type="password" minlength="6" required autocomplete="new-password">
    </div>
    <div class="mb-4">
        <label class="form-label" for="confirmar">Repita la nueva contraseña</label>
        <input class="form-control" id="confirmar" name="confirmar" type="password" minlength="6" required autocomplete="new-password">
    </div>
    <button class="btn btn-primary">Guardar</button>
</form>
