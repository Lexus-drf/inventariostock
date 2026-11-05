<?php $admin = Auth::esAdmin(); $u = Auth::usuario(); ?>
<div class="page-head">
    <div>
        <h1>Hola, <?= e(explode(' ', $u['nombre'])[0]) ?></h1>
        <p><?= e(ucfirst(strftime_es(time()))) ?></p>
    </div>
</div>

<section class="hero-search mb-3">
    <h2><i class="bi bi-search me-1"></i> ¿Qué busca el cliente?</h2>
    <form action="index.php" method="get" class="d-flex gap-2">
        <input type="hidden" name="r" value="buscar">
        <input class="form-control" type="search" name="q" placeholder="Ej.: nike blanca 40, medias, mochila" autofocus>
        <button class="btn btn-accent px-4">Buscar</button>
    </form>
</section>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card kpi h-100">
            <i class="bi bi-receipt"></i>
            <div class="valor"><?= (int) $resumen['ventas'] ?></div>
            <div class="etiqueta">Ventas de hoy</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card kpi h-100">
            <i class="bi bi-box2"></i>
            <div class="valor"><?= (int) $resumen['pares'] ?></div>
            <div class="etiqueta">Artículos vendidos hoy</div>
        </div>
    </div>
    <?php if ($admin): ?>
    <div class="col-6 col-md-3">
        <div class="card kpi h-100">
            <i class="bi bi-cash-coin"></i>
            <div class="valor"><?= soles($resumen['importe']) ?></div>
            <div class="etiqueta">Importe de hoy</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a class="card kpi h-100 text-decoration-none" href="<?= url('reportes/alertas') ?>">
            <i class="bi bi-exclamation-triangle"></i>
            <div class="valor <?= $nAlertas ? 'text-warning-emphasis' : '' ?>"><?= $nAlertas ?></div>
            <div class="etiqueta">Alertas de stock mínimo</div>
        </a>
    </div>
    <?php else: ?>
    <div class="col-6 col-md-3">
        <a class="card kpi h-100 text-decoration-none" href="<?= url('reservas') ?>">
            <i class="bi bi-bookmark"></i>
            <div class="valor"><?= $nReservas ?></div>
            <div class="etiqueta">Apartados y encargos activos</div>
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="accesos mb-4">
    <a class="acceso" href="<?= url('ventas/carrito') ?>"><i class="bi bi-bag-check"></i>Registrar venta<small>Busque y agregue artículos</small></a>
    <a class="acceso" href="<?= url('ventas/cambio') ?>"><i class="bi bi-arrow-left-right"></i>Cambio de talla<small>Devuelve uno y lleva otro</small></a>
    <a class="acceso" href="<?= url('reservas/crear') ?>"><i class="bi bi-bookmark-plus"></i>Nuevo apartado<small>Con adelanto del cliente</small></a>
    <a class="acceso" href="<?= url('inventario/traslado') ?>"><i class="bi bi-truck"></i>Traslado<small>Local ↔ vivienda</small></a>
    <?php if ($admin): ?>
    <a class="acceso" href="<?= url('inventario/ingreso') ?>"><i class="bi bi-box-arrow-in-down"></i>Ingreso de mercadería<small>Lo que llegó del viaje</small></a>
    <a class="acceso" href="<?= url('productos/crear') ?>"><i class="bi bi-plus-square"></i>Nuevo producto<small>Con foto, colores y tallas</small></a>
    <?php endif; ?>
</div>

<div class="row g-3">
    <?php if ($admin): ?>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                Ventas de los últimos 7 días
                <a class="small" href="<?= url('reportes') ?>">Ver reportes</a>
            </div>
            <div class="card-body">
                <div class="chart-box"><canvas id="graficoSemana"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                Por reponer
                <a class="small" href="<?= url('reportes/alertas') ?>">Ver todas (<?= $nAlertas ?>)</a>
            </div>
            <?php if (!$alertas): ?>
                <div class="vacio"><i class="bi bi-check2-circle"></i>Todo el stock está sobre el mínimo.</div>
            <?php else: ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($alertas as $a): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <a href="<?= url('productos/ver', ['id' => $a['producto_id']]) ?>" class="fw-semibold text-decoration-none"><?= e($a['nombre']) ?></a>
                        <div class="small text-muted-2"><?= e($a['color']) ?> · talla <?= e($a['talla']) ?> · mín. <?= (int) $a['stock_minimo'] ?></div>
                    </div>
                    <span class="stock-pill <?= $a['total'] == 0 ? 'cero' : 'bajo' ?>"><?= (int) $a['total'] ?> en stock</span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Apartados y encargos activos (<?= $nReservas ?>)
                <a class="small" href="<?= url('reservas') ?>">Ver todos</a>
            </div>
            <?php if (!$reservas): ?>
                <div class="vacio"><i class="bi bi-bookmark"></i>No hay apartados ni encargos pendientes.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Tipo</th><th>Artículo</th><th>Cliente</th><th>Recoge hasta</th><th class="num">Saldo</th></tr></thead>
                    <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><span class="badge text-bg-<?= $r['tipo'] === 'APARTADO' ? 'primary' : 'secondary' ?>"><?= $r['tipo'] === 'APARTADO' ? 'Apartado' : 'Encargo' ?></span></td>
                            <td><?= e($r['producto'] ? "{$r['producto']} {$r['color']} " . talla_corta($r['talla']) . "" : $r['descripcion']) ?></td>
                            <td><?= e($r['cliente_nombre']) ?></td>
                            <td><?= $r['fecha_limite'] ? fecha($r['fecha_limite'], false) : '—' ?></td>
                            <td class="num"><?= soles($r['precio'] * $r['cantidad'] - $r['adelanto']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($admin): ?>
<script src="<?= asset('vendor/chartjs/chart.umd.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const datos = <?= json_encode($semana) ?>;
    graficoVentas('graficoSemana', datos.map(d => d.dia), datos.map(d => d.importe), datos.map(d => d.pares));
});
</script>
<?php endif; ?>
