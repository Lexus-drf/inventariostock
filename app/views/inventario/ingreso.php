<?php
$opcionesUb = fn($sel) => implode('', array_map(fn($u) => '<option value="' . $u['id'] . '"' . ($u['id'] == $sel ? ' selected' : '') . '>' . e($u['nombre']) . '</option>', $ubicaciones));
?>
<div class="page-head">
    <div>
        <h1>Ingreso de mercadería</h1>
        <p><?= $producto ? 'Escriba cuántas unidades llegaron de cada variante de ' . e($producto['nombre']) . '.' : 'Agregue lo que llegó (por ejemplo, la compra de un viaje). El stock sube al registrar.' ?></p>
    </div>
    <?php if ($producto): ?><a class="btn btn-outline-secondary" href="<?= url('inventario/ingreso') ?>">Ingreso de varios productos</a><?php endif; ?>
</div>

<form method="post" action="<?= url('inventario/ingreso') ?>" id="formIngreso">
    <?= csrf_field() ?>
    <input type="hidden" name="producto" value="<?= (int) ($producto['id'] ?? 0) ?>">

    <?php if ($producto): ?>
    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Color</th><th>Talla</th><th>Código</th><th class="num">Stock actual</th><th style="width:150px">Llega a</th><th style="width:110px">Cantidad</th><th style="width:130px">Costo unit.</th></tr></thead>
                <tbody>
                <?php foreach ($producto['variantes'] as $i => $v): if (!$v['activo']) continue; ?>
                    <tr>
                        <td><?= e($v['color']) ?></td><td class="fw-semibold"><?= e($v['talla']) ?></td><td class="small"><?= e($v['codigo']) ?></td>
                        <td class="num"><?= (int) $v['total'] ?></td>
                        <td><input type="hidden" name="items[<?= $i ?>][variante_id]" value="<?= $v['id'] ?>">
                            <select class="form-select form-select-sm" name="items[<?= $i ?>][ubicacion_id]"><?= $opcionesUb(1) ?></select></td>
                        <td><input class="form-control form-control-sm" type="number" min="0" name="items[<?= $i ?>][cantidad]" placeholder="0" inputmode="numeric"></td>
                        <td><input class="form-control form-control-sm" type="number" min="0" step="0.10" name="items[<?= $i ?>][costo]" value="<?= number_format($producto['costo'], 2, '.', '') ?>"></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div id="filas"></div>
    <button type="button" class="btn btn-outline-primary mb-3" id="agregarFila"><i class="bi bi-plus-lg"></i> Agregar otro artículo</button>
    <template id="tplFila">
        <div class="card card-body mb-2 fila-ingreso">
            <div class="row g-2 align-items-end">
                <div class="col-lg-6"><label class="form-label small">Artículo</label><div class="picker" data-picker data-name="items[__i__][variante_id]"></div></div>
                <div class="col-6 col-lg-2"><label class="form-label small">Llega a</label><select class="form-select" name="items[__i__][ubicacion_id]"><?= $opcionesUb(1) ?></select></div>
                <div class="col-3 col-lg-1"><label class="form-label small">Cant.</label><input class="form-control" type="number" min="1" name="items[__i__][cantidad]" value="1" inputmode="numeric"></div>
                <div class="col-3 col-lg-2"><label class="form-label small">Costo S/</label><input class="form-control" type="number" min="0" step="0.10" name="items[__i__][costo]"></div>
                <div class="col-lg-1 text-end"><button type="button" class="btn btn-outline-danger quitar" aria-label="Quitar fila"><i class="bi bi-trash"></i></button></div>
            </div>
        </div>
    </template>
    <?php endif; ?>

    <div class="card card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label" for="nota">Nota (opcional)</label>
                <input class="form-control" id="nota" name="nota" maxlength="255" placeholder="Ej.: compra en Tacna del 10/10">
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="actCosto" name="actualizar_costo" value="1" checked>
                    <label class="form-check-label" for="actCosto">Actualizar el costo del producto</label>
                </div>
            </div>
            <div class="col-md-3"><button class="btn btn-accent btn-lg w-100"><i class="bi bi-box-arrow-in-down"></i> Registrar ingreso</button></div>
        </div>
    </div>
</form>

<?php if (!$producto): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let n = 0;
    const cont = document.getElementById('filas');
    const tpl = document.getElementById('tplFila').innerHTML;
    const agregar = () => {
        cont.insertAdjacentHTML('beforeend', tpl.replaceAll('__i__', n++));
        const fila = cont.lastElementChild;
        initPicker(fila.querySelector('[data-picker]'));
        fila.querySelector('.quitar').onclick = () => fila.remove();
        fila.addEventListener('picker:elegido', ev => {
            const costo = fila.querySelector('input[name$="[costo]"]');
            if (ev.detail.costo !== undefined) costo.value = (+ev.detail.costo).toFixed(2);
        });
        fila.querySelector('.picker-buscar input').focus();
    };
    document.getElementById('agregarFila').onclick = agregar;
    agregar();
});
</script>
<?php endif; ?>
