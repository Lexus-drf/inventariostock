/* InventarioStock — comportamiento de la interfaz */
'use strict';

const fmtSoles = n => 'S/ ' + Number(n || 0).toFixed(2);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ---------- Confirmación antes de enviar formularios delicados ---------- */
document.addEventListener('submit', ev => {
  const f = ev.target;
  if (f.dataset.confirmar && !confirm(f.dataset.confirmar)) {
    ev.preventDefault();
    return;
  }
  // Evita el doble envío (doble venta por doble toque).
  if (!ev.defaultPrevented) {
    setTimeout(() => f.querySelectorAll('button[type=submit], button:not([type])').forEach(b => { b.disabled = true; }), 0);
  }
});

/* ---------- Agregar a la venta desde la búsqueda ---------- */
document.addEventListener('click', ev => {
  const btn = ev.target.closest('[data-agregar]');
  if (!btn) return;
  const d = JSON.parse(btn.dataset.agregar);
  const modalEl = document.getElementById('modalAgregar');
  if (!modalEl) return;
  const f = modalEl.querySelector('form');
  f.variante_id.value = d.variante_id;
  f.precio.value = Number(d.precio).toFixed(2);
  f.cantidad.value = 1;
  modalEl.querySelector('[data-campo=detalle]').textContent = `${d.nombre} · ${d.color} · talla ${d.talla} · ${d.codigo}`;
  const cont = modalEl.querySelector('[data-campo=ubicaciones]');
  cont.innerHTML = '';
  let elegida = false;
  d.ubicaciones.forEach(u => {
    const id = 'ag-ub-' + u.id;
    const sinStock = u.disponible <= 0;
    const marcar = !sinStock && !elegida;
    if (marcar) elegida = true;
    cont.insertAdjacentHTML('beforeend', `
      <input type="radio" class="btn-check" name="ubicacion_id" id="${id}" value="${u.id}" ${sinStock ? 'disabled' : ''} ${marcar ? 'checked' : ''} data-max="${u.disponible}">
      <label class="btn btn-outline-primary text-start d-flex justify-content-between" for="${id}">
        <span><i class="bi bi-geo-alt"></i> ${esc(u.nombre)}</span>
        <span>${u.disponible} disponible(s)${u.reservado ? ` · ${u.reservado} apartado(s)` : ''}</span>
      </label>`);
  });
  const ajustarMax = () => {
    const r = cont.querySelector('input:checked');
    f.cantidad.max = r ? r.dataset.max : 1;
  };
  cont.onchange = ajustarMax;
  ajustarMax();
  bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

/* ---------- Selector de artículo con sugerencias ----------
   <div class="picker" data-picker data-name="variante_id"> ... </div>
   Emite el evento "picker:elegido" con el artículo en ev.detail. */
function initPicker(root) {
  if (root.dataset.listo) return;
  root.dataset.listo = '1';
  const nombre = root.dataset.name || 'variante_id';
  root.insertAdjacentHTML('afterbegin', `
    <input type="hidden" name="${nombre}" value="">
    <div class="picker-buscar">
      <input type="search" class="form-control" placeholder="${esc(root.dataset.placeholder || 'Escriba modelo, color, talla o código')}" autocomplete="off">
    </div>
    <div class="picker-elegido d-none"></div>
    <div class="picker-lista d-none" role="listbox"></div>`);
  const oculto = root.querySelector(`input[name="${nombre}"]`);
  const caja = root.querySelector('.picker-buscar input');
  const lista = root.querySelector('.picker-lista');
  const elegido = root.querySelector('.picker-elegido');
  if (root.dataset.required !== undefined) caja.required = true;
  let temporizador, resultados = [], indice = -1;

  const pintar = () => {
    if (!resultados.length) {
      lista.innerHTML = '<div class="picker-item text-muted-2">Sin resultados</div>';
    } else {
      lista.innerHTML = resultados.map((r, i) => {
        const stock = r.stock.map(s => `${esc(s.ubicacion)}: ${s.disponible}`).join(' · ') || 'sin stock';
        return `<div class="picker-item ${i === indice ? 'activo' : ''}" data-i="${i}" role="option">
          <div><strong>${esc(r.nombre)}</strong> · ${esc(r.color)} · talla ${esc(r.talla)}</div>
          <small>${esc(r.marca)} · ${esc(r.codigo)} · ${stock}</small></div>`;
      }).join('');
    }
    lista.classList.remove('d-none');
  };
  const elegir = r => {
    oculto.value = r.id;
    caja.required = false;
    root.querySelector('.picker-buscar').classList.add('d-none');
    lista.classList.add('d-none');
    const stock = r.stock.map(s => `<span class="stock-pill ${s.disponible > 0 ? 'ok' : 'cero'}">${esc(s.ubicacion)}: ${s.disponible}</span>`).join(' ')
      || '<span class="stock-pill cero">Sin stock</span>';
    elegido.innerHTML = `<div class="d-flex justify-content-between gap-2">
        <div><strong>${esc(r.nombre)}</strong> · ${esc(r.color)} · talla ${esc(r.talla)}
          <div class="small text-muted-2">${esc(r.marca)} · ${esc(r.codigo)} · ${fmtSoles(r.precio_venta)}</div>
          <div class="mt-1 d-flex flex-wrap gap-1">${stock}</div></div>
        <button type="button" class="btn btn-sm btn-outline-secondary align-self-start">Cambiar</button></div>`;
    elegido.classList.remove('d-none');
    elegido.querySelector('button').onclick = () => {
      oculto.value = '';
      elegido.classList.add('d-none');
      root.querySelector('.picker-buscar').classList.remove('d-none');
      if (root.dataset.required !== undefined) caja.required = true;
      caja.value = '';
      caja.focus();
      root.dispatchEvent(new CustomEvent('picker:limpio', { bubbles: true }));
    };
    root.dispatchEvent(new CustomEvent('picker:elegido', { bubbles: true, detail: r }));
  };
  caja.addEventListener('input', () => {
    clearTimeout(temporizador);
    const q = caja.value.trim();
    if (q.length < 2) { lista.classList.add('d-none'); return; }
    temporizador = setTimeout(async () => {
      const resp = await fetch('index.php?r=buscar/api&q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'fetch' } });
      if (!resp.ok) return;
      resultados = await resp.json();
      indice = resultados.length ? 0 : -1;
      pintar();
    }, 220);
  });
  caja.addEventListener('keydown', ev => {
    if (lista.classList.contains('d-none')) return;
    if (ev.key === 'ArrowDown') { indice = Math.min(indice + 1, resultados.length - 1); pintar(); ev.preventDefault(); }
    if (ev.key === 'ArrowUp') { indice = Math.max(indice - 1, 0); pintar(); ev.preventDefault(); }
    if (ev.key === 'Enter' && indice >= 0) { elegir(resultados[indice]); ev.preventDefault(); }
    if (ev.key === 'Escape') lista.classList.add('d-none');
  });
  lista.addEventListener('mousedown', ev => {
    const it = ev.target.closest('[data-i]');
    if (it) { ev.preventDefault(); elegir(resultados[+it.dataset.i]); }
  });
  caja.addEventListener('blur', () => setTimeout(() => lista.classList.add('d-none'), 150));
  if (root.dataset.inicial) elegir(JSON.parse(root.dataset.inicial));
}
document.querySelectorAll('[data-picker]').forEach(initPicker);
window.initPicker = initPicker;

/* ---------- Gráfico de ventas (Chart.js) ---------- */
window.graficoVentas = function (canvasId, dias, importes, pares) {
  const el = document.getElementById(canvasId);
  if (!el || !window.Chart) return;
  const etiquetas = dias.map(d => { const [a, m, dd] = d.split('-'); return `${dd}/${m}`; });
  new Chart(el, {
    data: {
      labels: etiquetas,
      datasets: [
        { type: 'bar', label: 'Importe (S/)', data: importes, backgroundColor: '#14213d', borderRadius: 6, yAxisID: 'y', maxBarThickness: 42 },
        { type: 'line', label: 'Artículos vendidos', data: pares, borderColor: '#fca311', backgroundColor: '#fca311', tension: .3, yAxisID: 'y1', pointRadius: 3 },
      ],
    },
    options: {
      responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: c => c.dataset.yAxisID === 'y' ? ' ' + fmtSoles(c.raw) : ` ${c.raw} artículo(s)` } } },
      scales: {
        y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v }, grid: { color: '#eef0f4' } },
        y1: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: { precision: 0 } },
        x: { grid: { display: false } },
      },
    },
  });
};

/* ---------- Fotos por color ---------- */
document.addEventListener('click', ev => {
  // Miniaturas en el detalle del producto
  const mini = ev.target.closest('.miniatura');
  if (mini) {
    const img = document.getElementById('fotoPrincipal');
    if (img) img.src = mini.dataset.src;
    mini.parentElement.querySelectorAll('.miniatura').forEach(m => m.classList.toggle('activa', m === mini));
    return;
  }
  // En la búsqueda: tocar la foto del color la muestra en grande en la tarjeta
  const fc = ev.target.closest('[data-ver-foto]');
  if (fc) {
    const tarjeta = fc.closest('.producto-card');
    const img = tarjeta && tarjeta.querySelector('img[data-principal]');
    if (img) img.src = img.src.endsWith(fc.dataset.verFoto) ? img.dataset.principal : fc.dataset.verFoto;
  }
});
