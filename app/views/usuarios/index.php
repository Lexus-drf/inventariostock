<div class="page-head">
    <div>
        <h1>Usuarios</h1>
        <p>El administrador gestiona productos, ingresos, ajustes y reportes. El vendedor consulta, vende, hace cambios y apartados, y no ve costos ni reportes.</p>
    </div>
    <a class="btn btn-primary" href="<?= url('usuarios/crear') ?>"><i class="bi bi-person-plus"></i> Nuevo usuario</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Último acceso</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr class="<?= $u['activo'] ? '' : 'text-muted-2' ?>">
                    <td class="fw-semibold"><?= e($u['nombre']) ?></td>
                    <td><?= e($u['usuario']) ?></td>
                    <td><span class="badge text-bg-<?= $u['rol'] === 'admin' ? 'primary' : 'light border' ?>"><?= $u['rol'] === 'admin' ? 'Administrador' : 'Vendedor' ?></span></td>
                    <td><?= fecha($u['ultimo_acceso']) ?></td>
                    <td><?= $u['activo'] ? '<span class="stock-pill ok">Activo</span>' : '<span class="stock-pill cero">Inactivo</span>' ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="<?= url('usuarios/editar', ['id' => $u['id']]) ?>">Editar</a>
                        <?php if ($u['id'] != Auth::id()): ?>
                        <form class="d-inline" method="post" action="<?= url('usuarios/alternar') ?>"
                              data-confirmar="<?= $u['activo'] ? '¿Desactivar este usuario? Ya no podrá ingresar.' : '¿Activar este usuario?' ?>">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn btn-sm btn-outline-<?= $u['activo'] ? 'danger' : 'success' ?>"><?= $u['activo'] ? 'Desactivar' : 'Activar' ?></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
