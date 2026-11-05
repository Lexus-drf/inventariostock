<div class="login-card card">
    <div class="login-brand">
        <span class="brand"><img src="<?= asset('img/icono.svg') ?>" alt="" width="34" height="34"><span>Inventario<strong>Stock</strong></span></span>
        <div class="small mt-2 opacity-75"><?= e(config('app.tienda')) ?></div>
    </div>
    <div class="card-body p-4">
        <?php foreach (flashes() as $f): ?>
            <div class="alert alert-<?= e($f['tipo']) ?> py-2"><?= e($f['mensaje']) ?></div>
        <?php endforeach; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger py-2" role="alert"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= url('auth/login') ?>" autocomplete="on">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="usuario">Usuario</label>
                <input class="form-control form-control-lg" id="usuario" name="usuario" required autofocus
                       autocapitalize="none" autocomplete="username" value="<?= e(old('usuario')) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label" for="clave">Contraseña</label>
                <input class="form-control form-control-lg" id="clave" name="clave" type="password" required autocomplete="current-password">
            </div>
            <button class="btn btn-primary btn-lg w-100">Ingresar</button>
        </form>
    </div>
</div>
