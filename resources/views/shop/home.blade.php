@extends('layouts.app')
@section('title', 'AURA33 | Cristales & Energía')

@push('preload')
<link rel="preload" href="{{ asset('img/cuarzorosa.jpg') }}" as="image">
@endpush

@section('content')

{{-- ── HERO ─────────────────────────────────────────────── --}}
<header class="hero">
    <div class="container hero-grid">

        <div class="hero-content">
            <span class="hero-eyebrow">Transformá tu energía con cristales naturales</span>
            <h1 class="hero-title">Botellas de cristal cargadas con la vibración de la Tierra.</h1>
            <p class="hero-desc">Torres, oráculos y herramientas holísticas para quienes saben y sienten que el agua y la energía cambian todo.</p>
            <a href="{{ route('shop') }}" class="btn">Ver Tienda</a>
        </div>

        <div class="hero-visual">
            <div class="hero-image-wrap">
                {{-- Si hay imagen de la amatista, se muestra; si no, placeholder hermoso --}}
                <img src="{{ asset('img/cuarzorosa.jpg') }}" alt="Botella de Cuarzo Rosa AURA33" class="hero-img" fetchpriority="high">
            </div>
            <div class="hero-badge">
                <ul>
                    <li>💧 Agua energizada</li>
                    <li>🔮 Cristales 100% naturales</li>
                    <li>✨ Envíos a todo el país</li>
                </ul>
            </div>
        </div>

    </div>
</header>

{{-- ── ORÁCULO ───────────────────────────────────────────── --}}
<section class="oracle-section" id="oraculo">
    <div class="oracle-bg-deco"></div>
    <div class="container">
        <div class="oracle-center">

            <div class="oracle-header-area">
                <span class="oracle-glow-icon">🔮</span>
                <h2 class="oracle-title">¿Qué cristal necesitás hoy?</h2>
                <p class="oracle-desc">
                    Contame cómo te sentís y nuestro oráculo encuentra la botella perfecta para este momento de tu vida.
                </p>
            </div>

            {{-- Formulario --}}
            <div id="oracle-form-area" class="oracle-form-area">
                <div class="oracle-textarea-wrap">
                    <textarea id="oracle-input" class="oracle-textarea"
                        placeholder="Contame cómo te sentís, qué estás atravesando o qué necesitás en este momento de tu vida..."
                        rows="3" maxlength="1000"></textarea>
                </div>

                <div class="oracle-hints">
                    <span onclick="fillHint(this)">💛 Me siento ansiosa</span>
                    <span onclick="fillHint(this)">💜 Quiero protegerme</span>
                    <span onclick="fillHint(this)">🌹 Busco amor propio</span>
                    <span onclick="fillHint(this)">✨ Necesito claridad mental</span>
                    <span onclick="fillHint(this)">🌙 Quiero dormir mejor</span>
                    <span onclick="fillHint(this)">🔥 Necesito más energía</span>
                </div>

                <button id="oracle-btn" class="oracle-btn" onclick="askOracle()">
                    <span id="oracle-btn-text">✦ Encontrá mi cristal</span>
                    <span id="oracle-btn-loading" style="display:none;">
                        <span class="oracle-spinner"></span> Leyendo tu energía...
                    </span>
                </button>

                <div id="oracle-error" style="display:none;" class="oracle-error"></div>
            </div>

            {{-- Resultado --}}
            <div id="oracle-result" style="display:none;" class="oracle-result-area">
                <p class="oracle-result-intro">✨ El oráculo encontró tu cristal</p>
                <div class="oracle-result-card">
                    <img id="oracle-img" src="" alt="" class="oracle-result-img">
                    <div class="oracle-result-info">
                        <span class="oracle-result-cat" id="oracle-cat"></span>
                        <h3 class="oracle-result-name" id="oracle-name"></h3>
                        <p class="oracle-result-frase" id="oracle-frase"></p>
                        <p class="oracle-result-razon" id="oracle-razon"></p>
                        <a id="oracle-link" href="#" class="oracle-result-btn">Ver mi cristal →</a>
                    </div>
                </div>
                <button onclick="resetOracle()" class="oracle-retry-btn">Consultar de nuevo</button>
            </div>

        </div>
    </div>
</section>

{{-- ── COLECCIONES ──────────────────────────────────────── --}}
<section class="section-pad">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Nuestras Líneas</span>
            <h2 class="section-title">Colecciones del Universo</h2>
            <div class="divider"></div>
        </div>

        <div class="collections-grid">
            @foreach($categories as $cat)
            <a href="{{ route('shop.category', $cat->slug) }}" class="collection-card">
                <span class="collection-icon">{{ $cat->icon }}</span>
                <h3 class="collection-name">{{ $cat->name }}</h3>
                <p class="collection-count">{{ $cat->products_count }} {{ $cat->products_count == 1 ? 'Producto' : 'Productos' }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── DESTACADOS ───────────────────────────────────────── --}}
@if($featuredProducts->count())
<section class="section-pad" style="background-color:#fbf9f6; border-top:1px solid rgba(74,59,82,0.05);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Lo más elegido</span>
            <h2 class="section-title">Productos Destacados</h2>
            <div class="divider"></div>
            <p style="color:var(--muted); font-size:0.95rem; margin-top:14px; font-weight:300;">
                Descubrí las piedras que más energía están irradiando hoy
            </p>
        </div>

        <div class="products-grid">
            @foreach($featuredProducts as $product)
                @include('shop._product_card', ['product' => $product])
            @endforeach
        </div>

        <div style="text-align:center; margin-top:50px;">
            <a href="{{ route('shop') }}" class="view-all-link">
                Ver todos los productos →
            </a>
        </div>
    </div>
</section>
@endif

{{-- ── PROPÓSITOS ───────────────────────────────────────── --}}
<section class="section-pad features-section">
    <div class="container features-grid">
        <div class="feature-item">
            <span class="feature-icon">💎</span>
            <h3 class="feature-title">Cristales Naturales</h3>
            <p class="feature-desc">Todas nuestras piedras son naturales, certificadas y energizadas con intenciones puras.</p>
        </div>
        <div class="feature-item">
            <span class="feature-icon">🚚</span>
            <h3 class="feature-title">Envío a Todo el País</h3>
            <p class="feature-desc">Despachamos por correo certificado a toda Argentina. Gratis en compras mayores a $30.000.</p>
        </div>
        <div class="feature-item">
            <span class="feature-icon">🌙</span>
            <h3 class="feature-title">Información Energética</h3>
            <p class="feature-desc">Cada pieza incluye su bitácora: chakra asignado, beneficios áuricos y modo de limpieza.</p>
        </div>
        <div class="feature-item">
            <span class="feature-icon">🏷️</span>
            <h3 class="feature-title">Precios Mayoristas</h3>
            <p class="feature-desc">Módulos especiales para revendedores, tiendas místicas y terapeutas holísticos.</p>
        </div>
    </div>
</section>

{{-- ── MAYORISTAS ────────────────────────────────────────── --}}
<section class="wholesale-home-section" id="mayoristas">
    <div class="container">
        <div class="wholesale-home-grid">

            <div class="wholesale-home-content">
                <span class="section-subtitle" style="color:rgba(255,255,255,0.7);">Para revendedores y profesionales</span>
                <h2 class="wholesale-home-title">¿Querés ser mayorista?</h2>
                <p class="wholesale-home-desc">
                    Trabajamos con terapeutas holísticos, tiendas místicas, revendedores y espacios de bienestar.
                    Accedé a precios especiales, prioridad de stock y soporte personalizado.
                </p>
                <ul class="wholesale-home-benefits">
                    <li>✦ Precios hasta un 35% menores al público</li>
                    <li>✦ Stock reservado para clientes mayoristas</li>
                    <li>✦ Atención directa y envíos coordinados</li>
                    <li>✦ Sin mínimo de pedido inicial</li>
                </ul>
                <div class="wholesale-home-actions">
                    <a href="{{ route('wholesale.register') }}" class="btn-wholesale-primary">Solicitar cuenta mayorista</a>
                    <a href="{{ route('wholesale.info') }}" class="btn-wholesale-secondary">Conocer más →</a>
                </div>
                <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.1);">
                    <a href="{{ route('wholesale.login') }}" style="font-size:0.82rem; color:rgba(255,255,255,0.55); transition:color 0.2s;"
                       onmouseover="this.style.color='rgba(255,255,255,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">
                        ¿Ya sos mayorista? <strong style="color:rgba(212,175,55,0.85);">Ingresá acá →</strong>
                    </a>
                </div>
            </div>

            <div class="wholesale-home-visual">
                <div class="wholesale-badge-card">
                    <span style="font-size:3rem; display:block; margin-bottom:16px;">💎</span>
                    <p style="font-family:var(--font-serif); font-size:1.3rem; color:var(--brand); margin-bottom:6px;">Programa Mayorista</p>
                    <p style="font-size:0.8rem; color:var(--muted); margin-bottom:20px;">Registrate y en 24-48hs te respondemos</p>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <div class="ws-step"><span class="ws-step-n">1</span> Completá el formulario</div>
                        <div class="ws-step"><span class="ws-step-n">2</span> Revisamos tu solicitud</div>
                        <div class="ws-step"><span class="ws-step-n">3</span> Comprás al precio mayorista</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── CITA ─────────────────────────────────────────────── --}}
<section class="quote-section">
    <div class="container" style="text-align:center;">
        <p class="quote-text">
            "Los cristales son la memoria viva del universo. Cada uno guarda millones de años de la vibración más pura de la Tierra."
        </p>
        <span class="quote-author">Explorar la tienda ✦ Aura33</span>
        <div style="margin-top:36px;">
            <a href="{{ route('shop') }}" class="btn">Explorar la Tienda</a>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
/* ── Hero ─────────────────────────────────────── */
.hero {
    padding: 80px 0;
    background: linear-gradient(180deg, rgba(247,231,230,0.45) 0%, rgba(250,248,245,1) 100%);
}

.hero-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 56px;
    align-items: center;
}

@media (min-width: 800px) {
    .hero-grid { grid-template-columns: 1.15fr 0.85fr; }
}

.hero-content { max-width: 560px; }

.hero-eyebrow {
    display: block;
    font-family: var(--font-serif);
    font-style: italic;
    color: var(--brand-light);
    font-size: 1.15rem;
    margin-bottom: 18px;
}

.hero-title {
    font-family: var(--font-serif);
    font-size: clamp(2rem, 4vw, 2.9rem);
    line-height: 1.22;
    color: var(--brand);
    margin-bottom: 22px;
    font-weight: 400;
}

.hero-desc {
    font-size: 1.02rem;
    color: var(--muted);
    margin-bottom: 36px;
    font-weight: 300;
    line-height: 1.75;
}

.hero-visual { position: relative; display: flex; justify-content: center; }

.hero-image-wrap {
    width: 100%;
    max-width: 400px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    border: 1px solid rgba(255,255,255,0.6);
}

.hero-img { width: 100%; height: auto; display: block; }

.hero-placeholder {
    aspect-ratio: 1;
    background: linear-gradient(135deg, #f7e7e6 0%, #ede6f4 50%, #faf8f5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-placeholder-inner {
    text-align: center;
}

.hero-crystal { font-size: 5rem; display: block; margin-bottom: 1rem; }

.hero-brand-mark {
    font-family: var(--font-serif);
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    color: var(--brand);
    opacity: 0.6;
}

.hero-badge {
    position: absolute;
    bottom: -18px;
    left: 0;
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(10px);
    padding: 18px 22px;
    border-radius: 12px;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(74,59,82,0.06);
    font-size: 0.85rem;
    color: var(--brand);
}

.hero-badge ul { list-style: none; }
.hero-badge li { margin-bottom: 6px; display: flex; align-items: center; gap: 8px; font-weight: 400; }
.hero-badge li:last-child { margin-bottom: 0; }

/* ── Colecciones ──────────────────────────────── */
.section-pad { padding: 100px 0; }

.collections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 28px;
}

.collection-card {
    background: var(--white);
    border-radius: 12px;
    padding: 40px 28px;
    text-align: center;
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    cursor: pointer;
    display: block;
}

.collection-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-card);
}

.collection-icon { font-size: 2.5rem; display: block; margin-bottom: 18px; }
.collection-name { font-family: var(--font-serif); font-size: 1.35rem; color: var(--brand); margin-bottom: 6px; font-weight: 400; }
.collection-count { font-size: 0.82rem; color: var(--muted); letter-spacing: 0.04em; }

/* ── View all ─────────────────────────────────── */
.view-all-link {
    font-family: var(--font-serif);
    font-style: italic;
    font-size: 1.1rem;
    color: var(--brand);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.view-all-link:hover { color: var(--accent); gap: 14px; }

/* ── Features ─────────────────────────────────── */
.features-section {
    background: var(--white);
    border-top: 1px solid rgba(74,59,82,0.05);
    border-bottom: 1px solid rgba(74,59,82,0.05);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 40px;
}

.feature-item { text-align: center; padding: 16px; }
.feature-icon { font-size: 2rem; display: block; margin-bottom: 14px; }
.feature-title { font-family: var(--font-serif); font-size: 1.15rem; color: var(--brand); margin-bottom: 10px; font-weight: 600; }
.feature-desc { font-size: 0.87rem; color: var(--muted); font-weight: 300; line-height: 1.7; }

/* ── Quote ────────────────────────────────────── */
.quote-section {
    padding: 100px 24px;
    background: linear-gradient(rgba(74,59,82,0.025), rgba(74,59,82,0.025));
}

.quote-text {
    font-family: var(--font-serif);
    font-size: clamp(1.35rem, 3vw, 1.85rem);
    color: var(--brand);
    max-width: 800px;
    margin: 0 auto 22px;
    line-height: 1.5;
    font-style: italic;
    font-weight: 400;
}

.quote-author {
    display: block;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: var(--brand-light);
    font-weight: 600;
    margin-bottom: 6px;
}

/* ── Oráculo ───────────────────────────────────────── */
.oracle-section {
    padding: 100px 0;
    background: linear-gradient(160deg, #fdf6f0 0%, #f5eef8 50%, #fdf6f0 100%);
    position: relative;
    overflow: hidden;
    border-top: 1px solid rgba(74,59,82,0.06);
    border-bottom: 1px solid rgba(74,59,82,0.06);
}

.oracle-bg-deco {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 50% 60% at 10% 40%, rgba(212,175,55,0.06) 0%, transparent 60%),
        radial-gradient(ellipse 40% 50% at 90% 60%, rgba(124,106,135,0.08) 0%, transparent 60%);
    pointer-events: none;
}

.oracle-center {
    max-width: 680px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 1;
}

.oracle-header-area { margin-bottom: 44px; }

.oracle-glow-icon {
    font-size: 3rem;
    display: block;
    margin-bottom: 20px;
    filter: drop-shadow(0 0 20px rgba(212,175,55,0.4));
    animation: float 3.5s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

.oracle-title {
    font-family: var(--font-serif);
    font-size: clamp(1.9rem, 3.5vw, 2.7rem);
    color: var(--brand);
    font-weight: 400;
    line-height: 1.2;
    margin-bottom: 16px;
}

.oracle-desc {
    font-size: 1rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.8;
}

/* Formulario */
.oracle-form-area { text-align: left; }

.oracle-textarea-wrap {
    background: var(--white);
    border: 1.5px solid rgba(74,59,82,0.12);
    border-radius: 16px;
    overflow: hidden;
    transition: border-color 0.25s, box-shadow 0.25s;
    box-shadow: var(--shadow-soft);
    margin-bottom: 16px;
}

.oracle-textarea-wrap:focus-within {
    border-color: var(--brand-light);
    box-shadow: 0 0 0 3px rgba(74,59,82,0.07);
}

.oracle-textarea {
    width: 100%;
    background: transparent;
    border: none;
    padding: 20px 22px;
    font-family: var(--font-sans);
    font-size: 0.97rem;
    font-weight: 300;
    color: var(--text);
    resize: none;
    outline: none;
    line-height: 1.8;
}

.oracle-textarea::placeholder {
    color: rgba(110,100,115,0.4);
    font-style: italic;
}

.oracle-hints {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin-bottom: 24px;
}

.oracle-hints span {
    font-size: 0.8rem;
    color: var(--brand);
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.12);
    padding: 7px 14px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 400;
    box-shadow: var(--shadow-soft);
}

.oracle-hints span:hover {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
    transform: translateY(-1px);
}

.oracle-btn {
    width: 100%;
    background: linear-gradient(135deg, var(--brand) 0%, #6b5278 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 16px;
    font-size: 0.92rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    cursor: pointer;
    font-family: var(--font-sans);
    transition: all 0.25s;
    box-shadow: 0 4px 18px rgba(74,59,82,0.25);
}

.oracle-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 26px rgba(74,59,82,0.32);
}

.oracle-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.oracle-spinner {
    display: inline-block;
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    vertical-align: middle;
    margin-right: 6px;
}

@keyframes spin { to { transform: rotate(360deg); } }

.oracle-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 0.85rem;
    margin-top: 14px;
    text-align: center;
}

/* Resultado */
.oracle-result-area {
    animation: fadeUp 0.5s ease;
    text-align: left;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

.oracle-result-intro {
    font-family: var(--font-serif);
    font-style: italic;
    color: var(--accent);
    font-size: 1rem;
    text-align: center;
    margin-bottom: 18px;
    letter-spacing: 0.03em;
}

.oracle-result-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    border: 1px solid rgba(74,59,82,0.07);
    display: flex;
    gap: 0;
    margin-bottom: 14px;
    min-height: 180px;
}

.oracle-result-img {
    width: 130px;
    min-height: 180px;
    object-fit: cover;
    flex-shrink: 0;
    background: linear-gradient(160deg, #f7e7e6, #ede6f4);
}

.oracle-result-info {
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    gap: 7px;
    flex: 1;
    min-width: 0;
}

.oracle-result-cat {
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--accent);
}

.oracle-result-name {
    font-family: var(--font-serif);
    font-size: 1.25rem;
    color: var(--brand);
    font-weight: 400;
    line-height: 1.2;
}

.oracle-result-frase {
    font-family: var(--font-serif);
    font-style: italic;
    font-size: 0.82rem;
    color: var(--brand-light);
    border-left: 2px solid var(--accent);
    padding-left: 9px;
    line-height: 1.5;
    margin: 2px 0;
}

.oracle-result-razon {
    font-size: 0.83rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.7;
}

.oracle-result-btn {
    display: inline-block;
    align-self: flex-start;
    margin-top: 4px;
    background: var(--brand);
    color: #fff !important;
    padding: 9px 20px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 600;
    transition: all 0.2s;
    white-space: nowrap;
}

.oracle-result-btn:hover {
    background: #3a2d42;
    transform: translateY(-1px);
}

.oracle-retry-btn {
    display: block;
    width: 100%;
    background: transparent;
    border: 1px solid rgba(74,59,82,0.15);
    border-radius: 50px;
    font-size: 0.82rem;
    color: var(--muted);
    cursor: pointer;
    font-family: var(--font-sans);
    text-align: center;
    padding: 10px;
    transition: all 0.2s;
}

.oracle-retry-btn:hover {
    border-color: var(--brand);
    color: var(--brand);
}

/* ── Mayoristas home ───────────────────────────────── */
.wholesale-home-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #4a3b52 0%, #2f2539 100%);
    position: relative;
    overflow: hidden;
}

.wholesale-home-section::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.wholesale-home-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 70px;
    align-items: center;
}

@media (max-width: 800px) {
    .wholesale-home-grid { grid-template-columns: 1fr; gap: 48px; }
}

.wholesale-home-title {
    font-family: var(--font-serif);
    font-size: clamp(2rem, 3.5vw, 2.8rem);
    color: #fff;
    font-weight: 400;
    margin: 10px 0 18px;
    line-height: 1.2;
}

.wholesale-home-desc {
    color: rgba(255,255,255,0.75);
    font-size: 1rem;
    font-weight: 300;
    line-height: 1.8;
    margin-bottom: 28px;
}

.wholesale-home-benefits {
    list-style: none;
    margin-bottom: 38px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.wholesale-home-benefits li {
    color: rgba(255,255,255,0.88);
    font-size: 0.9rem;
    font-weight: 300;
    display: flex;
    align-items: center;
    gap: 10px;
}

.wholesale-home-actions {
    display: flex;
    gap: 18px;
    align-items: center;
    flex-wrap: wrap;
}

.btn-wholesale-primary {
    background: var(--accent);
    color: #2f2539;
    padding: 14px 28px;
    border-radius: 50px;
    font-size: 0.88rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    transition: all 0.25s;
    white-space: nowrap;
}

.btn-wholesale-primary:hover {
    background: #e8c44a;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(212,175,55,0.35);
}

.btn-wholesale-secondary {
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
    font-style: italic;
    font-family: var(--font-serif);
    transition: color 0.2s;
}

.btn-wholesale-secondary:hover { color: #fff; }

.wholesale-badge-card {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 20px;
    padding: 36px 32px;
    backdrop-filter: blur(10px);
    text-align: center;
}

.ws-step {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.88rem;
    color: rgba(255,255,255,0.82);
    font-weight: 300;
    background: rgba(255,255,255,0.05);
    padding: 10px 16px;
    border-radius: 8px;
}

.ws-step-n {
    width: 26px; height: 26px;
    background: var(--accent);
    color: #2f2539;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script>
// Contador de caracteres
document.getElementById('oracle-input').addEventListener('input', function() {
    document.getElementById('oracle-count').textContent = this.value.length;
});

function askOracle() {
    const input = document.getElementById('oracle-input');
    const msg = input.value.trim();
    if (!msg || msg.length < 10) {
        input.focus();
        input.style.borderColor = '#e05050';
        setTimeout(() => input.style.borderColor = '', 1500);
        return;
    }

    const btn = document.getElementById('oracle-btn');
    document.getElementById('oracle-btn-text').style.display = 'none';
    document.getElementById('oracle-btn-loading').style.display = 'inline';
    btn.disabled = true;
    document.getElementById('oracle-result').style.display = 'none';
    document.getElementById('oracle-error').style.display = 'none';

    fetch('{{ route("crystal.recommend") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ message: msg }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) throw new Error(data.error);

        document.getElementById('oracle-frase').textContent = '"' + data.frase + '"';
        document.getElementById('oracle-name').textContent = data.product.nombre;
        document.getElementById('oracle-cat').textContent = data.product.categoria;
        document.getElementById('oracle-razon').textContent = data.razon;
        document.getElementById('oracle-link').href = data.product.url;

        const img = document.getElementById('oracle-img');
        if (data.product.imagen) {
            img.src = data.product.imagen;
            img.alt = data.product.nombre;
            img.style.display = 'block';
        } else {
            img.style.display = 'none';
        }

        document.getElementById('oracle-form-area').style.display = 'none';
        document.getElementById('oracle-result').style.display = 'block';
        document.getElementById('oracle-result').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    })
    .catch(err => {
        document.getElementById('oracle-error').textContent = err.message || 'Error al consultar el oráculo.';
        document.getElementById('oracle-error').style.display = 'block';
    })
    .finally(() => {
        document.getElementById('oracle-btn-text').style.display = 'inline';
        document.getElementById('oracle-btn-loading').style.display = 'none';
        btn.disabled = false;
    });
}

function fillHint(el) {
    const text = el.textContent.replace(/^[\p{Emoji}\s]+/u, '').trim();
    document.getElementById('oracle-input').value = text;
    document.getElementById('oracle-input').focus();
}

function resetOracle() {
    document.getElementById('oracle-result').style.display = 'none';
    document.getElementById('oracle-form-area').style.display = 'block';
    document.getElementById('oracle-input').value = '';
    document.getElementById('oracle-count').textContent = '0';
    document.getElementById('oracle-error').style.display = 'none';
    document.getElementById('oracle-input').focus();
}

// Ctrl+Enter para enviar
document.getElementById('oracle-input').addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') askOracle();
});
</script>
@endpush
