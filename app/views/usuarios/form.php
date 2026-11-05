<?php $u = $usuario; ?>
<div class="page-head">
    <div><h1><?= $u ? 'Editar usuario' : 'Nuevo usuario' ?></h1></div>
</div>
<form class="card card-body" style="max-width:560px" method="post" action="<?= url('usuarios/guardar') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) ($u['id'] ?? 0) ?>">
    <div class="mb-3">
        <label class="form-label" for="nombre">Nombre</label>
        <input class="form-control" id="nombre" name="nombre" required maxlength="100" value="<?= e(old('nombre', $u['nombre'] ?? '')) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="usuario">Usuario para ingresar</label>
        <input class="form-control" id="usuario" name="usuario" required maxlength="40" pattern="[a-zA-Z0-9._\-]{3,40}" autocapitalize="none"
               value="<?= e(old('usuario', $u['usuario'] ?? '')) ?>">
        <div class="form-text">De 3 a 40 caracteres, sin espacios.</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="rol">Rol</label>
        <select class="form-select" id="rol" name="rol">
            <?php $rol = old('rol', $u['rol'] ?? 'vendedor'); ?>
            <option value="vendedor" <?= $rol === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
            <option value="admin" <?= $rol === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="form-label" for="clave"><?= $u ? 'Nueva contraseña (dejar vacío para no cambiarla)' : 'Contraseña' ?></label>
        <input class="form-control" id="clave" name="clave" type="password" minlength="6" autocomplete="new-password" <?= $u ? '' : 'required' ?>>
        <div class="form-text">Mínimo 6 caracteres. Se guarda cifrada.</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Guardar</button>
        <a class="btn btn-outline-secondary" href="<?= url('usuarios') ?>">Cancelar</a>
    </div>
</form>
