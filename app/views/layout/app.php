<?php
/** Plantilla principal para usuarios con sesión. */
$u        = Auth::usuario();
$admin    = Auth::esAdmin();
$ruta     = (string) ($_GET['r'] ?? 'inicio');
$carrito  = array_sum(array_column($_SESSION['carrito'] ?? [], 'cantidad'));
$nAlertas = $admin ? Producto::contarAlertas() : 0;
$nReservas = Reserva::contarActivas();
$activo   = fn(string ...$prefijos) => array_filter($prefijos, fn($p) => str_starts_with($ruta, $p)) ? 'active' : '';

$menu = [
    ['Atención', [
        ['inicio', 'house', 'Inicio'],
        ['buscar', 'search', 'Buscar productos'],
        ['ventas/carrito', 'bag', 'Venta', $carrito],
        ['ventas/cambio', 'arrow-left-right', 'Cambio de talla o modelo'],
        ['reservas', 'bookmark', 'Apartados y encargos', $nReservas],
        ['inventario/traslado', 'truck', 'Traslado local ↔ vivienda'],
        ['inventario/historial', 'clock-history', 'Historial de operaciones'],
    ]],
];
if ($admin) {
    $menu[] = ['Administración', [
        ['productos', 'box-seam', 'Productos'],
        ['inventario/ingreso', 'box-arrow-in-down', 'Ingreso de mercadería'],
        ['inventario/ajuste', 'sliders', 'Ajuste de stock'],
        ['catalogo', 'tags', 'Categorías y marcas'],
        ['reportes/alertas', 'exclamation-triangle', 'Alertas de stock', $nAlertas],
        ['reportes', 'graph-up', 'Reportes'],
        ['usuarios', 'people', 'Usuarios'],
    ]];
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#14213d">
    <title><?= e(($tituloPagina ? $tituloPagina . ' · ' : '') . config('app.nombre')) ?></title>
    <link rel="icon" href="<?= asset('img/icono.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body data-csrf="<?= e(csrf_token()) ?>">

<header class="topbar">
    <div class="container-xl d-flex align-items-center gap-2">
        <button class="btn btn-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menu" aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>
        <a class="brand" href="<?= url('inicio') ?>">
            <img src="<?= asset('img/icono.svg') ?>" alt="" width="28" height="28">
            <span>Inventario<strong>Stock</strong></span>
        </a>
        <form class="topbar-search d-none d-md-flex" action="index.php" method="get" role="search">
            <input type="hidden" name="r" value="buscar">
            <i class="bi bi-search"></i>
            <input type="search" name="q" placeholder="Buscar modelo, marca, color, talla o código" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Buscar">
        </form>
        <div class="ms-auto d-flex align-items-center gap-1">
            <a class="btn btn-icon position-relative" href="<?= url('ventas/carrito') ?>" aria-label="Venta en curso">
                <i class="bi bi-bag"></i>
                <?php if ($carrito): ?><span class="badge-dot"><?= $carrito ?></span><?php endif; ?>
            </a>
            <div class="dropdown">
                <button class="btn btn-user dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar"><?= e(mb_strtoupper(mb_substr($u['nombre'], 0, 1))) ?></span>
                    <span class="d-none d-sm-inline"><?= e($u['nombre']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-secondary"><?= $admin ? 'Administrador' : 'Vendedor' ?></span></li>
                    <li><a class="dropdown-item" href="<?= url('cuenta') ?>"><i class="bi bi-key me-2"></i>Cambiar contraseña</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?= url('auth/salir') ?>" method="post">
                            <?= csrf_field() ?>
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="container-xl layout">
    <nav class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="menu" aria-label="Menú principal">
        <div class="offcanvas-header d-lg-none">
            <span class="brand"><img src="<?= asset('img/icono.svg') ?>" alt="" width="26" height="26"><span>Inventario<strong>Stock</strong></span></span>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#menu" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body flex-column">
            <?php foreach ($menu as [$grupo, $items]): ?>
                <div class="menu-grupo"><?= e($grupo) ?></div>
                <?php foreach ($items as $item): ?>
                    <?php [$r, $icono, $texto] = $item; $badge = $item[3] ?? 0;
                    $esActivo = $r === 'inicio' ? ($ruta === 'inicio' ? 'active' : '')
                        : ($r === 'reportes' ? ($ruta === 'reportes' || $ruta === 'reportes/index' || in_array($ruta, ['reportes/existencias', 'reportes/rotacion']) ? 'active' : '') : $activo($r)); ?>
                    <a class="menu-link <?= $esActivo ?>" href="<?= url($r) ?>">
                        <i class="bi bi-<?= $icono ?>"></i><span><?= e($texto) ?></span>
                        <?php if ($badge): ?><span class="menu-badge <?= $r === 'reportes/alertas' ? 'warn' : '' ?>"><?= $badge ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </nav>

    <main class="contenido" id="contenido">
        <?php foreach (flashes() as $f): ?>
            <div class="alert alert-<?= e($f['tipo']) ?> alert-dismissible fade show" role="alert">
                <?= e($f['mensaje']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endforeach; ?>
        <?= $contenido ?>
    </main>
</div>

<nav class="bottombar d-lg-none" aria-label="Accesos rápidos">
    <a href="<?= url('inicio') ?>" class="<?= $ruta === 'inicio' ? 'active' : '' ?>"><i class="bi bi-house"></i><span>Inicio</span></a>
    <a href="<?= url('buscar') ?>" class="<?= $activo('buscar') ?>"><i class="bi bi-search"></i><span>Buscar</span></a>
    <a href="<?= url('ventas/carrito') ?>" class="<?= $activo('ventas/carrito') ?>">
        <i class="bi bi-bag"></i><span>Venta</span><?php if ($carrito): ?><em><?= $carrito ?></em><?php endif; ?>
    </a>
    <a href="<?= url('reservas') ?>" class="<?= $activo('reservas') ?>"><i class="bi bi-bookmark"></i><span>Apartados</span></a>
    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#menu"><i class="bi bi-grid"></i><span>Más</span></button>
</nav>

<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
