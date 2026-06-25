@extends('layouts.app')
@section('title', $product->name . ' — AURA33')
@section('description', $product->short_description)

@section('content')
<div style="padding: 60px 0 100px;">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Inicio</a>
            <span>›</span>
            <a href="{{ route('shop') }}">Tienda</a>
            @if($product->category)
            <span>›</span>
            <a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a>
            @endif
            <span>›</span>
            <span style="color:var(--brand)">{{ $product->name }}</span>
        </nav>

        <div class="product-detail-grid">

            {{-- Imagen --}}
            <div class="product-detail-image">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <div class="detail-placeholder">
                        <span>{{ $product->category->icon ?? '🔮' }}</span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="product-detail-info">
                @if($product->category)
                    <span class="product-card-cat" style="font-size:0.8rem; margin-bottom:8px; display:block;">
                        {{ $product->category->icon }} {{ $product->category->name }}
                    </span>
                @endif

                <h1 class="product-detail-name">{{ $product->name }}</h1>

                @if($product->short_description)
                    <p class="product-detail-short">{{ $product->short_description }}</p>
                @endif

                <div class="price-box" style="margin: 28px 0 8px;">
                    @php $isWholesale = session('wholesale_user') !== null; @endphp
                    <span class="price-retail" style="font-size:1.6rem;">${{ number_format($isWholesale ? $product->price_wholesale : $product->price_retail, 0, ',', '.') }}</span>
                    @if($isWholesale)
                        <span class="price-wholesale">Precio mayorista</span>
                    @endif
                </div>

                @if($product->isInStock())
                    <p style="font-size:0.82rem; color:#5a9a5a; margin-bottom:24px; font-weight:500;">● En stock</p>
                @else
                    <p style="font-size:0.82rem; color:#c05050; margin-bottom:24px; font-weight:500;">✗ Sin stock</p>
                @endif

                @if($product->isInStock())
                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                        <label style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--muted); font-weight:500;">Cantidad</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <button type="button" class="qty-btn" onclick="let i=this.nextElementSibling; if(+i.value>1) i.value=+i.value-1">−</button>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="qty-input">
                            <button type="button" class="qty-btn" onclick="let i=this.previousElementSibling; if(+i.value<{{ $product->stock }}) i.value=+i.value+1">+</button>
                        </div>
                    </div>
                    <button type="submit" class="btn" style="width:100%; padding:16px; font-size:0.9rem;">
                        Añadir al Carrito
                    </button>
                </form>
                @else
                <div class="stock-alert-box">
                    <p class="stock-alert-title">🔔 Avisame cuando vuelva</p>
                    <p class="stock-alert-desc">Dejá tu email y te avisamos cuando este cristal esté disponible nuevamente.</p>
                    @if(session('stock_alert_ok'))
                        <div class="stock-alert-success">{{ session('stock_alert_ok') }}</div>
                    @else
                    <form action="{{ route('stock.alert', $product) }}" method="POST" class="stock-alert-form">
                        @csrf
                        <input type="email" name="email" placeholder="tu@email.com" class="stock-alert-input" required>
                        <button type="submit" class="stock-alert-btn">Avisame ✨</button>
                    </form>
                    @if($errors->has('email'))
                        <p style="color:#c05050; font-size:0.78rem; margin-top:6px;">{{ $errors->first('email') }}</p>
                    @endif
                    @endif
                </div>
                @endif

                {{-- Propiedades --}}
                @if($product->properties && is_array($product->properties))
                <div class="properties-block">
                    @if(isset($product->properties['chakra']))
                    <div class="prop-row">
                        <span class="prop-label">Chakra</span>
                        <span class="prop-val">{{ $product->properties['chakra'] }}</span>
                    </div>
                    @endif

                    @if(isset($product->properties['beneficios']) && is_array($product->properties['beneficios']))
                    <div class="prop-row" style="align-items:flex-start;">
                        <span class="prop-label">Beneficios</span>
                        <ul class="benefits-chips">
                            @foreach($product->properties['beneficios'] as $b)
                                <li>{{ $b }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($product->properties['combina_con']))
                    <div class="prop-row">
                        <span class="prop-label">Combina con</span>
                        <span class="prop-val">{{ is_array($product->properties['combina_con']) ? implode(', ', $product->properties['combina_con']) : $product->properties['combina_con'] }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Descripción completa --}}
        @if($product->description)
        <div class="detail-description">
            <h2 style="font-family:var(--font-serif); font-size:1.8rem; color:var(--brand); margin-bottom:20px; font-weight:400;">Descripción</h2>
            <div class="rich-text">{!! $product->description !!}</div>
        </div>
        @endif

        {{-- Relacionados --}}
        @if($related->count())
        <div style="margin-top:80px;">
            <div class="section-header">
                <span class="section-subtitle">También te puede interesar</span>
                <h2 class="section-title">Productos Relacionados</h2>
                <div class="divider"></div>
            </div>
            <div class="products-grid">
                @foreach($related as $r)
                    @include('shop._product_card', ['product' => $r])
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
.breadcrumb {
    font-size: 0.82rem;
    color: var(--muted);
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.breadcrumb a { color: var(--muted); transition: color 0.2s; }
.breadcrumb a:hover { color: var(--brand); }

.product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 70px;
    align-items: start;
    margin-bottom: 70px;
}

@media (max-width: 768px) {
    .product-detail-grid { grid-template-columns: 1fr; gap: 36px; }
}

.product-detail-image {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    border: 1px solid rgba(255,255,255,0.7);
    background: linear-gradient(135deg, #f7e7e6, #faf8f5);
}

.product-detail-image img { width: 100%; display: block; }

.detail-placeholder {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8rem;
    background: linear-gradient(135deg, #f7e7e6 0%, #ede6f4 50%, #faf8f5 100%);
}

.product-detail-name {
    font-family: var(--font-serif);
    font-size: 2.4rem;
    color: var(--brand);
    font-weight: 400;
    line-height: 1.2;
    margin-bottom: 14px;
}

.product-detail-short {
    font-size: 1.05rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.75;
}

.qty-btn {
    width: 32px; height: 32px;
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.18);
    color: var(--brand);
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: border-color 0.2s;
    display: flex; align-items: center; justify-content: center;
}

.qty-btn:hover { border-color: var(--brand); }

.qty-input {
    width: 56px;
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.18);
    border-radius: 4px;
    color: var(--text);
    text-align: center;
    padding: 4px;
    font-size: 0.95rem;
    -moz-appearance: textfield;
    font-family: var(--font-sans);
}
.qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }

.properties-block {
    margin-top: 30px;
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 12px;
    padding: 20px 22px;
    box-shadow: var(--shadow-soft);
}

.prop-row {
    display: flex;
    gap: 16px;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(74,59,82,0.05);
}

.prop-row:last-child { border-bottom: none; }

.prop-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--accent);
    min-width: 85px;
}

.prop-val { font-size: 0.9rem; color: var(--brand); font-weight: 400; }

.benefits-chips {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.benefits-chips li {
    font-size: 0.78rem;
    color: var(--brand-light);
    background: rgba(74,59,82,0.06);
    padding: 4px 12px;
    border-radius: 50px;
    border: 1px solid rgba(74,59,82,0.08);
}

.detail-description {
    padding-top: 60px;
    border-top: 1px solid rgba(74,59,82,0.06);
}

.rich-text {
    font-size: 1rem;
    color: var(--muted);
    line-height: 1.9;
    font-weight: 300;
    max-width: 720px;
}

.stock-alert-box {
    background: linear-gradient(135deg, #fdf8ff, #f5f0fa);
    border: 1px solid rgba(74,59,82,0.1);
    border-radius: 14px;
    padding: 22px 20px;
}
.stock-alert-title {
    font-family: var(--font-serif);
    font-size: 1.05rem;
    color: var(--brand);
    margin-bottom: 6px;
}
.stock-alert-desc {
    font-size: 0.82rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.6;
    margin-bottom: 14px;
}
.stock-alert-form {
    display: flex;
    gap: 8px;
}
.stock-alert-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid rgba(74,59,82,0.18);
    border-radius: 8px;
    font-family: var(--font-sans);
    font-size: 0.85rem;
    background: var(--white);
    color: var(--text);
    outline: none;
}
.stock-alert-input:focus { border-color: var(--brand); }
.stock-alert-btn {
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    font-family: var(--font-sans);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}
.stock-alert-btn:hover { background: #3a2d42; }
.stock-alert-success {
    background: rgba(90,154,90,0.1);
    border: 1px solid rgba(90,154,90,0.25);
    color: #3a6e3a;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.85rem;
}
@media (max-width: 480px) {
    .stock-alert-form { flex-direction: column; }
}
</style>
@endpush
