@extends('layouts.app')
@section('title', 'Pedir Reposición — AURA33')

@section('content')
<div style="padding: 60px 0 100px; background: var(--bg); min-height: 80vh;">
    <div class="container" style="max-width: 760px;">

        {{-- Header --}}
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('wholesale.portal') }}" style="font-size:0.85rem; color:var(--muted); text-decoration:none;">
                ← Volver al panel
            </a>
            <h1 style="font-family:var(--font-heading); font-size:1.8rem; margin: 0.5rem 0 0.25rem;">
                📦 Pedir reposición
            </h1>
            <p style="color:var(--muted); font-size:0.9rem;">
                Elegí los productos que necesitás y te contactamos para coordinar la entrega.
            </p>
        </div>

        @if($errors->any())
        <div class="portal-alert-error" style="margin-bottom:1.5rem;">
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('wholesale.restock.store') }}" id="restock-form">
            @csrf

            {{-- Lista de productos --}}
            <div id="items-container">
                <div class="restock-item-row" data-index="0">
                    @include('shop.wholesale.partials.restock-row', ['index' => 0, 'products' => $products])
                </div>
            </div>

            <button type="button" onclick="addRow()" class="btn-outline-muted" style="margin-top:0.75rem; font-size:0.85rem;">
                + Agregar otro producto
            </button>

            {{-- Notas --}}
            <div style="margin-top:1.5rem;">
                <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:0.4rem;">
                    Notas (opcional)
                </label>
                <textarea name="notes" rows="3" placeholder="Ej: necesito para la próxima semana..."
                    style="width:100%; padding:0.6rem 0.8rem; border:1px solid var(--border); border-radius:8px; background:var(--bg); color:var(--text); font-size:0.9rem; resize:vertical;"
                >{{ old('notes') }}</textarea>
            </div>

            <div style="margin-top:2rem; display:flex; gap:1rem; align-items:center;">
                <button type="submit" class="btn" style="padding:0.7rem 2rem;">
                    Enviar pedido
                </button>
                <a href="{{ route('wholesale.portal') }}" class="btn-outline-muted">Cancelar</a>
            </div>
        </form>

    </div>
</div>

{{-- Template fila para JS --}}
<template id="row-template">
    @include('shop.wholesale.partials.restock-row', ['index' => '__INDEX__', 'products' => $products])
</template>

<style>
.restock-item-row {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    position: relative;
}
.restock-remove-btn {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    font-size: 1rem;
    padding: 0.2rem 0.4rem;
}
.restock-remove-btn:hover { color: #e53e3e; }
.restock-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 0.75rem;
    align-items: end;
}
@media (max-width: 600px) {
    .restock-grid { grid-template-columns: 1fr; }
}
.restock-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--muted);
    display: block;
    margin-bottom: 0.3rem;
}
.restock-select, .restock-input {
    width: 100%;
    padding: 0.55rem 0.75rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--bg);
    color: var(--text);
    font-size: 0.9rem;
}
</style>

<script>
let rowCount = 1;

function addRow() {
    const template = document.getElementById('row-template').innerHTML;
    const html = template.replace(/__INDEX__/g, rowCount);
    const wrapper = document.createElement('div');
    wrapper.className = 'restock-item-row';
    wrapper.dataset.index = rowCount;
    wrapper.innerHTML = html;
    document.getElementById('items-container').appendChild(wrapper);
    rowCount++;
}

function removeRow(btn) {
    const row = btn.closest('.restock-item-row');
    const container = document.getElementById('items-container');
    if (container.querySelectorAll('.restock-item-row').length > 1) {
        row.remove();
    }
}

// Filtro por categoría
function filterCategory(selectEl, index) {
    const cat = selectEl.value;
    const productSelect = document.querySelector(`[name="items[${index}][product_id]"]`);
    productSelect.innerHTML = '<option value="">— Elegí un producto —</option>';
    if (!cat) return;

    const allOptions = productSelect.closest('.restock-item-row')
        .querySelectorAll(`.product-option[data-category="${cat}"]`);

    // Rebuild from data
    const dataEl = document.getElementById(`product-data-${index}`);
    if (!dataEl) return;
    const products = JSON.parse(dataEl.dataset.products);
    products.filter(p => p.category === cat).forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.name;
        productSelect.appendChild(opt);
    });
}
</script>
@endsection
