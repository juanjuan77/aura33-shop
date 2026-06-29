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
                <span class="portal-info-label">Botellas recibidas</span>
                <span class="portal-info-val">{{ $totalDelivered }}</span>
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
            <div style="overflow-x:auto;">
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
                            <td>{{ $d->created_at->format('d/m/Y') }}</td>
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
            <div style="overflow-x:auto;">
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
                            <td>{{ $p->created_at->format('d/m/Y') }}</td>
                            <td>{{ $p->product_name }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td><strong>${{ number_format($p->amount, 0, ',', '.') }}</strong></td>
                            <td style="color:var(--muted); font-size:0.88rem;">{{ $p->receipt ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</div>

<style>
.portal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.portal-table th {
    text-align: left;
    padding: 10px 14px;
    border-bottom: 2px solid var(--border, #e5e7eb);
    color: var(--muted);
    font-weight: 500;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.portal-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border, #f3f4f6);
    vertical-align: middle;
}
.portal-table tr:last-child td { border-bottom: none; }
.portal-table tr:hover td { background: var(--bg-soft, #fafafa); }
</style>

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
document.getElementById('profileMenu').classList.add('open');
@endif
</script>
@endsection
