<!-- Ventana para agregar un artículo a la venta en curso -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="<?= url('ventas/agregar') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="variante_id">
            <input type="hidden" name="volver" value="<?= e($_SERVER['REQUEST_URI'] ?? '') ?>">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title h5" id="modalAgregarTitulo">Agregar a la venta</h2>
                    <div class="small text-muted-2" data-campo="detalle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">¿De dónde sale el artículo?</label>
                <div class="d-grid gap-2 mb-3" data-campo="ubicaciones"></div>
                <div class="row g-2">
                    <div class="col-5">
                        <label class="form-label" for="agCantidad">Cantidad</label>
                        <input class="form-control form-control-lg" id="agCantidad" type="number" name="cantidad" min="1" value="1" required inputmode="numeric">
                    </div>
                    <div class="col-7">
                        <label class="form-label" for="agPrecio">Precio unitario (S/)</label>
                        <input class="form-control form-control-lg" id="agPrecio" type="number" name="precio" min="0" step="0.10" required inputmode="decimal">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="ir" value="venta" class="btn btn-accent btn-lg flex-fill"><i class="bi bi-bag-check"></i> Agregar e ir a la venta</button>
                <button type="submit" name="ir" value="seguir" class="btn btn-outline-primary btn-lg flex-fill">Agregar y seguir buscando</button>
            </div>
        </form>
    </div>
</div>
