@extends('layouts.app')

@section('title', 'Carrito — AURA33')

@section('content')
<div style="padding: 4rem 0;">
    <div class="container">

        <div class="section-header">
            <p class="section-label">✦ Tu selección ✦</p>
            <h1 class="section-title">Carrito</h1>
            <div class="divider"></div>
        </div>

        @if($items->count())
        <div class="cart-layout">

            {{-- Items --}}
            <div class="cart-items">
                @php $isWholesale = session('wholesale_user') !== null; @endphp
                @foreach($items as $item)
                @php $qty = session('cart')[$item->id] ?? 0; $cartPrice = $isWholesale ? $item->price_wholesale : $item->price_retail; @endphp
                <div class="cart-item">
                    <div class="cart-item-image">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                        @else
                            <span style="font-size:2.5rem;">{{ $item->category->icon ?? '🔮' }}</span>
                        @endif
                    </div>
                    <div class="cart-item-info">
                        <div style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--purple2);">{{ $item->category->name ?? '' }}</div>
                        <a href="{{ route('product', $item->slug) }}" style="font-family:'Cormorant Garamond',serif; font-size:1.3rem; color:var(--text);">{{ $item->name }}</a>
                        <div style="color:var(--gold); font-size:1rem; margin-top:0.3rem;">${{ number_format($cartPrice, 0, ',', '.') }}</div>
                    </div>
                    <div class="cart-item-qty">
                        <form action="{{ route('cart.update', $item) }}" method="POST" style="display:flex; align-items:center; gap:0.5rem;">
                            @csrf @method('PATCH')
                            <button type="button" onclick="let i=this.nextElementSibling; i.value=Math.max(0,+i.value-1); this.closest('form').submit();" class="qty-btn-sm">−</button>
                            <input type="number" name="quantity" value="{{ $qty }}" min="0" max="{{ $item->stock }}" class="qty-input-sm" onchange="this.closest('form').submit()">
                            <button type="button" onclick="let i=this.previousElementSibling; i.value=Math.min({{ $item->stock }},+i.value+1); this.closest('form').submit();" class="qty-btn-sm">+</button>
                        </form>
                    </div>
                    <div class="cart-item-subtotal">
                        ${{ number_format($cartPrice * $qty, 0, ',', '.') }}
                    </div>
                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="cart-remove-btn" title="Quitar">✕</button>
                    </form>
                </div>
                @endforeach
            </div>

            {{-- Resumen --}}
            <div class="cart-summary">
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; margin-bottom:1.5rem;">Resumen</h2>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Envío</span>
                    @if($totals['free_shipping'])
                        <span style="color:#7ee8a2">Gratis ✓</span>
                    @else
                        <span style="color:var(--muted); font-size:0.85rem;">A calcular en el próximo paso</span>
                    @endif
                </div>

                @if(!$totals['free_shipping'])
                <div style="font-size:0.78rem; color:var(--muted); margin: 0.5rem 0 1rem; padding: 0.7rem; background:rgba(201,168,76,0.07); border-radius:4px; border:1px solid rgba(201,168,76,0.15);">
                    El costo de envío se calcula según tu provincia en el checkout
                </div>
                @endif

                <div class="divider" style="margin: 1rem 0;"></div>

                <div class="summary-row total-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('checkout') }}" class="btn btn-gold" style="width:100%; margin-top:1.5rem; padding:1rem;">
                    Finalizar Pedido →
                </a>

                <a href="{{ route('shop') }}" class="btn btn-ghost" style="width:100%; margin-top:0.8rem; padding:0.8rem; font-size:0.82rem;">
                    ← Seguir comprando
                </a>

                <div style="margin-top:1.5rem; font-size:0.78rem; color:var(--muted); text-align:center; line-height:1.7;">
                    Pago por transferencia bancaria<br>
                    Envíos a todo Argentina
                </div>
            </div>

        </div>

        @else
        <div style="text-align:center; padding:5rem 2rem;">
            <div style="font-size:4rem; margin-bottom:1.5rem;">🛒</div>
            <h2 style="font-family:'Cormorant Garamond',serif; font-size:2rem; color:var(--text); margin-bottom:1rem;">Tu carrito está vacío</h2>
            <p style="color:var(--muted); margin-bottom:2rem;">Explorá nuestra tienda y encontrá el cristal que vibra con vos.</p>
            <a href="{{ route('shop') }}" class="btn btn-gold">Explorar Tienda</a>
        </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
.cart-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 3rem;
    align-items: start;
}

@media (max-width: 900px) {
    .cart-layout { grid-template-columns: 1fr; }
}

.cart-items { display: flex; flex-direction: column; gap: 1rem; }

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 1.2rem;
    align-items: center;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1.2rem;
}

@media (max-width: 600px) {
    .cart-item { grid-template-columns: 60px 1fr; }
}

.cart-item-image {
    width: 80px;
    height: 80px;
    border-radius: 6px;
    overflow: hidden;
    background: var(--darker);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-item-image img { width: 100%; height: 100%; object-fit: cover; }

.cart-item-qty { display: flex; }

.qty-btn-sm {
    width: 28px;
    height: 28px;
    background: var(--darker);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-input-sm {
    width: 48px;
    background: var(--darker);
    border: 1px solid var(--border);
    border-radius: 4px;
    color: var(--text);
    text-align: center;
    padding: 0.2rem;
    font-size: 0.9rem;
    -moz-appearance: textfield;
}

.qty-input-sm::-webkit-inner-spin-button { -webkit-appearance: none; }

.cart-item-subtotal {
    font-size: 1rem;
    font-weight: 500;
    color: var(--gold);
    min-width: 80px;
    text-align: right;
}

.cart-remove-btn {
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0.3rem;
    transition: color 0.2s;
}
.cart-remove-btn:hover { color: #f4a0a0; }

.cart-summary {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 2rem;
    position: sticky;
    top: 90px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.92rem;
    color: var(--muted);
    padding: 0.5rem 0;
}

.total-row {
    font-size: 1.2rem;
    font-weight: 500;
    color: var(--text);
}
</style>
@endpush
