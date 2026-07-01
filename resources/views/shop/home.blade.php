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

    {{-- ── Oráculo franja ──────────────────────────────── --}}
    <div class="hero-oracle-strip">
        <div class="ho-strip-deco" aria-hidden="true">
            <span class="ho-deco-line"></span>
            <span class="ho-deco-text">✦ &nbsp; Un mensajito del oráculo &nbsp; ✦</span>
            <span class="ho-deco-line"></span>
        </div>

        <div class="ho-strip-body">

            {{-- Idle --}}
            <div class="hero-oracle-idle" id="heroOracleIdle">
                <div class="hero-oracle-sparkles" aria-hidden="true">
                    <span class="ho-spark">✦</span>
                    <span class="ho-spark">·</span>
                    <span class="ho-spark">✧</span>
                    <span class="ho-spark">✦</span>
                    <span class="ho-spark">·</span>
                    <span class="ho-spark">✧</span>
                </div>
                <div class="hero-oracle-card-wrap" onclick="heroDrawCard()" title="Sacar mi carta">
                    <svg viewBox="0 0 120 180" xmlns="http://www.w3.org/2000/svg" class="hero-oracle-cardsvg">
                        <defs>
                            <radialGradient id="hoBg" cx="50%" cy="40%" r="65%">
                                <stop offset="0%" stop-color="#7a5a8a"/>
                                <stop offset="100%" stop-color="#2d1f35"/>
                            </radialGradient>
                            <pattern id="hoStars" x="0" y="0" width="30" height="30" patternUnits="userSpaceOnUse">
                                <circle cx="3"  cy="3"  r="0.8" fill="rgba(255,220,160,0.45)"/>
                                <circle cx="18" cy="11" r="0.5" fill="rgba(255,220,160,0.3)"/>
                                <circle cx="26" cy="23" r="1"   fill="rgba(255,220,160,0.5)"/>
                                <circle cx="9"  cy="26" r="0.6" fill="rgba(255,220,160,0.3)"/>
                            </pattern>
                        </defs>
                        <rect width="120" height="180" rx="12" fill="url(#hoBg)"/>
                        <rect width="120" height="180" rx="12" fill="url(#hoStars)"/>
                        <rect x="8" y="8" width="104" height="164" rx="8" fill="none" stroke="rgba(255,220,160,0.22)" stroke-width="1.2"/>
                        <text x="60" y="82"  text-anchor="middle" font-size="36" fill="rgba(255,220,160,0.9)">🔮</text>
                        <text x="60" y="108" text-anchor="middle" font-family="Georgia,serif" font-size="8" fill="rgba(255,220,160,0.65)" letter-spacing="2.5">AURA33</text>
                        <text x="60" y="122" text-anchor="middle" font-family="Georgia,serif" font-size="6" fill="rgba(255,220,160,0.4)" letter-spacing="2">✦ ORÁCULO ✦</text>
                    </svg>
                    <div class="hero-oracle-card-hint">Tocá para sacar tu carta</div>
                </div>
            </div>

            {{-- Shuffling --}}
            <div class="hero-oracle-shuffling" id="heroOracleShuffling" style="display:none;">
                <div class="hero-oshuffle">
                    <div class="hero-oshuffle-c c3"></div>
                    <div class="hero-oshuffle-c c2"></div>
                    <div class="hero-oshuffle-c c1"></div>
                </div>
                <p class="hero-oracle-embed-sublabel">El universo elige...</p>
            </div>

            {{-- Resultado --}}
            <div class="hero-oracle-result" id="heroOracleResult" style="display:none;">
                <div class="hero-oracle-result-card">
                    <span class="hero-oracle-result-symbol" id="heroOracleSymbol"></span>
                    <div class="hero-oracle-result-text">
                        <span class="hero-oracle-result-keyword" id="heroOracleKeyword"></span>
                        <p class="hero-oracle-result-msg" id="heroOracleMsg"></p>
                        <button class="hero-oracle-again" onclick="heroResetOracle()">Sacar otra carta ✦</button>
                    </div>
                </div>
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

                <div class="oracle-card-scene">
                    <div class="oracle-card-flip" id="oracle-card-flip">
                        {{-- Dorso de la carta --}}
                        <div class="oracle-card-back">
                            <div class="oracle-card-back-inner">
                                <div class="oracle-card-back-deco">
                                    <svg viewBox="0 0 200 300" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;">
                                        <defs>
                                            <radialGradient id="bgGrad" cx="50%" cy="50%" r="60%">
                                                <stop offset="0%" stop-color="#6b4f7a"/>
                                                <stop offset="100%" stop-color="#2d1f35"/>
                                            </radialGradient>
                                            <pattern id="stars" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                                <circle cx="5" cy="5" r="1" fill="rgba(255,220,180,0.4)"/>
                                                <circle cx="25" cy="15" r="0.7" fill="rgba(255,220,180,0.3)"/>
                                                <circle cx="35" cy="30" r="1.2" fill="rgba(255,220,180,0.5)"/>
                                                <circle cx="15" cy="35" r="0.8" fill="rgba(255,220,180,0.3)"/>
                                            </pattern>
                                        </defs>
                                        <rect width="200" height="300" rx="16" fill="url(#bgGrad)"/>
                                        <rect width="200" height="300" rx="16" fill="url(#stars)"/>
                                        <rect x="12" y="12" width="176" height="276" rx="12" fill="none" stroke="rgba(255,220,180,0.25)" stroke-width="1.5"/>
                                        <rect x="18" y="18" width="164" height="264" rx="9" fill="none" stroke="rgba(255,220,180,0.12)" stroke-width="1"/>
                                        <text x="100" y="130" text-anchor="middle" font-size="52" fill="rgba(255,220,180,0.9)">🔮</text>
                                        <text x="100" y="170" text-anchor="middle" font-family="Georgia, serif" font-size="13" fill="rgba(255,220,180,0.7)" letter-spacing="3">AURA33</text>
                                        <text x="100" y="190" text-anchor="middle" font-family="Georgia, serif" font-size="9" fill="rgba(255,220,180,0.45)" letter-spacing="2">✦ ORÁCULO ✦</text>
                                        <line x1="50" y1="210" x2="150" y2="210" stroke="rgba(255,220,180,0.2)" stroke-width="0.8"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        {{-- Frente de la carta --}}
                        <div class="oracle-card-front">
                            <div class="oracle-card-front-inner">
                                <div class="oracle-card-front-header">
                                    <span class="oracle-card-eyebrow" id="oracle-cat"></span>
                                </div>
                                <div class="oracle-card-img-wrap">
                                    <img id="oracle-img" src="" alt="" class="oracle-card-crystal-img">
                                    <div class="oracle-card-img-glow"></div>
                                </div>
                                <div class="oracle-card-front-body">
                                    <h3 class="oracle-card-name" id="oracle-name"></h3>
                                    <p class="oracle-card-frase" id="oracle-frase"></p>
                                </div>
                                <div class="oracle-card-front-footer">
                                    <div class="oracle-card-deco-line"></div>
                                    <span class="oracle-card-brand">✦ AURA33 ✦</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oracle-message-area" id="oracle-message-area" style="display:none;">
                    <p class="oracle-razon-text" id="oracle-razon"></p>
                    <a id="oracle-link" href="#" class="oracle-result-btn">Ver mi cristal →</a>
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

{{-- ── LUNA ─────────────────────────────────────────────── --}}
@php
// Cálculo de fase lunar (ciclo de 29.53 días desde luna nueva conocida)
$knownNewMoon = new \DateTime('2000-01-06 18:14:00', new \DateTimeZone('UTC'));
$now          = new \DateTime('now', new \DateTimeZone('UTC'));
$diff         = $knownNewMoon->diff($now);
$daysSince    = $diff->days + $diff->h / 24;
$cycle        = 29.53058867;
$phase        = fmod($daysSince, $cycle);
$pct          = $phase / $cycle;

$lunarPhases = [
    ['name' => 'Luna Nueva',         'emoji' => '🌑', 'range' => [0,    0.063], 'energia' => 'Intención y comienzo',  'desc' => 'La oscuridad invita a sembrar nuevos propósitos. Es tiempo de intención, silencio interior y de plantar las semillas de lo que querés manifestar.', 'crystal' => 'amatista',    'crystal_name' => 'Amatista',    'crystal_why' => 'Potencia la intuición y conecta con tu propósito más profundo en este momento de oscuridad y renovación.'],
    ['name' => 'Luna Creciente',     'emoji' => '🌒', 'range' => [0.063, 0.25], 'energia' => 'Acción y crecimiento', 'desc' => 'La luz crece y con ella tu energía. Es momento de tomar acción, dar los primeros pasos y nutrir lo que comenzaste.', 'crystal' => 'citrino',      'crystal_name' => 'Citrino',      'crystal_why' => 'Piedra de la abundancia y la acción. Potencia tu motivación y atrae oportunidades mientras la luna crece.'],
    ['name' => 'Cuarto Creciente',   'emoji' => '🌓', 'range' => [0.25,  0.313],'energia' => 'Decisión y fuerza',   'desc' => 'Momento de tomar decisiones y superar obstáculos. La luna te da fuerza para sostener tus compromisos.', 'crystal' => 'ojo-de-tigre', 'crystal_name' => 'Ojo de Tigre', 'crystal_why' => 'Piedra de la determinación y la confianza. Te ayuda a mantenerte firme y seguro/a en tus decisiones.'],
    ['name' => 'Luna Gibosa Creciente','emoji'=>'🌔','range' => [0.313, 0.5],  'energia' => 'Perfeccionamiento',   'desc' => 'Estás casi en la cima. Refiná tus proyectos, observá el progreso y preparáte para la plenitud que se acerca.', 'crystal' => 'cuarzo-rosa',  'crystal_name' => 'Cuarzo Rosa',  'crystal_why' => 'Armoniza el corazón y te ayuda a recibir con gracia lo que estás construyendo.'],
    ['name' => 'Luna Llena',         'emoji' => '🌕', 'range' => [0.5,   0.563],'energia' => 'Plenitud y gratitud', 'desc' => 'La energía está en su punto más alto. Celebrá los logros, agradecé profundamente y liberá lo que ya no necesitás.', 'crystal' => 'cuarzo-rosa',  'crystal_name' => 'Cuarzo Rosa',  'crystal_why' => 'En luna llena amplifica el amor y la gratitud, ayudándote a celebrar con el corazón abierto.'],
    ['name' => 'Luna Gibosa Menguante','emoji'=>'🌖','range' => [0.563, 0.75], 'energia' => 'Gratitud y entrega',  'desc' => 'Tiempo de compartir lo aprendido, agradecer las bendiciones y comenzar a soltar con amor lo que ya cumplió su ciclo.', 'crystal' => 'amatista',    'crystal_name' => 'Amatista',    'crystal_why' => 'Facilita el proceso de soltar con sabiduría y conecta con la gratitud espiritual.'],
    ['name' => 'Cuarto Menguante',   'emoji' => '🌗', 'range' => [0.75,  0.813],'energia' => 'Soltar y limpiar',   'desc' => 'La luna invita a hacer limpieza energética: de pensamientos, vínculos y situaciones que ya no vibran con vos.', 'crystal' => 'ojo-de-tigre', 'crystal_name' => 'Ojo de Tigre', 'crystal_why' => 'Protege y despeja energías densas durante el proceso de limpieza y desapego.'],
    ['name' => 'Luna Menguante',     'emoji' => '🌘', 'range' => [0.813, 1.0],  'energia' => 'Descanso e integración','desc' => 'El ciclo llega a su fin. Descansá, integrá las lecciones y preparáte en silencio para el próximo renacer.', 'crystal' => 'amatista',    'crystal_name' => 'Amatista',    'crystal_why' => 'Acompaña el descanso profundo y la integración espiritual antes del nuevo ciclo.'],
];

$currentPhase = $lunarPhases[7]; // fallback
foreach ($lunarPhases as $p) {
    if ($pct >= $p['range'][0] && $pct < $p['range'][1]) {
        $currentPhase = $p;
        break;
    }
}

// Buscar el producto recomendado
$lunarProduct = \App\Models\Product::where('active', true)
    ->where('stock', '>', 0)
    ->where('slug', 'like', '%' . $currentPhase['crystal'] . '%')
    ->first();
if (!$lunarProduct) {
    $lunarProduct = \App\Models\Product::where('active', true)->where('stock', '>', 0)->first();
}
@endphp

<section class="lunar-section">
    <div class="lunar-stars" aria-hidden="true"></div>
    <div class="container">
        <div class="lunar-grid">

            {{-- Lado izquierdo: fase --}}
            <div class="lunar-left">
                <span class="lunar-eyebrow">✦ Calendario Lunar ✦</span>
                <div class="lunar-moon-wrap">
                    <span class="lunar-moon-emoji" id="lunarMoonEmoji">{{ $currentPhase['emoji'] }}</span>
                    <div class="lunar-moon-glow"></div>
                </div>
                <h2 class="lunar-phase-name">{{ $currentPhase['name'] }}</h2>
                <p class="lunar-energia">{{ $currentPhase['energia'] }}</p>
                <p class="lunar-desc">{{ $currentPhase['desc'] }}</p>
                <div class="lunar-progress-wrap">
                    <div class="lunar-progress-bar" style="width: {{ round($pct * 100) }}%"></div>
                </div>
                <p class="lunar-progress-label">Ciclo lunar: {{ round($pct * 100) }}% completado</p>
            </div>

            {{-- Lado derecho: cristal recomendado --}}
            @if($lunarProduct)
            <div class="lunar-right">
                <div class="lunar-crystal-card">
                    <div class="lunar-crystal-img-wrap">
                        <img src="{{ $lunarProduct->image_url }}" alt="{{ $lunarProduct->name }}" class="lunar-crystal-img">
                        <div class="lunar-crystal-badge">Cristal del momento</div>
                    </div>
                    <div class="lunar-crystal-info">
                        <span class="lunar-crystal-category">{{ $lunarProduct->category->name ?? '' }}</span>
                        <h3 class="lunar-crystal-name">{{ $lunarProduct->name }}</h3>
                        <p class="lunar-crystal-why">{{ $currentPhase['crystal_why'] }}</p>
                        <a href="{{ route('product', $lunarProduct->slug) }}" class="lunar-crystal-btn">Ver cristal →</a>
                    </div>
                </div>
            </div>
            @endif

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
/* ── Hero Oráculo Franja ─────────────────────────── */
.hero-oracle-strip {
    width: 100%;
    padding: 36px 0 44px;
    border-top: 1px solid rgba(107,79,122,0.1);
    background: linear-gradient(to bottom, rgba(107,79,122,0.04), transparent);
}
/* Cabecera decorativa con líneas */
.ho-strip-deco {
    display: flex;
    align-items: center;
    gap: 16px;
    justify-content: center;
    margin-bottom: 32px;
}
.ho-deco-line {
    flex: 1;
    max-width: 160px;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(147,51,234,0.3), transparent);
}
.ho-deco-text {
    font-family: var(--font-serif);
    font-size: 0.85rem;
    color: var(--brand);
    letter-spacing: 0.08em;
    font-style: italic;
    white-space: nowrap;
    opacity: 0.75;
}
/* Cuerpo centrado */
.ho-strip-body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}
/* Idle */
.hero-oracle-idle {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
.hero-oracle-sparkles {
    position: absolute;
    inset: -20px;
    pointer-events: none;
    overflow: visible;
}
.ho-spark {
    position: absolute;
    font-size: 1rem;
    animation: hoSparkFloat linear infinite;
    opacity: 0;
    color: rgba(147,51,234,0.7);
}
.ho-spark:nth-child(1) { left: -20px; top: 20px;  animation-duration: 3.2s; animation-delay: 0s; }
.ho-spark:nth-child(2) { left: 130px; top: 10px;  animation-duration: 4.1s; animation-delay: 1.1s; }
.ho-spark:nth-child(3) { left: -30px; top: 80px;  animation-duration: 3.7s; animation-delay: 0.6s; }
.ho-spark:nth-child(4) { left: 120px; top: 90px;  animation-duration: 4.5s; animation-delay: 1.8s; }
.ho-spark:nth-child(5) { left: 50px;  top: -15px; animation-duration: 3.9s; animation-delay: 2.3s; }
.ho-spark:nth-child(6) { left: 70px;  top: 150px; animation-duration: 3.4s; animation-delay: 0.9s; }
@keyframes hoSparkFloat {
    0%   { opacity: 0;   transform: translateY(0)    scale(0.5); }
    30%  { opacity: 0.9; transform: translateY(-10px) scale(1); }
    70%  { opacity: 0.5; transform: translateY(-22px) scale(0.8); }
    100% { opacity: 0;   transform: translateY(-34px) scale(0.3); }
}
.hero-oracle-card-wrap {
    cursor: pointer;
    width: 108px;
    position: relative;
    animation: hoFloat 3.5s ease-in-out infinite, hoGlow 3.5s ease-in-out infinite;
    transition: filter 0.3s, transform 0.3s;
}
.hero-oracle-card-wrap:hover {
    animation-play-state: paused;
    transform: scale(1.07) rotate(-3deg) !important;
    filter: drop-shadow(0 18px 40px rgba(147,51,234,0.75)) drop-shadow(0 0 16px rgba(255,220,160,0.45)) !important;
}
@keyframes hoFloat {
    0%, 100% { transform: translateY(0)     rotate(-1.5deg); }
    50%       { transform: translateY(-10px) rotate(1.5deg); }
}
@keyframes hoGlow {
    0%, 100% { filter: drop-shadow(0 10px 22px rgba(107,79,122,0.45)) drop-shadow(0 0 8px rgba(255,220,160,0.12)); }
    50%       { filter: drop-shadow(0 14px 32px rgba(147,51,234,0.6))  drop-shadow(0 0 18px rgba(255,220,160,0.28)); }
}
.hero-oracle-cardsvg { display: block; width: 100%; border-radius: 12px; }
.hero-oracle-card-hint {
    font-size: 0.72rem;
    color: var(--muted);
    text-align: center;
    margin-top: 12px;
    font-style: italic;
    opacity: 0.6;
    letter-spacing: 0.03em;
}
/* Shuffling */
.hero-oracle-shuffling {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}
.hero-oracle-embed-sublabel {
    font-size: 0.82rem;
    color: var(--muted);
    font-style: italic;
    text-align: center;
    margin: 0;
}
.hero-oshuffle {
    position: relative;
    width: 108px;
    height: 140px;
}
.hero-oshuffle-c {
    position: absolute;
    width: 80px;
    height: 116px;
    border-radius: 10px;
    background: linear-gradient(135deg, #7a5a8a, #2d1f35);
    border: 1px solid rgba(255,220,160,0.2);
    box-shadow: 0 4px 16px rgba(45,31,53,0.4);
}
.hero-oshuffle-c.c1 { top:0;   left:14px; animation: hoShuffle 0.65s ease-in-out infinite alternate; }
.hero-oshuffle-c.c2 { top:8px; left:7px;  animation: hoShuffle 0.65s 0.12s ease-in-out infinite alternate-reverse; opacity:0.7; }
.hero-oshuffle-c.c3 { top:16px;left:0;    animation: hoShuffle 0.65s 0.24s ease-in-out infinite alternate; opacity:0.45; }
@keyframes hoShuffle { from { transform: rotate(-8deg); } to { transform: rotate(8deg) translateX(12px); } }
/* Result */
.hero-oracle-result {
    animation: hoReveal 0.5s ease-out both;
    display: flex;
    align-items: center;
    justify-content: center;
}
@keyframes hoReveal { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }
.hero-oracle-result-card {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 24px 28px;
    background: linear-gradient(135deg, rgba(107,79,122,0.07), rgba(147,51,234,0.04));
    border: 1px solid rgba(147,51,234,0.15);
    border-radius: 20px;
    max-width: 440px;
    box-shadow: 0 4px 24px rgba(107,79,122,0.1);
}
.hero-oracle-result-symbol { font-size: 2.6rem; line-height: 1; flex-shrink: 0; }
.hero-oracle-result-text { display: flex; flex-direction: column; gap: 4px; }
.hero-oracle-result-keyword {
    font-family: var(--font-serif);
    font-size: 1.15rem;
    color: var(--brand);
    font-weight: 600;
    letter-spacing: 0.02em;
}
.hero-oracle-result-msg {
    font-size: 0.85rem;
    color: var(--text);
    line-height: 1.65;
    margin: 4px 0 12px;
    font-style: italic;
    opacity: 0.85;
}
.hero-oracle-again {
    background: none;
    border: none;
    padding: 0;
    font-size: 0.75rem;
    color: rgba(147,51,234,0.55);
    cursor: pointer;
    font-family: var(--font-serif);
    font-style: italic;
    transition: color 0.2s;
    letter-spacing: 0.04em;
}
.hero-oracle-again:hover { color: var(--brand); }

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
    text-align: center;
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
    margin-bottom: 24px;
    letter-spacing: 0.03em;
}

/* ── Oracle card flip ── */
.oracle-card-scene {
    perspective: 900px;
    width: 220px;
    height: 330px;
    margin: 0 auto 24px;
}

.oracle-card-flip {
    width: 100%; height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.9s cubic-bezier(0.4, 0, 0.2, 1);
}

.oracle-card-flip.flipped {
    transform: rotateY(180deg);
}

.oracle-card-back,
.oracle-card-front {
    position: absolute;
    inset: 0;
    backface-visibility: hidden;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(45,25,53,0.35), 0 2px 8px rgba(45,25,53,0.2);
}

.oracle-card-back {
    background: linear-gradient(160deg, #4a3b52, #2d1f35);
}

.oracle-card-back-inner,
.oracle-card-back-deco {
    width: 100%; height: 100%;
}

.oracle-card-front {
    transform: rotateY(180deg);
    background: var(--white);
    display: flex;
    flex-direction: column;
}

.oracle-card-front-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.oracle-card-front-header {
    background: linear-gradient(135deg, #4a3b52, #6b4f7a);
    padding: 8px 12px;
    text-align: center;
}

.oracle-card-eyebrow {
    font-size: 0.58rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: rgba(255,220,180,0.85);
}

.oracle-card-img-wrap {
    position: relative;
    flex: 1;
    overflow: hidden;
    background: linear-gradient(160deg, #f7e7e6, #ede6f4);
}

.oracle-card-crystal-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.oracle-card-img-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 50% 80%, rgba(212,175,55,0.15) 0%, transparent 70%);
    pointer-events: none;
}

.oracle-card-front-body {
    padding: 12px 14px 6px;
    text-align: center;
}

.oracle-card-name {
    font-family: var(--font-serif);
    font-size: 1rem;
    color: var(--brand);
    font-weight: 400;
    line-height: 1.2;
    margin-bottom: 4px;
}

.oracle-card-frase {
    font-family: var(--font-serif);
    font-style: italic;
    font-size: 0.68rem;
    color: var(--brand-light);
    line-height: 1.4;
}

.oracle-card-front-footer {
    padding: 6px 14px 10px;
    text-align: center;
}

.oracle-card-deco-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(74,59,82,0.15), transparent);
    margin-bottom: 5px;
}

.oracle-card-brand {
    font-size: 0.56rem;
    letter-spacing: 0.2em;
    color: var(--muted);
    opacity: 0.6;
}

/* Mensaje debajo de la carta */
.oracle-message-area {
    animation: fadeUp 0.6s ease;
    text-align: left;
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 14px;
    padding: 20px 22px;
    margin-bottom: 14px;
    box-shadow: var(--shadow-soft);
}

.oracle-razon-text {
    font-size: 0.88rem;
    color: var(--text);
    font-weight: 300;
    line-height: 1.75;
    margin-bottom: 16px;
}

.oracle-result-btn {
    display: inline-block;
    background: var(--brand);
    color: #fff !important;
    padding: 10px 24px;
    border-radius: 50px;
    font-size: 0.82rem;
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
    margin-top: 8px;
}

.oracle-retry-btn:hover {
    border-color: var(--brand);
    color: var(--brand);
}

/* ── Luna ─────────────────────────────────────────── */
.lunar-section {
    position: relative;
    background: linear-gradient(160deg, #f5f0f8 0%, #faf7fc 50%, #f0edf6 100%);
    padding: 90px 0;
    overflow: hidden;
    border-top: 1px solid rgba(74,59,82,0.06);
    border-bottom: 1px solid rgba(74,59,82,0.06);
}

.lunar-stars {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(1px 1px at 10% 15%, rgba(180,160,200,0.35) 0%, transparent 100%),
        radial-gradient(1px 1px at 25% 60%, rgba(180,160,200,0.25) 0%, transparent 100%),
        radial-gradient(1.5px 1.5px at 40% 25%, rgba(180,160,200,0.3) 0%, transparent 100%),
        radial-gradient(1px 1px at 70% 10%, rgba(180,160,200,0.3) 0%, transparent 100%),
        radial-gradient(1px 1px at 85% 75%, rgba(180,160,200,0.25) 0%, transparent 100%),
        radial-gradient(1.5px 1.5px at 50% 5%, rgba(180,160,200,0.3) 0%, transparent 100%),
        radial-gradient(1px 1px at 90% 45%, rgba(212,175,55,0.15) 0%, transparent 100%),
        radial-gradient(1px 1px at 15% 85%, rgba(212,175,55,0.12) 0%, transparent 100%);
    pointer-events: none;
    animation: twinkle 6s ease-in-out infinite alternate;
}
@keyframes twinkle {
    from { opacity: 0.6; }
    to   { opacity: 1; }
}

.lunar-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 1;
}
@media (max-width: 800px) {
    .lunar-grid { grid-template-columns: 1fr; gap: 48px; }
}

/* Izquierda */
.lunar-eyebrow {
    display: block;
    font-size: 0.65rem;
    letter-spacing: 0.22em;
    color: rgba(212,175,55,0.7);
    font-weight: 600;
    margin-bottom: 20px;
    text-transform: uppercase;
}

.lunar-moon-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.lunar-moon-emoji {
    font-size: 5rem;
    display: block;
    animation: moonFloat 5s ease-in-out infinite;
    filter: drop-shadow(0 0 20px rgba(212,175,55,0.4));
}
@keyframes moonFloat {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-10px); }
}

.lunar-moon-glow {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    width: 100px; height: 100px;
    background: radial-gradient(circle, rgba(212,175,55,0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.lunar-phase-name {
    font-family: var(--font-serif);
    font-size: clamp(1.8rem, 3vw, 2.6rem);
    color: var(--brand);
    font-weight: 400;
    margin-bottom: 6px;
    line-height: 1.2;
}

.lunar-energia {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--accent);
    margin-bottom: 16px;
}

.lunar-desc {
    color: var(--muted);
    font-size: 0.92rem;
    font-weight: 300;
    line-height: 1.8;
    margin-bottom: 24px;
}

.lunar-progress-wrap {
    height: 3px;
    background: rgba(74,59,82,0.1);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 8px;
}
.lunar-progress-bar {
    height: 100%;
    background: linear-gradient(to right, rgba(212,175,55,0.5), rgba(212,175,55,0.9));
    border-radius: 10px;
    transition: width 1s ease;
}
.lunar-progress-label {
    font-size: 0.7rem;
    color: var(--muted);
    opacity: 0.6;
    letter-spacing: 0.05em;
}

/* Derecha — carta cristal */
.lunar-crystal-card {
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: transform 0.3s;
}
.lunar-crystal-card:hover { transform: translateY(-4px); }

.lunar-crystal-img-wrap {
    position: relative;
    height: 220px;
    overflow: hidden;
}
.lunar-crystal-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.85) saturate(1.1);
    transition: transform 0.5s;
}
.lunar-crystal-card:hover .lunar-crystal-img { transform: scale(1.04); }

.lunar-crystal-badge {
    position: absolute;
    top: 14px; left: 14px;
    background: rgba(212,175,55,0.9);
    color: #1a1020;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    padding: 4px 12px;
    border-radius: 50px;
}

.lunar-crystal-info {
    padding: 22px 24px 26px;
}
.lunar-crystal-category {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--accent);
    display: block;
    margin-bottom: 6px;
}
.lunar-crystal-name {
    font-family: var(--font-serif);
    font-size: 1.4rem;
    color: var(--brand);
    font-weight: 400;
    margin-bottom: 10px;
}
.lunar-crystal-why {
    font-size: 0.84rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.7;
    margin-bottom: 16px;
    font-style: italic;
}
.lunar-crystal-price {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--brand);
    margin-bottom: 14px;
}
.lunar-crystal-btn {
    display: inline-block;
    background: linear-gradient(135deg, var(--accent), #e8c44a);
    color: #1a1020 !important;
    padding: 11px 28px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    transition: all 0.25s;
    box-shadow: 0 4px 16px rgba(212,175,55,0.3);
}
.lunar-crystal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(212,175,55,0.45);
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
        }

        // Mostrar resultado con animación de carta
        const card = document.getElementById('oracle-card-flip');
        const messageArea = document.getElementById('oracle-message-area');
        card.classList.remove('flipped');
        messageArea.style.display = 'none';

        document.getElementById('oracle-form-area').style.display = 'none';
        document.getElementById('oracle-result').style.display = 'block';
        document.getElementById('oracle-result').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Voltear la carta después de 800ms
        setTimeout(() => {
            card.classList.add('flipped');
            // Mostrar mensaje después de que termine el volteo
            setTimeout(() => {
                messageArea.style.display = 'block';
            }, 900);
        }, 800);
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
    document.getElementById('oracle-card-flip').classList.remove('flipped');
    document.getElementById('oracle-message-area').style.display = 'none';
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

@push('scripts')
<script>
let heroLastCard = -1;
function heroDrawCard() {
    let idx;
    do { idx = Math.floor(Math.random() * ORACLE_CARDS.length); } while (idx === heroLastCard);
    heroLastCard = idx;
    const card = ORACLE_CARDS[idx];
    document.getElementById('heroOracleIdle').style.display      = 'none';
    document.getElementById('heroOracleResult').style.display    = 'none';
    document.getElementById('heroOracleShuffling').style.display = 'flex';
    setTimeout(() => {
        document.getElementById('heroOracleSymbol').textContent  = card.symbol;
        document.getElementById('heroOracleKeyword').textContent = card.keyword;
        document.getElementById('heroOracleMsg').textContent     = card.message;
        document.getElementById('heroOracleShuffling').style.display = 'none';
        document.getElementById('heroOracleResult').style.display    = 'block';
    }, 1600);
}
function heroResetOracle() {
    document.getElementById('heroOracleResult').style.display    = 'none';
    document.getElementById('heroOracleShuffling').style.display = 'none';
    document.getElementById('heroOracleIdle').style.display      = 'flex';
}
</script>
@endpush
