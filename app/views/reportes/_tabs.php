<?php $r = $_GET['r'] ?? ''; ?>
<ul class="nav nav-pills mb-3 no-print">
    <li class="nav-item"><a class="nav-link <?= in_array($r, ['reportes', 'reportes/index']) ? 'active' : '' ?>" href="<?= url('reportes') ?>">Ventas</a></li>
    <li class="nav-item"><a class="nav-link <?= $r === 'reportes/existencias' ? 'active' : '' ?>" href="<?= url('reportes/existencias') ?>">Existencias y valorización</a></li>
    <li class="nav-item"><a class="nav-link <?= $r === 'reportes/rotacion' ? 'active' : '' ?>" href="<?= url('reportes/rotacion') ?>">Rotación</a></li>
    <li class="nav-item"><a class="nav-link <?= $r === 'reportes/alertas' ? 'active' : '' ?>" href="<?= url('reportes/alertas') ?>">Alertas de stock</a></li>
</ul>
