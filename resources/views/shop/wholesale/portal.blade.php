@extends('layouts.app')
@section('title', 'Panel Mayorista — AURA33')

@section('content')
<div style="padding: 60px 0 100px; background: var(--bg); min-height: 80vh;">
    <div class="container">

        {{-- Header --}}
        <div class="portal-header">
            <div>
                <span class="section-subtitle">Portal Mayorista</span>
                <h1 class="portal-title">Hola, {{ $wholesaler->name }} 💎</h1>
                <p style="font-size:0.88rem; color:var(--muted);">
                    {{ $wholesaler->business_name }} &nbsp;·&nbsp; {{ $wholesaler->city }}, {{ $wholesaler->province }}
                </p>
            </div>
            <div class="portal-header-actions">
                <a href="{{ route('wholesale.restock') }}" class="btn" style="background:var(--accent,#7c3aed); white-space:nowrap;">
                    📦 Pedir reposición
                </a>
                <div class="profile-dropdown-wrap">
                    <button class="profile-dropdown-trigger btn-outline-muted" onclick="toggleProfileMenu()" type="button">
                        👤 {{ $wholesaler->name }}
                    </button>
                    <div class="profile-dropdown-menu" id="profileMenu">
                        <div class="profile-dropdown-name">{{ $wholesaler->email }}</div>
                        <div class="profile-dropdown-divider"></div>

                        @if($errors->has('current_password') || session('show_password_form'))
                        <div class="profile-pw-form" id="profilePwForm" style="display:block;">
                        @else
                        <div class="profile-pw-form" id="profilePwForm" style="display:none;">
                        @endif
                            @if($errors->has('current_password'))
                                <p class="profile-pw-error">{{ $errors->first('current_password') }}</p>
                            @endif
                            @if($errors->has('password'))
                                <p class="profile-pw-error">{{ $errors->first('password') }}</p>
                            @endif
                            <form method="POST" action="{{ route('wholesale.change-password') }}">
                                @csrf
                                <input type="password" name="current_password" placeholder="Contraseña actual" required class="profile-pw-input">
                                <input type="password" name="password" placeholder="Nueva contraseña" required minlength="6" class="profile-pw-input">
                                <input type="password" name="password_confirmation" placeholder="Confirmar nueva" required minlength="6" class="profile-pw-input">
                                <button type="submit" class="profile-pw-btn">Guardar</button>
                            </form>
                        </div>

                        <button class="profile-dropdown-item" onclick="togglePwForm()" type="button">🔑 Cambiar contraseña</button>
                        <div class="profile-dropdown-divider"></div>
                        <form method="POST" action="{{ route('wholesale.logout') }}">
                            @csrf
                            <button type="submit" class="profile-dropdown-item profile-dropdown-item--danger">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="portal-alert-success">{{ session('success') }}</div>
        @endif

        {{-- Resumen --}}
        <div class="portal-info-grid" style="margin-top:32px;">
            <div class="portal-info-card">
                <span class="portal-info-label">Recibidas</span>
                <span class="portal-info-val">{{ $totalDelivered }}</span>
            </div>
            <div class="portal-info-card">
                <span class="portal-info-label">Vendidas</span>
                <span class="portal-info-val">{{ $totalSold }}</span>
            </div>
            <div class="portal-info-card" style="border-top: 3px solid #22c55e;">
                <span class="portal-info-label">Quedan en stock</span>
                <span class="portal-info-val" style="color:#15803d;">{{ $quedan }}</span>
            </div>
            <div class="portal-info-card">
                <span class="portal-info-label">Total pagado</span>
                <span class="portal-info-val">${{ number_format($totalPaid, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Entregas --}}
        <div style="margin-top:50px;">
            <h2 style="font-family:var(--font-serif); font-size:1.5rem; color:var(--brand); font-weight:400; margin-bottom:20px;">
                Entregas recibidas
            </h2>
            @if($deliveries->isEmpty())
                <p style="color:var(--muted); font-size:0.9rem;">Todavía no hay entregas registradas.</p>
            @else
            <div class="portal-table-wrap">
                <table class="portal-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Botellas</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $d)
                        <tr>
                            <td>{{ ($d->date ?? $d->created_at)->format('d/m/Y') }}</td>
                            <td><strong>{{ $d->quantity }}</strong></td>
                            <td style="color:var(--muted); font-size:0.88rem;">{{ $d->notes ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Pagos --}}
        <div style="margin-top:50px;">
            <h2 style="font-family:var(--font-serif); font-size:1.5rem; color:var(--brand); font-weight:400; margin-bottom:20px;">
                Pagos registrados
            </h2>
            @if($payments->isEmpty())
                <p style="color:var(--muted); font-size:0.9rem;">Todavía no hay pagos registrados.</p>
            @else
            <div class="portal-table-wrap">
                <table class="portal-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Importe</th>
                            <th>Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $p)
                        <tr>
                            <td>{{ ($p->date ?? $p->created_at)->format('d/m/Y') }}</td>
                            <td>{{ $p->product_name }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td><strong>${{ number_format($p->amount, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($p->receipt)
                                    <a href="{{ asset('storage/' . $p->receipt) }}" target="_blank" style="color:var(--brand); font-size:0.88rem;">Ver comprobante</a>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
.portal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 36px;
    padding-bottom: 32px;
    border-bottom: 1px solid var(--border);
}
.portal-title {
    font-family: var(--font-serif);
    font-size: 2rem;
    color: var(--brand);
    font-weight: 400;
    margin: 6px 0 8px;
}
.portal-header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-shrink: 0;
}
.btn-outline-muted {
    padding: 10px 20px;
    border: 1px solid rgba(74,59,82,0.2);
    border-radius: 50px;
    font-size: 0.82rem;
    color: var(--muted);
    cursor: pointer;
    background: transparent;
    transition: all 0.2s;
    font-family: var(--font-sans);
}
.btn-outline-muted:hover {
    border-color: var(--brand);
    color: var(--brand);
}
.portal-alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    padding: 12px 18px;
    border-radius: 10px;
    font-size: 0.88rem;
    margin-bottom: 28px;
}
.portal-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}
.portal-info-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 22px 24px;
    box-shadow: var(--shadow-soft);
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.portal-info-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    font-weight: 600;
}
.portal-info-val {
    font-family: var(--font-serif);
    font-size: 1.6rem;
    color: var(--brand);
    font-weight: 400;
}

/* ── Tablas de entregas/pagos ── */
.portal-table-wrap {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
}
.portal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
}
.portal-table th {
    background: rgba(74,59,82,0.04);
    text-align: left;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    color: var(--brand);
    font-weight: 600;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.portal-table td {
    padding: 14px 18px;
    border-bottom: 1px solid rgba(74,59,82,0.04);
    vertical-align: middle;
    color: var(--text);
}
.portal-table tr:last-child td { border-bottom: none; }
.portal-table tr:hover td { background: rgba(74,59,82,0.015); }

/* ── Profile dropdown ── */
.profile-dropdown-wrap { position: relative; }
.profile-dropdown-trigger { cursor: pointer; white-space: nowrap; }
.profile-dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(74,59,82,0.13);
    min-width: 260px;
    z-index: 200;
    overflow: hidden;
}
.profile-dropdown-menu.open { display: block; }
.profile-dropdown-name {
    padding: 12px 16px 8px;
    font-size: 0.78rem;
    color: var(--muted);
    letter-spacing: 0.04em;
}
.profile-dropdown-divider {
    height: 1px;
    background: var(--border);
    margin: 4px 0;
}
.profile-dropdown-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 10px 16px;
    font-size: 0.88rem;
    color: var(--brand);
    background: none;
    border: none;
    cursor: pointer;
    font-family: var(--font-sans);
    transition: background 0.15s;
}
.profile-dropdown-item:hover { background: rgba(74,59,82,0.05); }
.profile-dropdown-item--danger { color: #b91c1c; }
.profile-dropdown-item--danger:hover { background: #fff5f5; }
.profile-pw-form { padding: 12px 16px; border-bottom: 1px solid var(--border); }
.profile-pw-input {
    display: block;
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 8px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 0.85rem;
    font-family: var(--font-sans);
    outline: none;
    box-sizing: border-box;
    color: var(--brand);
}
.profile-pw-input:focus { border-color: var(--brand); }
.profile-pw-btn {
    width: 100%;
    padding: 8px;
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    cursor: pointer;
    font-family: var(--font-sans);
    transition: opacity 0.2s;
}
.profile-pw-btn:hover { opacity: 0.85; }
.profile-pw-error {
    font-size: 0.8rem;
    color: #b91c1c;
    margin: 0 0 8px;
}
</style>
@endpush

@push('scripts')
<script>
function toggleProfileMenu() {
    document.getElementById('profileMenu').classList.toggle('open');
}
function togglePwForm() {
    var f = document.getElementById('profilePwForm');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    var wrap = document.querySelector('.profile-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('profileMenu').classList.remove('open');
    }
});
@if($errors->has('current_password') || session('show_password_form'))
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('profileMenu').classList.add('open');
});
@endif
</script>
@endpush
