@extends('layouts.app')
@section('title', 'Venta Mayorista — AURA33')

@section('content')

{{-- Hero mayorista --}}
<section class="ws-hero">
    <div class="container ws-hero-inner">
        <div>
            <span class="section-subtitle">Programa Mayorista</span>
            <h1 class="ws-title">Crecé junto<br>a AURA33</h1>
            <p class="ws-lead">
                Si tenés una tienda, emprendimiento o sos terapeuta holística, nuestro programa mayorista está diseñado para vos.
                Accedé a precios especiales, prioridad de stock y soporte personalizado.
            </p>
            <a href="{{ route('wholesale.register') }}" class="btn" style="margin-top:12px;">
                Quiero ser Mayorista →
            </a>
        </div>
        <div class="ws-hero-visual">
            <div class="ws-hero-card">
                <div style="font-size:3.5rem; margin-bottom:16px;">🏪</div>
                <p style="font-family:var(--font-serif); font-size:1.4rem; color:var(--brand); margin-bottom:6px; font-weight:400;">Precio Mayorista</p>
                <p style="font-size:0.85rem; color:var(--muted); font-weight:300;">Hasta 27% de descuento sobre precio minorista</p>
                <div class="ws-example">
                    <div class="ws-price-row">
                        <span>Botella cristal (minorista)</span>
                        <span style="text-decoration:line-through; color:var(--muted);">$9.500</span>
                    </div>
                    <div class="ws-price-row" style="font-weight:600; color:var(--brand);">
                        <span>Botella cristal (mayorista)</span>
                        <span style="color:var(--accent);">$7.000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Beneficios --}}
<section style="padding: 90px 0; background: var(--bg-alt); border-top: 1px solid rgba(74,59,82,0.06);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">¿Qué incluye?</span>
            <h2 class="section-title">Beneficios del Programa</h2>
            <div class="divider"></div>
        </div>

        <div class="ws-benefits-grid">
            <div class="ws-benefit">
                <span class="ws-benefit-icon">💸</span>
                <h3>Precios Especiales</h3>
                <p>Accedés a los precios mayoristas en toda la línea: botellas de cristal, torres y oráculos.</p>
            </div>
            <div class="ws-benefit">
                <span class="ws-benefit-icon">📦</span>
                <h3>Prioridad de Stock</h3>
                <p>Los mayoristas aprobados tienen prioridad de compra cuando hay stock limitado de algún cristal.</p>
            </div>
            <div class="ws-benefit">
                <span class="ws-benefit-icon">🔮</span>
                <h3>Info Energética Completa</h3>
                <p>Recibís material de cada cristal para que puedas explicarle a tus clientes sus propiedades.</p>
            </div>
            <div class="ws-benefit">
                <span class="ws-benefit-icon">💬</span>
                <h3>Atención Personalizada</h3>
                <p>Canal de WhatsApp exclusivo para mayoristas. Respondemos rápido y te acompañamos en tu crecimiento.</p>
            </div>
            <div class="ws-benefit">
                <span class="ws-benefit-icon">🌙</span>
                <h3>Novedades Primero</h3>
                <p>Enterarte primero de nuevos productos, colecciones y promociones especiales antes que nadie.</p>
            </div>
            <div class="ws-benefit">
                <span class="ws-benefit-icon">🚚</span>
                <h3>Envío Gratis</h3>
                <p>En pedidos mayoristas que superen el monto mínimo, el envío no tiene costo adicional.</p>
            </div>
        </div>
    </div>
</section>

{{-- Requisitos --}}
<section style="padding: 90px 0;">
    <div class="container" style="max-width: 800px;">
        <div class="section-header">
            <span class="section-subtitle">¿Quién puede aplicar?</span>
            <h2 class="section-title">Requisitos</h2>
            <div class="divider"></div>
        </div>

        <div class="ws-reqs">
            <div class="ws-req-item">
                <span class="ws-req-icon">✦</span>
                <div>
                    <strong>Tener un negocio activo</strong>
                    <p>Tienda física, online, Instagram, Marketplace o cualquier canal de venta.</p>
                </div>
            </div>
            <div class="ws-req-item">
                <span class="ws-req-icon">✦</span>
                <div>
                    <strong>Terapeutas y facilitadores</strong>
                    <p>Si trabajás con energía, chakras, Reiki, registros akáshicos o terapias holísticas.</p>
                </div>
            </div>
            <div class="ws-req-item">
                <span class="ws-req-icon">✦</span>
                <div>
                    <strong>Intención y cuidado</strong>
                    <p>Buscamos que los cristales lleguen a quienes los valoran. Evaluamos cada solicitud con atención.</p>
                </div>
            </div>
            <div class="ws-req-item">
                <span class="ws-req-icon">✦</span>
                <div>
                    <strong>Pedido mínimo</strong>
                    <p>El primer pedido mayorista tiene un mínimo de $30.000. Sin mínimo de unidades por producto.</p>
                </div>
            </div>
        </div>

        <div style="text-align:center; margin-top:56px;">
            <p style="font-family:var(--font-serif); font-style:italic; font-size:1.15rem; color:var(--brand-light); margin-bottom:30px;">
                "Revisamos cada solicitud y respondemos en menos de 48 horas hábiles."
            </p>
            <a href="{{ route('wholesale.register') }}" class="btn" style="padding: 16px 44px; font-size:0.95rem;">
                Completar Solicitud
            </a>
        </div>

    </div>
</section>

{{-- FAQ --}}
<section style="padding: 80px 0 100px; background: var(--bg-alt); border-top: 1px solid rgba(74,59,82,0.06);">
    <div class="container" style="max-width: 700px;">
        <div class="section-header">
            <span class="section-subtitle">Preguntas frecuentes</span>
            <h2 class="section-title">FAQ Mayoristas</h2>
            <div class="divider"></div>
        </div>

        <div class="ws-faq">
            <details class="faq-item">
                <summary>¿Cuánto tarda la aprobación?</summary>
                <p>Revisamos las solicitudes de lunes a viernes. Te contactamos por email o WhatsApp en menos de 48 horas hábiles.</p>
            </details>
            <details class="faq-item">
                <summary>¿Cómo hago mi primer pedido una vez aprobada?</summary>
                <p>Una vez aprobado tu perfil, podés ingresar a la tienda, ir al checkout y seleccionar "Mayorista". Ingresás tu email aprobado y el sistema aplica automáticamente los precios especiales.</p>
            </details>
            <details class="faq-item">
                <summary>¿Puedo mezclar productos en un pedido?</summary>
                <p>Sí, el mínimo de $30.000 es por pedido total. Podés combinar cualquier producto de cualquier categoría.</p>
            </details>
            <details class="faq-item">
                <summary>¿Tienen imágenes para que yo use en mis redes?</summary>
                <p>¡Sí! Al momento de la aprobación te enviamos un pack de imágenes profesionales de cada producto para que uses en tu comunicación.</p>
            </details>
            <details class="faq-item">
                <summary>¿Puedo revender en Instagram o MercadoLibre?</summary>
                <p>Totalmente. Muchos de nuestros mayoristas venden en redes sociales, Mercado Libre, ferias y tiendas físicas. No tenemos restricciones de canal.</p>
            </details>
        </div>

    </div>
</section>

@endsection

@push('styles')
<style>
.ws-hero {
    padding: 90px 0;
    background: linear-gradient(180deg, rgba(247,231,230,0.4) 0%, rgba(250,248,245,1) 100%);
}

.ws-hero-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

@media (max-width: 768px) { .ws-hero-inner { grid-template-columns: 1fr; } }

.ws-title {
    font-family: var(--font-serif);
    font-size: clamp(2.4rem, 5vw, 3.5rem);
    color: var(--brand);
    font-weight: 400;
    line-height: 1.15;
    margin-bottom: 20px;
}

.ws-lead {
    font-size: 1.02rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.8;
    max-width: 480px;
}

.ws-hero-card {
    background: var(--white);
    border-radius: 16px;
    padding: 36px;
    box-shadow: var(--shadow-card);
    border: 1px solid rgba(74,59,82,0.06);
    text-align: center;
}

.ws-example {
    margin-top: 24px;
    background: var(--bg-alt);
    border-radius: 8px;
    padding: 16px;
    border: 1px solid rgba(74,59,82,0.06);
}

.ws-price-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.88rem;
    color: var(--text);
    padding: 6px 0;
}

.ws-benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
}

.ws-benefit {
    background: var(--white);
    border-radius: 12px;
    padding: 32px 28px;
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border);
}

.ws-benefit-icon { font-size: 2rem; display: block; margin-bottom: 14px; }

.ws-benefit h3 {
    font-family: var(--font-serif);
    font-size: 1.15rem;
    color: var(--brand);
    margin-bottom: 8px;
    font-weight: 600;
}

.ws-benefit p { font-size: 0.87rem; color: var(--muted); font-weight: 300; line-height: 1.7; }

.ws-reqs { display: flex; flex-direction: column; gap: 0; }

.ws-req-item {
    display: flex;
    gap: 20px;
    padding: 22px 0;
    border-bottom: 1px solid rgba(74,59,82,0.06);
}

.ws-req-item:first-child { border-top: 1px solid rgba(74,59,82,0.06); }

.ws-req-icon {
    font-size: 1rem;
    color: var(--accent);
    margin-top: 3px;
    flex-shrink: 0;
}

.ws-req-item strong {
    display: block;
    font-size: 0.95rem;
    color: var(--brand);
    margin-bottom: 4px;
    font-weight: 600;
}

.ws-req-item p { font-size: 0.87rem; color: var(--muted); font-weight: 300; }

.ws-faq { display: flex; flex-direction: column; gap: 0; }

.faq-item {
    border-bottom: 1px solid rgba(74,59,82,0.07);
    cursor: pointer;
}

.faq-item:first-child { border-top: 1px solid rgba(74,59,82,0.07); }

.faq-item summary {
    padding: 18px 0;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--brand);
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-item summary::after {
    content: '+';
    font-size: 1.3rem;
    font-weight: 300;
    color: var(--brand-light);
    transition: transform 0.2s;
}

.faq-item[open] summary::after { transform: rotate(45deg); }

.faq-item p {
    padding: 0 0 18px;
    font-size: 0.88rem;
    color: var(--muted);
    font-weight: 300;
    line-height: 1.75;
}
</style>
@endpush
