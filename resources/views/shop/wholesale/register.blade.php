@extends('layouts.app')
@section('title', 'Solicitud Mayorista — AURA33')

@section('content')
<div style="padding: 80px 0 100px;">
    <div class="container" style="max-width: 680px;">

        <div class="section-header">
            <span class="section-subtitle">Programa Mayorista</span>
            <h1 class="section-title">Solicitud de Alta</h1>
            <div class="divider"></div>
            <p style="color:var(--muted); font-size:0.95rem; margin-top:16px; font-weight:300; line-height:1.75;">
                Completá el formulario y lo revisamos en menos de 48 horas hábiles.
                Te vamos a contactar por email o WhatsApp.
            </p>
        </div>

        @if($errors->any())
        <div style="background:rgba(210,80,80,0.08); border:1px solid rgba(210,80,80,0.18); border-radius:10px; padding:16px 20px; margin-bottom:28px;">
            <ul style="list-style:none; color:#a03030; font-size:0.88rem; display:flex; flex-direction:column; gap:4px;">
                @foreach($errors->all() as $e)
                    <li>⚠ {{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('wholesale.store') }}" method="POST" class="ws-form">
            @csrf

            <div class="form-section">
                <h3 class="form-section-title">Tus Datos</h3>
                <div class="form-grid">
                    <div class="form-group form-full">
                        <label>Nombre y Apellido *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="María González" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="maria@tunegocio.com" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono / WhatsApp *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+54 341 000 0000" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña para el panel *</label>
                        <input type="password" name="password" class="form-input" placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Repetir contraseña *</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Repetí la contraseña" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Tu Negocio</h3>
                <div class="form-grid">
                    <div class="form-group form-full">
                        <label>Nombre del Negocio / Emprendimiento *</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" class="form-input" placeholder="Cristales del Alma" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de negocio *</label>
                        <select name="business_type" class="form-input" required>
                            <option value="" disabled {{ old('business_type') ? '' : 'selected' }}>Seleccioná una opción</option>
                            @foreach(\App\Models\WholesaleRequest::BUSINESS_TYPES as $val => $label)
                                <option value="{{ $val }}" {{ old('business_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>CUIT / DNI</label>
                        <input type="text" name="cuit" value="{{ old('cuit') }}" class="form-input" placeholder="20-12345678-9">
                    </div>
                    <div class="form-group">
                        <label>Ciudad *</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-input" placeholder="Rosario" required>
                    </div>
                    <div class="form-group">
                        <label>Provincia *</label>
                        <input type="text" name="province" value="{{ old('province', 'Santa Fe') }}" class="form-input" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Contanos un poco más <span style="font-size:0.8rem; font-weight:300; color:var(--muted);">(opcional)</span></h3>
                <textarea name="notes" class="form-input" rows="4"
                    placeholder="¿Cómo conociste AURA33? ¿Qué cristales te interesan más? ¿Dónde y cómo vendés? Cualquier información que quieras compartir es bienvenida.">{{ old('notes') }}</textarea>
            </div>

            <div style="background: rgba(74,59,82,0.04); border:1px solid rgba(74,59,82,0.12); border-radius:12px; padding:22px 26px; margin-bottom:28px; line-height:1.8;">
                <p style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--brand); margin-bottom:12px;">✦ Condiciones del programa mayorista</p>
                <ul style="list-style:none; font-size:0.86rem; color:var(--text); display:flex; flex-direction:column; gap:8px;">
                    <li style="display:flex; gap:10px;"><span style="color:var(--brand); font-size:1rem;">💎</span><span><strong>Mínimo 10 unidades</strong> por pedido al <strong>precio mayorista</strong>. La variedad de piedras es a elección de AURA33.</span></li>
                    <li style="display:flex; gap:10px;"><span style="color:var(--brand); font-size:1rem;">✨</span><span><strong>Más de 20 unidades</strong> — <strong>5% adicional de descuento</strong>. El cliente elige las piedras con un máximo de 3 repeticiones por variedad.</span></li>
                    <li style="display:flex; gap:10px;"><span style="color:var(--brand); font-size:1rem;">🚚</span><span>Envíos coordinados y atención directa para mayoristas.</span></li>
                </ul>
                <p style="font-size:0.78rem; color:var(--muted); margin-top:14px; border-top:1px solid rgba(74,59,82,0.08); padding-top:12px;">
                    Al enviar esta solicitud entendés que AURA33 la revisará manualmente. La aprobación no es automática y nos reservamos el derecho de aceptar o no cada solicitud.
                </p>
            </div>

            <button type="submit" class="btn" style="width:100%; padding:16px; font-size:0.95rem;">
                Enviar Solicitud
            </button>

            <div style="text-align:center; margin-top:16px;">
                <a href="{{ route('wholesale.info') }}" style="font-size:0.82rem; color:var(--muted);">← Ver más info del programa</a>
            </div>
        </form>

    </div>
</div>
@endsection

@push('styles')
<style>
.ws-form { display: flex; flex-direction: column; gap: 20px; }

.form-section {
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 12px;
    padding: 28px;
    box-shadow: var(--shadow-soft);
}

.form-section-title {
    font-family: var(--font-serif);
    font-size: 1.2rem;
    color: var(--brand);
    margin-bottom: 20px;
    font-weight: 400;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }

.form-full { grid-column: 1 / -1; }

.form-group label {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin-bottom: 6px;
}

.form-input {
    width: 100%;
    background: var(--bg);
    border: 1px solid rgba(74,59,82,0.15);
    border-radius: 6px;
    padding: 12px 14px;
    color: var(--text);
    font-family: var(--font-sans);
    font-size: 0.9rem;
    font-weight: 300;
    outline: none;
    transition: border-color 0.2s;
    resize: vertical;
}

.form-input:focus { border-color: var(--brand); background: var(--white); }
.form-input::placeholder { color: rgba(110,100,115,0.55); }

select.form-input { cursor: pointer; }
</style>
@endpush
