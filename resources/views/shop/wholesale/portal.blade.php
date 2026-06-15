@extends('layouts.app')
@section('title', 'Panel Mayorista — AURA33')

@section('content')
<div style="padding: 60px 0 100px; background: var(--bg); min-height: 80vh;">
    <div class="container">

        {{-- Header panel --}}
        <div class="portal-header">
            <div>
                <span class="section-subtitle">Portal Mayorista</span>
                <h1 class="portal-title">Hola, {{ $wholesaler->name }} 💎</h1>
                <p style="font-size:0.88rem; color:var(--muted);">
                    {{ $wholesaler->business_name }} &nbsp;·&nbsp; {{ $wholesaler->city }}, {{ $wholesaler->province }}
                </p>
            </div>
            <div class="portal-header-actions">
                <a href="{{ route('shop') }}" class="btn">
                    Hacer un pedido
                </a>
                <form method="POST" action="{{ route('wholesale.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-outline-muted">Salir</button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="portal-alert-success">{{ session('success') }}</div>
        @endif

        {{-- Info cuenta --}}
        <div class="portal-info-grid">
            <div class="portal-info-card">
                <span class="portal-info-label">Estado de cuenta</span>
                <span class="badge-approved">✓ Aprobada</span>
            </div>
            <div class="portal-info-card">
                <span class="portal-info-label">Total de pedidos</span>
                <span class="portal-info-val">{{ $orders->count() }}</span>
            </div>
            <div class="portal-info-card">
                <span class="portal-info-label">Total comprado</span>
                <span class="portal-info-val">${{ number_format($orders->sum('total'), 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- CTA nuevo pedido --}}
        <div class="portal-cta">
            <div>
                <p style="font-family:var(--font-serif); font-size:1.15rem; color:var(--brand); margin-bottom:4px;">
                    ¿Querés hacer un pedido nuevo?
                </p>
                <p style="font-size:0.85rem; color:var(--muted);">
                    Navegá la tienda, agregá productos al carrito y al hacer el checkout seleccioná "Mayorista" con tu email.
                </p>
            </div>
            <a href="{{ route('shop') }}" class="btn" style="white-space:nowrap;">Ir a la tienda →</a>
        </div>

        {{-- Pedidos --}}
        <div style="margin-top: 50px;">
            <h2 style="font-family:var(--font-serif); font-size:1.6rem; color:var(--brand); font-weight:400; margin-bottom:28px;">
                Mis pedidos
            </h2>

            @if($orders->isEmpty())
            <div class="portal-empty">
                <span style="font-size:3rem; display:block; margin-bottom:16px;">🛍️</span>
                <p style="font-family:var(--font-serif); font-size:1.2rem; color:var(--brand); margin-bottom:8px;">Todavía no tenés pedidos</p>
                <p style="font-size:0.88rem; color:var(--muted); margin-bottom:24px;">Visitá la tienda y hacé tu primer pedido mayorista.</p>
                <a href="{{ route('shop') }}" class="btn">Ver productos</a>
            </div>
            @else
            <div class="orders-table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <span style="font-family:monospace; font-size:0.82rem; color:var(--brand); font-weight:600;">
                                    {{ $order->order_number }}
                                </span>
                            </td>
                            <td style="color:var(--muted); font-size:0.85rem;">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="font-size:0.85rem; color:var(--text);">
                                @foreach($order->items as $item)
                                    <div>{{ $item->product_name }} × {{ $item->quantity }}</div>
                                @endforeach
                            </td>
                            <td style="font-weight:600; color:var(--brand);">
                                ${{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending'          => ['bg'=>'#fefce8','color'=>'#a16207','label'=>'Pendiente'],
                                        'transfer_pending' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','label'=>'Esperando transferencia'],
                                        'confirmed'        => ['bg'=>'#f0fdf4','color'=>'#15803d','label'=>'Confirmado'],
                                        'shipped'          => ['bg'=>'#faf5ff','color'=>'#7e22ce','label'=>'Enviado'],
                                        'delivered'        => ['bg'=>'#f0fdf4','color'=>'#15803d','label'=>'Entregado'],
                                        'cancelled'        => ['bg'=>'#fef2f2','color'=>'#b91c1c','label'=>'Cancelado'],
                                    ];
                                    $sc = $statusColors[$order->status] ?? ['bg'=>'#f5f5f5','color'=>'#666','label'=>$order->status];
                                @endphp
                                <span style="background:{{ $sc['bg'] }}; color:{{ $sc['color'] }}; padding:3px 10px; border-radius:50px; font-size:0.75rem; font-weight:600; white-space:nowrap;">
                                    {{ $sc['label'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Info transferencia si hay pedidos pendientes --}}
            @if($orders->where('status', 'transfer_pending')->isNotEmpty())
            <div class="portal-transfer-info">
                <h3 style="font-family:var(--font-serif); font-size:1.1rem; color:var(--brand); margin-bottom:14px;">
                    💳 Datos para transferencia
                </h3>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                    <div><span class="transfer-label">Banco</span><span class="transfer-val">Banco Galicia</span></div>
                    <div><span class="transfer-label">Titular</span><span class="transfer-val">AURA33</span></div>
                    <div><span class="transfer-label">CBU</span><span class="transfer-val" style="font-family:monospace;">0070999520000004567890</span></div>
                    <div><span class="transfer-label">Alias</span><span class="transfer-val" style="font-family:monospace;">AURA33.CRISTALES</span></div>
                </div>
            </div>
            @endif
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

.badge-approved {
    font-size: 0.88rem;
    font-weight: 600;
    color: #15803d;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 4px 12px;
    border-radius: 50px;
    align-self: flex-start;
}

.portal-cta {
    background: linear-gradient(135deg, rgba(74,59,82,0.04), rgba(212,175,55,0.04));
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 14px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}

.portal-empty {
    text-align: center;
    padding: 60px 20px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
}

.orders-table-wrap {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th {
    background: rgba(74,59,82,0.04);
    padding: 14px 18px;
    text-align: left;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--brand);
    border-bottom: 1px solid var(--border);
}

.orders-table td {
    padding: 16px 18px;
    border-bottom: 1px solid rgba(74,59,82,0.04);
    vertical-align: top;
}

.orders-table tr:last-child td { border-bottom: none; }
.orders-table tr:hover td { background: rgba(74,59,82,0.015); }

.portal-transfer-info {
    margin-top: 28px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 14px;
    padding: 24px 28px;
}

.transfer-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin-bottom: 4px;
}

.transfer-val {
    display: block;
    font-size: 0.9rem;
    color: var(--brand);
    font-weight: 500;
}
</style>
@endpush
