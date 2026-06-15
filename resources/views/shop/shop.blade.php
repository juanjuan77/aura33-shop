@extends('layouts.app')
@section('title', isset($category) ? $category->name . ' — AURA33' : 'Tienda — AURA33')

@section('content')
<div style="padding: 80px 0;">
    <div class="container">

        <div class="section-header">
            <span class="section-subtitle">✦ Cristales Naturales ✦</span>
            <h1 class="section-title">
                @isset($category)
                    {{ $category->icon }} {{ $category->name }}
                @else
                    Todos los Productos
                @endisset
            </h1>
            <div class="divider"></div>
            @isset($category)
                @if($category->description)
                    <p style="color:var(--muted); font-size:0.95rem; margin-top:14px; font-weight:300; max-width:600px; margin-left:auto; margin-right:auto;">{{ $category->description }}</p>
                @endif
            @endisset
        </div>

        {{-- Filtros --}}
        <div class="shop-filters">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('shop') }}" class="filter-chip {{ !request()->routeIs('shop.category') ? 'active' : '' }}">
                    Todos
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('shop.category', $cat->slug) }}" class="filter-chip {{ isset($category) && $category->id === $cat->id ? 'active' : '' }}">
                    {{ $cat->icon }} {{ $cat->name }}
                </a>
                @endforeach
            </div>

            <form action="{{ route('shop') }}" method="GET" style="display:flex; gap:8px;">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar cristal..." class="search-input">
                <button type="submit" class="btn" style="padding:10px 20px;">Buscar</button>
            </form>
        </div>

        @if($products->count())
            <div class="products-grid" id="products-grid">
                @foreach($products as $product)
                    @include('shop._product_card', ['product' => $product])
                @endforeach
            </div>

            @if($products->hasMorePages())
            <div id="infinite-sentinel" style="height:60px; display:flex; align-items:center; justify-content:center; margin-top:20px;">
                <div class="scroll-loader">
                    <span></span><span></span><span></span>
                </div>
            </div>
            @endif
        @else
            <div style="text-align:center; padding:80px 24px;">
                <div style="font-size:3rem; margin-bottom:16px;">🔮</div>
                <h3 style="font-family:var(--font-serif); font-size:1.5rem; color:var(--brand); margin-bottom:10px;">Sin resultados</h3>
                <p style="color:var(--muted); font-weight:300;">No encontramos productos con esos filtros.</p>
                <a href="{{ route('shop') }}" class="btn" style="margin-top:28px;">Ver todos</a>
            </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
.shop-filters {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 48px;
    padding-bottom: 28px;
    border-bottom: 1px solid rgba(74,59,82,0.07);
}

.filter-chip {
    padding: 8px 18px;
    border: 1px solid rgba(74,59,82,0.15);
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--muted);
    letter-spacing: 0.04em;
    transition: all 0.2s;
    background: transparent;
}

.filter-chip:hover, .filter-chip.active {
    border-color: var(--brand);
    color: var(--brand);
    background: rgba(74,59,82,0.04);
}

.search-input {
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.15);
    border-radius: 4px;
    padding: 10px 16px;
    color: var(--text);
    font-family: var(--font-sans);
    font-size: 0.88rem;
    font-weight: 300;
    outline: none;
    transition: border-color 0.2s;
    width: 220px;
}

.search-input:focus { border-color: var(--brand); }
.search-input::placeholder { color: var(--muted); }

.scroll-loader { display: flex; gap: 6px; }
.scroll-loader span {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--brand);
    opacity: 0.3;
    animation: loader-bounce 1.2s infinite ease-in-out;
}
.scroll-loader span:nth-child(2) { animation-delay: 0.2s; }
.scroll-loader span:nth-child(3) { animation-delay: 0.4s; }

@keyframes loader-bounce {
    0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
    40%            { opacity: 1;   transform: scale(1.2); }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const grid     = document.getElementById('products-grid');
    const sentinel = document.getElementById('infinite-sentinel');
    if (!grid || !sentinel) return;

    let page    = 2;
    let loading = false;
    let done    = false;

    const baseUrl = '{{ url()->current() }}';
    const params  = new URLSearchParams(window.location.search);

    function loadMore() {
        if (loading || done) return;
        loading = true;

        params.set('page', page);
        fetch(baseUrl + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            grid.insertAdjacentHTML('beforeend', data.html);
            page++;
            if (!data.hasMore) {
                done = true;
                sentinel.remove();
            }
        })
        .finally(() => { loading = false; });
    }

    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) loadMore();
    }, { rootMargin: '200px' });

    observer.observe(sentinel);
})();
</script>
@endpush
