<div class="product-card {{ !$product->isInStock() ? 'product-card--out' : '' }}">
    <a href="{{ route('product', $product->slug) }}" class="product-card-image">
        <span class="product-tag">{{ $product->properties['chakra'] ?? $product->category->name ?? '' }}</span>
        @if(!$product->isInStock())
            <span class="product-card-out-badge">Sin stock</span>
        @endif
        @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" class="{{ !$product->isInStock() ? 'img-out' : '' }}">
        @else
            <div class="product-card-placeholder">{{ $product->category->icon ?? '🔮' }}</div>
        @endif
    </a>

    <div class="product-card-body">
        <div class="product-card-cat">{{ $product->category->name ?? '' }}</div>
        <h3 class="product-card-name">{{ $product->name }}</h3>
        <p class="product-card-desc">{{ $product->short_description }}</p>

        @php $isWholesale = session('wholesale_user') !== null; @endphp
        <div class="price-box">
            @if($isWholesale)
                <span class="price-retail" style="color:var(--accent);">${{ number_format($product->price_wholesale, 0, ',', '.') }}</span>
                <span class="price-wholesale">Precio mayorista</span>
            @else
                <span class="price-retail">${{ number_format($product->price_retail, 0, ',', '.') }}</span>
            @endif
        </div>
    </div>

    <div class="product-card-actions">
        <a href="{{ route('product', $product->slug) }}" class="btn-card-secondary">Ver más</a>
        @if($product->isInStock())
            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf
                <button type="submit" class="btn-add-cart" title="Añadir al carrito">+</button>
            </form>
        @else
            <button class="btn-notify-me"
                onclick="openNotifyPopup('{{ $product->id }}', '{{ addslashes($product->name) }}', '{{ route('stock.alert', $product) }}')"
                title="Avisame cuando haya stock">
                🔔 Avisame
            </button>
        @endif
    </div>
</div>
