@extends('layouts.app')
@section('title', 'Ingreso Mayoristas — AURA33')

@section('content')
<div class="ws-login-wrap">

    <div class="ws-login-card">

        <div class="ws-login-header">
            <div class="ws-login-gem">💎</div>
            <h1 class="ws-login-title">Portal Mayoristas</h1>
            <p class="ws-login-sub">Ingresá con tu email y contraseña</p>
        </div>

        @if($errors->any())
        <div class="ws-alert ws-alert-error">
            <span>⚠</span> {{ $errors->first() }}
        </div>
        @endif

        @if(session('success'))
        <div class="ws-alert ws-alert-ok">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('wholesale.login.post') }}" class="ws-login-form">
            @csrf

            <div class="ws-field">
                <label class="ws-label">Email</label>
                <input type="email" name="email" class="ws-input"
                    value="{{ old('email') }}"
                    placeholder="tu@email.com"
                    autofocus required>
            </div>

            <div class="ws-field">
                <label class="ws-label">Contraseña</label>
                <input type="password" name="password" class="ws-input"
                    placeholder="••••••••" required>
            </div>

            <button type="submit" class="ws-btn-submit">
                Ingresar al panel
            </button>
        </form>

        <div class="ws-login-footer">
            <p>¿Aún no tenés cuenta? <a href="{{ route('wholesale.register') }}">Solicitá el alta</a></p>
            <a href="{{ route('home') }}" class="ws-back-link">← Volver a la tienda</a>
        </div>

    </div>

    {{-- Panel decorativo lateral --}}
    <div class="ws-login-deco">
        <div class="ws-deco-inner">
            <p class="ws-deco-quote">"Los cristales son la memoria viva del universo."</p>
            <div class="ws-deco-benefits">
                <div class="ws-deco-item">💎 Precios exclusivos mayoristas</div>
                <div class="ws-deco-item">📦 Stock reservado para vos</div>
                <div class="ws-deco-item">✨ Atención personalizada</div>
                <div class="ws-deco-item">🚚 Envíos coordinados</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.ws-login-wrap {
    min-height: calc(100vh - 80px);
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--bg);
}

@media (max-width: 768px) {
    .ws-login-wrap { grid-template-columns: 1fr; }
    .ws-login-deco { display: none; }
}

/* ── Card izquierda ── */
.ws-login-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px 64px;
    max-width: 500px;
    width: 100%;
    margin: 0 auto;
}

.ws-login-header {
    margin-bottom: 36px;
}

.ws-login-gem {
    font-size: 2.6rem;
    margin-bottom: 16px;
    display: block;
}

.ws-login-title {
    font-family: var(--font-serif);
    font-size: 2.2rem;
    color: var(--brand);
    font-weight: 400;
    margin-bottom: 8px;
    line-height: 1.2;
}

.ws-login-sub {
    font-size: 0.92rem;
    color: var(--muted);
    font-weight: 300;
}

.ws-alert {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.87rem;
    margin-bottom: 24px;
    display: flex;
    gap: 8px;
    align-items: flex-start;
}

.ws-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.ws-alert-ok {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
}

.ws-login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.ws-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.ws-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
}

.ws-input {
    background: var(--white);
    border: 1.5px solid rgba(74,59,82,0.15);
    border-radius: 8px;
    padding: 13px 16px;
    font-size: 0.95rem;
    font-family: var(--font-sans);
    color: var(--text);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%;
}

.ws-input:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(74,59,82,0.08);
}

.ws-input::placeholder { color: rgba(110,100,115,0.45); }

.ws-btn-submit {
    margin-top: 8px;
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 15px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    cursor: pointer;
    font-family: var(--font-sans);
    transition: background 0.2s, transform 0.15s;
}

.ws-btn-submit:hover {
    background: #3a2d42;
    transform: translateY(-1px);
}

.ws-login-footer {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgba(74,59,82,0.08);
    text-align: center;
    font-size: 0.85rem;
    color: var(--muted);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.ws-login-footer a {
    color: var(--brand);
    font-weight: 500;
}

.ws-back-link {
    font-size: 0.8rem;
    color: var(--muted) !important;
    font-weight: 300 !important;
}

/* ── Panel derecho decorativo ── */
.ws-login-deco {
    background: linear-gradient(145deg, #4a3b52 0%, #2f2539 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 56px;
    position: relative;
    overflow: hidden;
}

.ws-login-deco::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(212,175,55,0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.ws-login-deco::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -60px;
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(124,106,135,0.2) 0%, transparent 70%);
    border-radius: 50%;
}

.ws-deco-inner {
    position: relative;
    z-index: 1;
    max-width: 340px;
}

.ws-deco-quote {
    font-family: var(--font-serif);
    font-size: 1.5rem;
    color: rgba(255,255,255,0.9);
    font-style: italic;
    font-weight: 400;
    line-height: 1.5;
    margin-bottom: 44px;
}

.ws-deco-benefits {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ws-deco-item {
    color: rgba(255,255,255,0.75);
    font-size: 0.9rem;
    font-weight: 300;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: rgba(255,255,255,0.06);
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.06);
}
</style>
@endpush
