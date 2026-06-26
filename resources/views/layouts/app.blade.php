<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AURA33 | Cristales & Energía')</title>
    <meta name="description" content="@yield('description', 'Botellas de cristal, torres energéticas y oráculos. Conectá con la vibración de los cristales naturales.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet"></noscript>
    @stack('preload')

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #faf8f5;
            --bg-alt:       #fbf9f6;
            --white:        #ffffff;
            --brand:        #4a3b52;
            --brand-light:  #7c6a87;
            --accent:       #d4af37;
            --rose:         #f7e7e6;
            --text:         #2c2530;
            --muted:        #6e6473;
            --border:       rgba(74,59,82,0.07);
            --shadow-soft:  0 10px 30px rgba(74,59,82,0.05);
            --shadow-card:  0 15px 35px rgba(74,59,82,0.08);
            --font-serif:   'Playfair Display', serif;
            --font-sans:    'Montserrat', sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; transition: all 0.3s ease; }
        img { max-width: 100%; height: auto; display: block; }

        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ── Announcement bar ─────────────────────── */
        .announcement-bar {
            background-color: var(--brand);
            color: #fff;
            text-align: center;
            padding: 9px 10px;
            font-size: 0.78rem;
            letter-spacing: 0.2em;
            font-weight: 500;
            text-transform: uppercase;
        }

        /* ── Navbar ───────────────────────────────── */
        .navbar {
            position: sticky;
            top: 0;
            background-color: rgba(250,248,245,0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
        }

        .navbar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 78px;
        }

        .navbar-logo {
            font-family: var(--font-serif);
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            color: var(--brand);
        }

        .nav-menu { display: flex; list-style: none; gap: 32px; }

        .nav-link {
            font-size: 0.85rem;
            font-weight: 400;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 8px 0;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 1px;
            background-color: var(--brand);
            transition: width 0.3s ease;
        }

        .nav-link:hover, .nav-link.active { color: var(--brand); }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        .cart-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--brand);
            background: rgba(74,59,82,0.06);
            padding: 10px 18px;
            border-radius: 50px;
            transition: background 0.2s;
        }

        .cart-btn:hover { background: rgba(74,59,82,0.12); }

        .nav-ws-btn {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--brand);
            border: 1px solid rgba(74,59,82,0.22);
            padding: 7px 14px;
            border-radius: 50px;
            white-space: nowrap;
            transition: all 0.2s;
            letter-spacing: 0.01em;
        }

        .nav-ws-btn:hover {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .cart-badge {
            background: var(--brand);
            color: #fff;
            border-radius: 50%;
            width: 18px; height: 18px;
            font-size: 0.68rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Flash ────────────────────────────────── */
        .flash {
            padding: 12px 24px;
            text-align: center;
            font-size: 0.88rem;
            font-weight: 500;
        }

        .flash.success { background: rgba(134,196,134,0.15); color: #3d7a3d; border-bottom: 1px solid rgba(100,180,100,0.2); }
        .flash.error   { background: rgba(210,80,80,0.1); color: #a03030; border-bottom: 1px solid rgba(210,80,80,0.15); }

        /* ── Shared ───────────────────────────────── */
        .section-subtitle {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--brand-light);
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .section-title {
            font-family: var(--font-serif);
            font-size: 2.2rem;
            color: var(--brand);
            font-weight: 400;
        }

        .section-header { text-align: center; margin-bottom: 56px; }

        .divider {
            width: 48px; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            margin: 14px auto 0;
        }

        /* ── Buttons ──────────────────────────────── */
        .btn {
            display: inline-block;
            background-color: var(--brand);
            color: #fff;
            padding: 14px 34px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            border: 1px solid var(--brand);
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: var(--font-sans);
        }

        .btn:hover {
            background-color: transparent;
            color: var(--brand);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74,59,82,0.1);
        }

        .btn-outline {
            background: transparent;
            color: var(--brand);
            border: 1px solid rgba(74,59,82,0.25);
        }

        .btn-outline:hover { background: rgba(74,59,82,0.04); border-color: var(--brand); }

        /* ── Product Card (shared) ────────────────── */
        .product-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .product-card:hover { box-shadow: var(--shadow-card); transform: translateY(-4px); }

        .product-card-image {
            position: relative;
            height: 320px;
            overflow: hidden;
            background: #fcfbfa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.25,1,0.5,1);
        }

        .product-card:hover .product-card-image img { transform: scale(1.05); }

        .product-card-placeholder {
            font-size: 5rem;
            width: 100%; height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f7e7e6 0%, #faf8f5 100%);
        }

        .product-tag {
            position: absolute;
            top: 14px; left: 14px;
            background: rgba(255,255,255,0.92);
            padding: 5px 12px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 50px;
            color: var(--brand);
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .product-card-body { padding: 22px 22px 12px; flex-grow: 1; display: flex; flex-direction: column; }

        .product-card-cat {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--brand-light);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .product-card-name {
            font-family: var(--font-serif);
            font-size: 1.35rem;
            color: var(--brand);
            margin-bottom: 7px;
            font-weight: 400;
        }

        .product-card-desc {
            font-size: 0.87rem;
            color: var(--muted);
            font-weight: 300;
            flex-grow: 1;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .price-box {
            display: flex;
            align-items: baseline;
            gap: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .price-retail { font-size: 1.3rem; font-weight: 600; color: var(--brand); }

        .price-wholesale { font-size: 0.85rem; color: var(--brand-light); }
        .price-wholesale strong { color: var(--accent); font-weight: 600; }

        .product-card-actions { display: grid; grid-template-columns: 1fr auto; gap: 10px; padding: 0 22px 22px; }

        .btn-card-secondary {
            background: transparent;
            color: var(--brand);
            border: 1px solid rgba(74,59,82,0.15);
            text-align: center;
            padding: 11px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.2s;
            font-family: var(--font-sans);
        }

        .btn-card-secondary:hover { background: rgba(74,59,82,0.04); border-color: var(--brand); }

        .btn-add-cart {
            background: var(--brand);
            color: #fff;
            border: none;
            padding: 11px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 300;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-add-cart:hover { background: var(--brand-light); }

        /* ── Products Grid ────────────────────────── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 36px;
        }

        /* ── Footer ───────────────────────────────── */
        footer {
            background-color: var(--brand);
            color: #fff;
            padding: 80px 0 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 50px;
            margin-bottom: 60px;
        }

        .footer-brand { grid-column: span 2; }

        .footer-logo {
            font-family: var(--font-serif);
            font-size: 2rem;
            letter-spacing: 0.1em;
            margin-bottom: 18px;
        }

        .footer-desc { color: rgba(255,255,255,0.65); font-weight: 300; max-width: 300px; line-height: 1.8; font-size: 0.9rem; }

        .footer-col-title {
            font-family: var(--font-serif);
            font-size: 1.15rem;
            margin-bottom: 22px;
            font-weight: 400;
            letter-spacing: 0.04em;
        }

        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 11px; }
        .footer-links a { color: rgba(255,255,255,0.65); font-weight: 300; font-size: 0.9rem; transition: all 0.2s; }
        .footer-links a:hover { color: var(--accent); padding-left: 5px; }

        .footer-bottom {
            padding-top: 28px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            color: rgba(255,255,255,0.45);
            font-size: 0.8rem;
        }

        /* ── WhatsApp float ───────────────────────── */
        .wa-float {
            position: fixed;
            bottom: 28px; right: 28px;
            width: 58px; height: 58px;
            background: #25d366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
            z-index: 999;
            transition: transform 0.3s;
        }

        .wa-float:hover { transform: scale(1.1); }

        /* ── Responsive ───────────────────────────── */
        @media (max-width: 768px) {
            .nav-menu { display: none; }
            .footer-brand { grid-column: span 1; }
            .products-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="announcement-bar">
        ✦ Energía · Cristales · Vibración ✦
    </div>

    <nav class="navbar">
        <div class="container navbar-container">
            <a href="{{ route('home') }}" class="navbar-logo">AURA33</a>

            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop*') ? 'active' : '' }}">Tienda</a></li>
                <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Nosotros</a></li>
                <li><a href="{{ route('home') }}#mayoristas" class="nav-link {{ request()->routeIs('wholesale*') ? 'active' : '' }}">Mayoristas</a></li>
            </ul>

            <div style="display:flex; align-items:center; gap:10px;">
            @if(session('wholesale_user'))
                <a href="{{ route('wholesale.portal') }}" class="nav-ws-btn">Mi panel 💎</a>
            @else
                <a href="{{ route('wholesale.login') }}" class="nav-ws-btn">Ingreso mayoristas</a>
            @endif

            <a href="{{ route('cart') }}" class="cart-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zM7.42 13l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 20.13 2H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 14.1 5.9 15 7 15h12v-2H7.42z"/>
                </svg>
                <span>Carrito</span>
                @php $cartCount = array_sum(session('cart', [])); @endphp
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
            </div>{{-- /flex wrapper --}}
        </div>
    </nav>

    @if(session('success'))
        <div class="flash success">✦ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash error">⚠ {{ session('error') }}</div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">AURA33</div>
                <p class="footer-desc">Cristales naturales que transforman tu energía diaria. Cada piedra tiene un propósito sagrado, cada vibración tiene un significado profundo.</p>
            </div>
            <div>
                <h3 class="footer-col-title">Tienda</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('shop.category', 'botellas-de-cristal') }}">Botellas de Cristal</a></li>
                    <li><a href="{{ route('shop.category', 'torres-de-cristal') }}">Torres de Cristal</a></li>
                    <li><a href="{{ route('shop.category', 'oraculos-y-tarot') }}">Oráculos y Tarot</a></li>
                </ul>
            </div>
            <div>
                <h3 class="footer-col-title">Info</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">Nosotros</a></li>
                    <li><a href="#">Envíos</a></li>
                    <li><a href="{{ route('wholesale.info') }}">Venta Mayorista</a></li>
                </ul>
            </div>
            <div>
                <h3 class="footer-col-title">Contacto</h3>
                <ul class="footer-links">
                    <li style="color:rgba(255,255,255,0.65);font-size:0.9rem;">Funes, Santa Fe, Argentina</li>
                    <li><a href="https://instagram.com/aura33.ok" target="_blank" style="color:var(--accent)">@aura33.ok</a></li>
                    <li><a href="https://wa.me/5493400441832">WhatsApp</a></li>
                </ul>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>&copy; {{ date('Y') }} AURA33 · Cristales & Energía · Todos los derechos reservados.</span>
            <span>Hecho con intención espiritual ✨</span>
        </div>
    </footer>

    <a href="https://wa.me/5493400441832" class="wa-float" target="_blank" title="Escribinos por WhatsApp">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.479 5.393 1.48 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.741-.769zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
    </a>

    @stack('scripts')

    {{-- ── Popup Avisame ───────────────────────────── --}}
    <div class="notify-overlay" id="notifyOverlay" onclick="closeNotifyPopup()"></div>
    <div class="notify-popup" id="notifyPopup" role="dialog">
        <button class="notify-popup-close" onclick="closeNotifyPopup()">✕</button>
        <div class="notify-popup-inner">
            <span class="notify-popup-icon">🔔</span>
            <h3 class="notify-popup-title">Avisame cuando vuelva</h3>
            <p class="notify-popup-product" id="notifyProductName"></p>
            <p class="notify-popup-desc">Dejá tu email y te avisamos cuando este cristal esté disponible nuevamente.</p>
            <form id="notifyForm" method="POST">
                @csrf
                <div class="notify-popup-field">
                    <input type="email" name="email" id="notifyEmail" placeholder="tu@email.com" class="notify-popup-input" required>
                    <button type="submit" class="notify-popup-btn">Avisame ✨</button>
                </div>
                <p class="notify-popup-success" id="notifySuccess" style="display:none;">¡Listo! Te avisamos cuando vuelva 🔮</p>
            </form>
        </div>
    </div>

<style>
.product-card--out { opacity: 0.88; }
.product-card-out-badge {
    position: absolute;
    top: 10px; right: 10px;
    background: rgba(44,37,48,0.75);
    color: #fff;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 4px 10px;
    border-radius: 50px;
    backdrop-filter: blur(4px);
    z-index: 2;
}
.img-out { filter: grayscale(30%); }
.btn-notify-me {
    background: transparent;
    border: 1px solid rgba(74,59,82,0.2);
    border-radius: 50px;
    color: var(--muted);
    font-family: var(--font-sans);
    font-size: 0.72rem;
    font-weight: 600;
    padding: 7px 14px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-notify-me:hover { border-color: var(--brand); color: var(--brand); }

/* Notify popup */
.notify-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(30,20,38,0.55);
    z-index: 1000;
    backdrop-filter: blur(3px);
}
.notify-overlay.active { display: block; }
.notify-popup {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    z-index: 1001;
    width: 360px;
    max-width: calc(100vw - 32px);
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(45,25,53,0.3);
    animation: notifyIn 0.3s cubic-bezier(0.34,1.56,0.64,1) forwards;
}
.notify-popup.active { display: block; }
@keyframes notifyIn {
    from { opacity:0; transform: translate(-50%,-50%) scale(0.85); }
    to   { opacity:1; transform: translate(-50%,-50%) scale(1); }
}
.notify-popup-close {
    position: absolute;
    top: 12px; right: 14px;
    background: rgba(74,59,82,0.08);
    border: none;
    color: var(--muted);
    width: 28px; height: 28px;
    border-radius: 50%;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}
.notify-popup-close:hover { background: rgba(74,59,82,0.15); }
.notify-popup-inner { padding: 32px 28px 28px; text-align: center; }
.notify-popup-icon { font-size: 2.2rem; display: block; margin-bottom: 10px; }
.notify-popup-title {
    font-family: var(--font-serif);
    font-size: 1.25rem;
    color: var(--brand);
    font-weight: 400;
    margin-bottom: 4px;
}
.notify-popup-product {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
}
.notify-popup-desc {
    font-size: 0.82rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.6;
    margin-bottom: 18px;
}
.notify-popup-field { display: flex; gap: 8px; }
.notify-popup-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid rgba(74,59,82,0.18);
    border-radius: 8px;
    font-family: var(--font-sans);
    font-size: 0.85rem;
    background: var(--bg);
    color: var(--text);
    outline: none;
}
.notify-popup-input:focus { border-color: var(--brand); }
.notify-popup-btn {
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 16px;
    font-family: var(--font-sans);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}
.notify-popup-btn:hover { background: #3a2d42; }
.notify-popup-success {
    margin-top: 12px;
    background: rgba(90,154,90,0.1);
    border: 1px solid rgba(90,154,90,0.25);
    color: #3a6e3a;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.85rem;
}
</style>

<script>
function openNotifyPopup(productId, productName, actionUrl) {
    document.getElementById('notifyProductName').textContent = productName;
    document.getElementById('notifyForm').action = actionUrl;
    document.getElementById('notifyEmail').value = '';
    document.getElementById('notifySuccess').style.display = 'none';
    document.getElementById('notifyForm').style.display = 'block';
    document.getElementById('notifyOverlay').classList.add('active');
    document.getElementById('notifyPopup').classList.add('active');
    setTimeout(() => document.getElementById('notifyEmail').focus(), 300);
}
function closeNotifyPopup() {
    document.getElementById('notifyOverlay').classList.remove('active');
    document.getElementById('notifyPopup').classList.remove('active');
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('notifyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const data = new FormData(form);
            fetch(form.action, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(() => {
                    form.style.display = 'none';
                    document.getElementById('notifySuccess').style.display = 'block';
                    setTimeout(closeNotifyPopup, 2500);
                })
                .catch(() => form.submit());
        });
    }
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNotifyPopup(); });
</script>

    {{-- ── Oráculo Flotante ─────────────────────────── --}}
    <button class="oracle-float-btn" id="oracleFloatBtn" onclick="openOraclePopup()" aria-label="Oráculo">
        <span class="oracle-float-icon">🔮</span>
        <span class="oracle-float-label">Saca un mensajito del oráculo ✨</span>
    </button>

    <div class="oracle-popup-overlay" id="oracleOverlay" onclick="closeOraclePopup()"></div>

    <div class="oracle-popup" id="oraclePopup" role="dialog" aria-modal="true">
        <button class="oracle-popup-close" onclick="closeOraclePopup()" aria-label="Cerrar">✕</button>

        {{-- Estado inicial --}}
        <div id="oraclePopupIdle" class="oracle-popup-idle">
            <p class="oracle-popup-eyebrow">✦ Oráculo AURA33 ✦</p>
            <h3 class="oracle-popup-title">Un mensaje del universo</h3>
            <p class="oracle-popup-subtitle">Respirá profundo, pensá en lo que necesitás y sacá tu carta.</p>
            <div class="oracle-popup-card-preview" id="oracleCardPreview">
                <svg viewBox="0 0 180 270" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;">
                    <defs>
                        <radialGradient id="popBg" cx="50%" cy="40%" r="60%">
                            <stop offset="0%" stop-color="#7a5a8a"/>
                            <stop offset="100%" stop-color="#2d1f35"/>
                        </radialGradient>
                        <pattern id="popStars" x="0" y="0" width="36" height="36" patternUnits="userSpaceOnUse">
                            <circle cx="4"  cy="4"  r="0.9" fill="rgba(255,220,160,0.45)"/>
                            <circle cx="22" cy="14" r="0.6" fill="rgba(255,220,160,0.3)"/>
                            <circle cx="32" cy="28" r="1.1" fill="rgba(255,220,160,0.5)"/>
                            <circle cx="12" cy="32" r="0.7" fill="rgba(255,220,160,0.3)"/>
                            <circle cx="28" cy="6"  r="0.8" fill="rgba(255,220,160,0.35)"/>
                        </pattern>
                    </defs>
                    <rect width="180" height="270" rx="14" fill="url(#popBg)"/>
                    <rect width="180" height="270" rx="14" fill="url(#popStars)"/>
                    <rect x="10" y="10" width="160" height="250" rx="10" fill="none" stroke="rgba(255,220,160,0.22)" stroke-width="1.2"/>
                    <text x="90" y="115" text-anchor="middle" font-size="46" fill="rgba(255,220,160,0.9)">🔮</text>
                    <text x="90" y="152" text-anchor="middle" font-family="Georgia,serif" font-size="11" fill="rgba(255,220,160,0.65)" letter-spacing="3">AURA33</text>
                    <text x="90" y="169" text-anchor="middle" font-family="Georgia,serif" font-size="8"  fill="rgba(255,220,160,0.4)"  letter-spacing="2">✦ ORÁCULO ✦</text>
                </svg>
            </div>
            <button class="oracle-popup-draw-btn" onclick="drawOracleCard()">
                <span>Sacar mi carta</span>
                <span style="font-size:1.1rem;">✨</span>
            </button>
        </div>

        {{-- Barajando --}}
        <div id="oracleShuffling" class="oracle-popup-idle" style="display:none;">
            <p class="oracle-popup-eyebrow">✦ Oráculo AURA33 ✦</p>
            <h3 class="oracle-popup-title">El universo elige...</h3>
            <div class="oracle-shuffle-scene">
                <div class="oracle-shuffle-card oracle-shuffle-card--3"></div>
                <div class="oracle-shuffle-card oracle-shuffle-card--2"></div>
                <div class="oracle-shuffle-card oracle-shuffle-card--1"></div>
            </div>
            <p class="oracle-popup-subtitle" style="margin-top:16px;">Respirá y conectá con tu energía...</p>
        </div>

        {{-- Carta revelada --}}
        <div id="oraclePopupResult" class="oracle-popup-result" style="display:none;">
            <p class="oracle-popup-eyebrow">✦ Tu mensaje de hoy ✦</p>
            <div class="oracle-popup-card-reveal" id="oracleCardReveal">
                <div class="oracle-reveal-card">
                    <div class="oracle-reveal-header">
                        <span id="oracleCardSymbol" class="oracle-reveal-symbol"></span>
                    </div>
                    <div class="oracle-reveal-body">
                        <p class="oracle-reveal-keyword" id="oracleCardKeyword"></p>
                        <p class="oracle-reveal-message" id="oracleCardMessage"></p>
                    </div>
                    <div class="oracle-reveal-footer">
                        <div class="oracle-reveal-deco"></div>
                        <span class="oracle-reveal-brand">✦ AURA33 ✦</span>
                    </div>
                </div>
            </div>
            <button class="oracle-popup-draw-btn oracle-popup-draw-btn--again" onclick="drawOracleCard()">
                Sacar otra carta ↺
            </button>
        </div>
    </div>

<style>
/* ── Float button ── */
.oracle-float-btn {
    position: fixed;
    bottom: 90px;
    right: 24px;
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #4a3b52, #6b4f7a);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 12px 18px 12px 14px;
    font-family: var(--font-sans);
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    cursor: pointer;
    box-shadow: 0 6px 24px rgba(74,59,82,0.4), 0 2px 8px rgba(74,59,82,0.2);
    transition: all 0.25s;
    animation: floatPulse 3s ease-in-out infinite;
}
.oracle-float-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 32px rgba(74,59,82,0.5);
}
.oracle-float-icon { font-size: 1.15rem; }
@keyframes floatPulse {
    0%,100% { box-shadow: 0 6px 24px rgba(74,59,82,0.4), 0 0 0 0 rgba(107,79,122,0); }
    50%      { box-shadow: 0 6px 24px rgba(74,59,82,0.4), 0 0 0 8px rgba(107,79,122,0.15); }
}
@media (max-width: 600px) {
    .oracle-float-label { display: none; }
    .oracle-float-btn { padding: 13px; border-radius: 50%; bottom: 80px; }
}

/* ── Overlay ── */
.oracle-popup-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(30,20,38,0.6);
    z-index: 1000;
    backdrop-filter: blur(3px);
}
.oracle-popup-overlay.active { display: block; }

/* ── Popup ── */
.oracle-popup {
    display: none;
    position: fixed;
    bottom: 160px;
    right: 24px;
    z-index: 1001;
    width: 320px;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(45,25,53,0.35), 0 4px 16px rgba(45,25,53,0.15);
    overflow: hidden;
    animation: popupIn 0.35s cubic-bezier(0.34,1.56,0.64,1);
}
.oracle-popup.active { display: block; }
@keyframes popupIn {
    from { opacity: 0; transform: scale(0.85) translateY(20px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
@media (max-width: 600px) {
    .oracle-popup {
        bottom: 0; right: 0; left: 0;
        width: 100%; border-radius: 20px 20px 0 0;
    }
}

.oracle-popup-close {
    position: absolute;
    top: 12px; right: 14px;
    background: rgba(74,59,82,0.08);
    border: none;
    color: var(--muted);
    width: 28px; height: 28px;
    border-radius: 50%;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s;
    z-index: 2;
}
.oracle-popup-close:hover { background: rgba(74,59,82,0.15); }

.oracle-popup-idle,
.oracle-popup-result {
    padding: 28px 24px 24px;
    text-align: center;
}

.oracle-popup-eyebrow {
    font-size: 0.65rem;
    letter-spacing: 0.2em;
    color: var(--accent);
    font-weight: 600;
    margin-bottom: 6px;
}

.oracle-popup-title {
    font-family: var(--font-serif);
    font-size: 1.3rem;
    color: var(--brand);
    font-weight: 400;
    margin-bottom: 6px;
}

.oracle-popup-subtitle {
    font-size: 0.8rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.6;
    margin-bottom: 18px;
}

.oracle-popup-card-preview {
    width: 120px;
    height: 180px;
    margin: 0 auto 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(45,25,53,0.25);
    animation: cardFloat 4s ease-in-out infinite;
}
@keyframes cardFloat {
    0%,100% { transform: translateY(0) rotate(-1deg); }
    50%      { transform: translateY(-6px) rotate(1deg); }
}

.oracle-popup-draw-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--brand), #6b4f7a);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 12px 24px;
    font-family: var(--font-sans);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s;
    box-shadow: 0 4px 16px rgba(74,59,82,0.3);
}
.oracle-popup-draw-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(74,59,82,0.4); }
.oracle-popup-draw-btn--again {
    background: transparent;
    color: var(--muted);
    border: 1px solid rgba(74,59,82,0.15);
    box-shadow: none;
    font-size: 0.78rem;
    margin-top: 4px;
}
.oracle-popup-draw-btn--again:hover { background: rgba(74,59,82,0.04); box-shadow: none; transform: none; }

/* ── Carta revelada ── */
.oracle-popup-card-reveal {
    margin-bottom: 16px;
}
.oracle-reveal-card {
    background: linear-gradient(160deg, #2d1f35, #4a3b52);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(45,25,53,0.3);
    animation: cardReveal 0.6s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes cardReveal {
    from { opacity: 0; transform: rotateY(90deg) scale(0.8); }
    to   { opacity: 1; transform: rotateY(0) scale(1); }
}
.oracle-reveal-header {
    padding: 18px 16px 10px;
    text-align: center;
    border-bottom: 1px solid rgba(255,220,160,0.1);
}
.oracle-reveal-symbol { font-size: 2.4rem; }
.oracle-reveal-body {
    padding: 14px 18px 16px;
}
.oracle-reveal-keyword {
    font-family: var(--font-serif);
    font-size: 1.05rem;
    color: rgba(255,220,160,0.95);
    text-align: center;
    margin-bottom: 10px;
    letter-spacing: 0.04em;
}
.oracle-reveal-message {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.78);
    font-weight: 300;
    line-height: 1.75;
    text-align: center;
    font-style: italic;
}
.oracle-reveal-footer {
    padding: 8px 16px 12px;
    text-align: center;
}
.oracle-reveal-deco {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(212,175,55,0.3), transparent);
    margin-bottom: 6px;
}
.oracle-reveal-brand {
    font-size: 0.55rem;
    letter-spacing: 0.22em;
    color: rgba(212,175,55,0.5);
}

/* ── Shuffle animation ── */
.oracle-shuffle-scene {
    position: relative;
    width: 120px;
    height: 180px;
    margin: 0 auto;
}
.oracle-shuffle-card {
    position: absolute;
    inset: 0;
    border-radius: 10px;
    background: linear-gradient(160deg, #4a3b52, #2d1f35);
    box-shadow: 0 6px 20px rgba(45,25,53,0.35);
    border: 1px solid rgba(255,220,160,0.15);
}
.oracle-shuffle-card::after {
    content: '🔮';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    font-size: 2rem;
    opacity: 0.6;
}
.oracle-shuffle-card--1 { animation: shuffle1 1.8s ease-in-out infinite; z-index: 3; }
.oracle-shuffle-card--2 { animation: shuffle2 1.8s ease-in-out infinite; z-index: 2; }
.oracle-shuffle-card--3 { animation: shuffle3 1.8s ease-in-out infinite; z-index: 1; }

@keyframes shuffle1 {
    0%   { transform: translateX(0)    rotate(0deg); }
    20%  { transform: translateX(-30px) rotate(-12deg); }
    50%  { transform: translateX(30px)  rotate(10deg); }
    80%  { transform: translateX(-15px) rotate(-6deg); }
    100% { transform: translateX(0)    rotate(0deg); }
}
@keyframes shuffle2 {
    0%   { transform: translateX(0)    rotate(3deg); }
    20%  { transform: translateX(28px)  rotate(14deg); }
    50%  { transform: translateX(-25px) rotate(-8deg); }
    80%  { transform: translateX(18px)  rotate(8deg); }
    100% { transform: translateX(0)    rotate(3deg); }
}
@keyframes shuffle3 {
    0%   { transform: translateX(0)    rotate(-3deg); }
    30%  { transform: translateX(20px)  rotate(8deg); }
    60%  { transform: translateX(-22px) rotate(-10deg); }
    100% { transform: translateX(0)    rotate(-3deg); }
}
</style>

<script>
const ORACLE_CARDS = [
    { symbol: '🌙', keyword: 'Confianza', message: 'El universo siempre conspira a tu favor. Suelta el control y confía en el proceso. Lo que debe llegar, llega.' },
    { symbol: '✨', keyword: 'Luz interior', message: 'Brillas incluso cuando no lo notás. Tu energía transforma los espacios que habitás.' },
    { symbol: '🌸', keyword: 'Renovación', message: 'Es tiempo de soltar lo que ya no te nutre. Cada final es el comienzo de algo más alineado con tu alma.' },
    { symbol: '🔮', keyword: 'Intuición', message: 'Tu sabiduría interior sabe el camino. Detente, respira y escucha lo que tu corazón ya conoce.' },
    { symbol: '💜', keyword: 'Amor propio', message: 'Cuidarte a vos misma/o no es egoísmo, es sabiduría. Llená tu copa antes de repartir.' },
    { symbol: '🌿', keyword: 'Calma', message: 'En el silencio encuentras las respuestas que buscás. La naturaleza nunca apura sus ciclos.' },
    { symbol: '⭐', keyword: 'Propósito', message: 'Estás exactamente donde debés estar. Cada paso, incluso los difíciles, te acerca a tu versión más auténtica.' },
    { symbol: '🦋', keyword: 'Transformación', message: 'Estás en medio de un cambio profundo. No te resistas. La mariposa no sabe que es hermosa hasta que vuela.' },
    { symbol: '🌊', keyword: 'Fluidez', message: 'Como el agua, encontrá el camino alrededor de los obstáculos. La flexibilidad es tu mayor fortaleza hoy.' },
    { symbol: '🌺', keyword: 'Abundancia', message: 'La abundancia fluye hacia quienes se sienten merecedores. Abrí los brazos y recibí lo que el universo tiene para vos.' },
    { symbol: '🕊️', keyword: 'Paz', message: 'La paz que buscás no está afuera. Está en el espacio entre un pensamiento y el siguiente.' },
    { symbol: '🌟', keyword: 'Gratitud', message: 'Lo que agradecés se multiplica. Hoy, reconocé la magia que ya existe en tu vida.' },
    { symbol: '💫', keyword: 'Manifestación', message: 'Tus pensamientos crean tu realidad. Elegí con cuidado en qué enfocás tu energía hoy.' },
    { symbol: '🌈', keyword: 'Esperanza', message: 'Después de toda tormenta viene la calma y con ella, una perspectiva nueva. El arco iris aparece cuando ya pasó la lluvia.' },
    { symbol: '🔥', keyword: 'Determinación', message: 'Tenés dentro tuyo una fuerza que aún no conocés del todo. Hoy es un buen día para descubrirla.' },
    { symbol: '🌙', keyword: 'Descanso', message: 'Honrar tu cuerpo con descanso también es productivo. Los sueños procesan lo que la mente despierta no puede.' },
    { symbol: '💎', keyword: 'Valor', message: 'Como los cristales, la presión te forma. Sos más fuerte y más valioso/a de lo que imaginás.' },
    { symbol: '🌹', keyword: 'Apertura', message: 'Ábriste a recibir amor en todas sus formas. Lo que das con genuinidad siempre vuelve multiplicado.' },
];

let lastCardIndex = -1;

function openOraclePopup() {
    document.getElementById('oracleOverlay').classList.add('active');
    document.getElementById('oraclePopup').classList.add('active');
    document.getElementById('oraclePopupIdle').style.display = 'block';
    document.getElementById('oraclePopupResult').style.display = 'none';
}

function closeOraclePopup() {
    document.getElementById('oracleOverlay').classList.remove('active');
    document.getElementById('oraclePopup').classList.remove('active');
}

function drawOracleCard() {
    let idx;
    do { idx = Math.floor(Math.random() * ORACLE_CARDS.length); } while (idx === lastCardIndex);
    lastCardIndex = idx;
    const card = ORACLE_CARDS[idx];

    // Mostrar animación de barajado
    document.getElementById('oraclePopupIdle').style.display   = 'none';
    document.getElementById('oraclePopupResult').style.display = 'none';
    document.getElementById('oracleShuffling').style.display   = 'block';

    // Después de la animación revelar la carta
    setTimeout(() => {
        document.getElementById('oracleCardSymbol').textContent  = card.symbol;
        document.getElementById('oracleCardKeyword').textContent = card.keyword;
        document.getElementById('oracleCardMessage').textContent = card.message;

        document.getElementById('oracleShuffling').style.display   = 'none';
        document.getElementById('oraclePopupResult').style.display = 'block';
    }, 1800);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOraclePopup(); });
</script>

    @stack('scripts')
</body>
</html>
