@php
    $allProducts = $products->flatMap(fn($g, $cat) => $g->map(fn($p) => [
        'id' => $p->id, 'name' => $p->name, 'category' => $cat
    ]))->values()->toArray();
    $categories = $products->keys()->sort()->values();
@endphp

<button type="button" class="restock-remove-btn" onclick="removeRow(this)" title="Quitar">✕</button>

{{-- JSON oculto para filtro por categoría en JS --}}
<div id="product-data-{{ $index }}"
     data-products="{{ json_encode($allProducts) }}"
     style="display:none;"></div>

<div class="restock-grid">
    <div>
        <label class="restock-label">Categoría</label>
        <select class="restock-select" onchange="filterCategory(this, {{ $index }})">
            <option value="">— Todas —</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="restock-label">Producto</label>
        <select name="items[{{ $index }}][product_id]" class="restock-select" required>
            <option value="">— Elegí un producto —</option>
            @foreach($products as $cat => $group)
                <optgroup label="{{ $cat }}">
                    @foreach($group as $product)
                        <option value="{{ $product->id }}"
                            data-category="{{ $cat }}"
                            {{ old("items.{$index}.product_id") == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>

    <div>
        <label class="restock-label">Cantidad</label>
        <input type="number" name="items[{{ $index }}][quantity]"
               class="restock-input" min="1" value="{{ old("items.{$index}.quantity", 1) }}"
               required style="width:80px;">
    </div>
</div>
