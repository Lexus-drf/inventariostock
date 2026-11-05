<div class="page-head">
    <div>
        <h1>Cambio de talla o modelo</h1>
        <p>El par devuelto vuelve al stock y el nuevo se descuenta. Se calcula la diferencia de precio.</p>
    </div>
</div>

<form method="post" action="<?= url('ventas/cambio') ?>" id="formCambio">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-arrow-return-left text-success"></i> 1. Lo que devuelve el cliente</div>
                <div class="card-body">
                    <div class="picker mb-3" data-picker data-name="devuelve_id" data-required id="pkDevuelve"></div>
                    <div class="row g-2">
                        <div class="col-12 col-sm-5">
                            <label class="form-label">Vuelve a</label>
                            <select class="form-select" name="devuelve_ubicacion">
                                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['nombre']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4 col-sm-3">
                            <label class="form-label">Cant.</label>
                            <input class="form-control" type="number" name="devuelve_cantidad" min="1" value="1" required>
                        </div>
                        <div class="col-8 col-sm-4">
                            <label class="form-label">Precio pagado S/</label>
                            <input class="form-control" type="number" name="devuelve_precio" min="0" step="0.10" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-arrow-right text-danger"></i> 2. Lo que se lleva</div>
                <div class="card-body">
                    <div class="picker mb-3" data-picker data-name="lleva_id" data-required id="pkLleva"></div>
                    <div class="row g-2">
                        <div class="col-12 col-sm-5">
                            <label class="form-label">Sale de</label>
                            <select class="form-select" name="lleva_ubicacion">
                                <?php foreach ($ubicaciones as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['nombre']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4 col-sm-3">
                            <label class="form-label">Cant.</label>
                            <input class="form-control" type="number" name="lleva_cantidad" min="1" value="1" required>
                        </div>
                        <div class="col-8 col-sm-4">
                            <label class="form-label">Precio S/</label>
                            <input class="form-control" type="number" name="lleva_precio" min="0" step="0.10" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-body mt-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label" for="nota">Motivo (opcional)</label>
                <input class="form-control" id="nota" name="nota" maxlength="255" placeholder="Ej.: le quedó chica la talla">
            </div>
            <div class="col-md-3">
                <div class="card-title-sm">Diferencia</div>
                <div class="fs-4 fw-bold num" id="diferencia">S/ 0.00</div>
                <div class="small text-muted-2" id="difTexto">Sin diferencia</div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-accent btn-lg w-100"><i class="bi bi-check2-circle"></i> Registrar cambio</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const f = document.getElementById('formCambio');
    const calcular = () => {
        const dif = (+f.lleva_cantidad.value || 0) * (+f.lleva_precio.value || 0)
                  - (+f.devuelve_cantidad.value || 0) * (+f.devuelve_precio.value || 0);
        document.getElementById('diferencia').textContent = fmtSoles(Math.abs(dif));
        document.getElementById('difTexto').textContent = dif > 0 ? 'Cobrar al cliente' : dif < 0 ? 'Devolver al cliente' : 'Sin diferencia';
    };
    f.addEventListener('input', calcular);
    document.getElementById('pkDevuelve').addEventListener('picker:elegido', ev => { f.devuelve_precio.value = (+ev.detail.precio_venta).toFixed(2); calcular(); });
    document.getElementById('pkLleva').addEventListener('picker:elegido', ev => {
        f.lleva_precio.value = (+ev.detail.precio_venta).toFixed(2);
        const conStock = ev.detail.stock.find(s => s.disponible > 0);
        if (conStock) f.lleva_ubicacion.value = conStock.ubicacion_id;
        calcular();
    });
});
</script>
