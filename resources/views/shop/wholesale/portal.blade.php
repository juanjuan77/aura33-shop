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

        {{-- ── CONSIGNACIONES ────────────────────────────── --}}
        @if($consignments->isNotEmpty())
        <div style="margin-top: 60px;">
            <h2 style="font-family:var(--font-serif); font-size:1.6rem; color:var(--brand); font-weight:400; margin-bottom:6px;">
                Cuenta corriente en consignación
            </h2>
            <p style="font-size:0.88rem; color:var(--muted); margin-bottom:28px;">
                Productos entregados en tu local, lo que se vendió y los pagos recibidos.
            </p>

            {{-- Resumen global --}}
            <div class="consign-summary-grid" style="margin-bottom:40px;">
                <div class="consign-summary-card">
                    <span class="consign-summary-label">Total entregado</span>
                    <span class="consign-summary-val">${{ number_format($totalDebt, 0, ',', '.') }}</span>
                </div>
                <div class="consign-summary-card">
                    <span class="consign-summary-label">Total cobrado</span>
                    <span class="consign-summary-val consign-summary-val--paid">${{ number_format($totalPaid, 0, ',', '.') }}</span>
                </div>
                <div class="consign-summary-card {{ $pendingBalance > 0 ? 'consign-summary-card--pending' : 'consign-summary-card--ok' }}">
                    <span class="consign-summary-label">Saldo pendiente</span>
                    <span class="consign-summary-val consign-summary-val--{{ $pendingBalance > 0 ? 'pending' : 'ok' }}">
                        @if($pendingBalance <= 0)
                            ✓ Al día
                        @else
                            ${{ number_format($pendingBalance, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>

            {{-- Informe por producto --}}
            @if($reportByProduct->isNotEmpty())
            <div class="consign-report-wrap">
                <h3 class="consign-section-title" style="margin-bottom:16px;">📊 Informe por producto</h3>
                <div class="orders-table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Producto</th>
                                <th style="text-align:center;">Entregados</th>
                                <th style="text-align:center;">Vendidos</th>
                                <th style="text-align:center;">Stock tuyo</th>
                                <th style="text-align:center;">Pagados</th>
                                <th style="text-align:center;">Debés</th>
                                <th style="text-align:right;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $prevCat = null; @endphp
                            @foreach($reportByProduct as $row)
                            @if($row['category'] !== $prevCat)
                            <tr style="background:rgba(74,59,82,0.03);">
                                <td colspan="8" style="padding:8px 18px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--muted);">
                                    {{ $row['category'] }}
                                </td>
                            </tr>
                            @php $prevCat = $row['category']; @endphp
                            @endif
                            <tr>
                                <td></td>
                                <td style="font-weight:600; font-size:0.88rem;">{{ $row['product_name'] }}</td>
                                <td style="text-align:center;"><span class="report-badge report-badge--blue">{{ $row['delivered'] }}</span></td>
                                <td style="text-align:center;"><span class="report-badge report-badge--purple">{{ $row['sold'] }}</span></td>
                                <td style="text-align:center;"><span class="report-badge report-badge--gray">{{ $row['stock'] }}</span></td>
                                <td style="text-align:center;"><span class="report-badge report-badge--green">{{ $row['paid_qty'] }}</span></td>
                                <td style="text-align:center;">
                                    @if($row['debe'] > 0)
                                        <span class="report-badge report-badge--red">{{ $row['debe'] }}</span>
                                    @else
                                        <span style="color:#15803d; font-weight:700; font-size:0.8rem;">✓</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:700; font-size:0.88rem; color:{{ $row['debe_amount'] > 0 ? '#b91c1c' : '#15803d' }};">
                                    @if($row['debe_amount'] > 0)
                                        ${{ number_format($row['debe_amount'], 0, ',', '.') }}
                                    @else —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Gráfico ventas por categoría --}}
                @php
                    $chartCats = $reportByProduct->groupBy('category')->map(fn($r) => $r->sum('sold'))->filter()->sortDesc();
                    $chartMax  = $chartCats->max() ?: 1;
                @endphp
                @if($chartCats->isNotEmpty())
                <div style="margin-top:24px; background:var(--white); border:1px solid var(--border); border-radius:14px; padding:24px; box-shadow:var(--shadow-soft);">
                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--muted); margin-bottom:20px;">
                        Más vendido por categoría
                    </div>
                    @foreach($chartCats as $cat => $qty)
                    @php $pct = round($qty / $chartMax * 100); @endphp
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <div style="width:120px; font-size:0.78rem; color:var(--text); text-align:right; flex-shrink:0;">{{ $cat }}</div>
                        <div style="flex:1; background:#f0edf4; border-radius:99px; height:28px; position:relative; overflow:hidden;">
                            <div style="height:28px; border-radius:99px; background:linear-gradient(90deg,#7c3aed,#a855f7); display:flex; align-items:center; justify-content:flex-end; padding-right:10px; width:{{ max($pct, 10) }}%;">
                                <span style="font-size:0.75rem; font-weight:700; color:white;">{{ $qty }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- Una card por cada entrega --}}
            @foreach($consignments as $c)
            @php
                $cDelivered = $c->totalDelivered();
                $cPaid      = $c->payments->sum('amount');
                $cSaldo     = $cDelivered - $cPaid;
                $cPct       = $cDelivered > 0 ? min(100, round($cPaid / $cDelivered * 100)) : 0;
                $itemMap    = $c->items->keyBy('id');
            @endphp
            <div class="consign-card">

                {{-- Card header --}}
                <div class="consign-card-header">
                    <div>
                        <span class="consign-card-date">{{ $c->created_at->format('d/m/Y') }}</span>
                        <span class="consign-card-badge {{ $c->status === 'active' ? 'badge-active' : 'badge-closed' }}">
                            {{ $c->status === 'active' ? 'Activa' : 'Cerrada' }}
                        </span>
                    </div>
                    <div class="consign-card-totals">
                        <span class="consign-card-total-label">Total entregado:</span>
                        <span class="consign-card-total-val">${{ number_format($cDelivered, 0, ',', '.') }}</span>
                        @if($cSaldo <= 0)
                            <span class="badge-saldado">✓ Saldado</span>
                        @else
                            <span class="badge-debe">Debe ${{ number_format($cSaldo, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>

                <div class="consign-card-body">

                    {{-- Productos entregados --}}
                    <div class="consign-col">
                        <div class="consign-col-title">📦 Productos entregados</div>
                        <table class="consign-mini-table">
                            <thead><tr><th>Producto</th><th>Cant.</th><th>Precio u.</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                @foreach($c->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td style="text-align:center;">{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td style="font-weight:600;">${{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagos recibidos --}}
                    <div class="consign-col">
                        <div class="consign-col-title">💳 Pagos recibidos</div>

                        @if($c->payments->isEmpty())
                            <p style="font-size:0.85rem; color:var(--muted); padding:12px 0;">Sin pagos registrados aún.</p>
                        @else
                            @foreach($c->payments as $pay)
                            <div class="consign-pay-row">
                                <div class="consign-pay-top">
                                    <span class="consign-pay-date">{{ $pay->created_at->format('d/m/Y') }}</span>
                                    <span class="consign-pay-amount">${{ number_format($pay->amount, 0, ',', '.') }}</span>
                                </div>
                                @if($pay->items_sold)
                                @php
                                    $sold = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
                                    $totalUnits = collect($sold)->sum(fn($s) => (int)($s['qty_sold'] ?? 0));
                                @endphp
                                @if(is_array($sold) && count($sold))
                                <div class="consign-pay-items">
                                    @foreach($sold as $s)
                                    @php
                                        $sid  = (int)($s['consignment_item_id'] ?? 0);
                                        $name = $itemMap->has($sid) ? $itemMap->get($sid)->product_name : '?';
                                        $qty  = $s['qty_sold'] ?? '?';
                                    @endphp
                                    <span class="consign-pay-tag">{{ $name }} <strong>×{{ $qty }}</strong></span>
                                    @endforeach
                                    <span class="consign-pay-tag-units">{{ $totalUnits }} unid. vendidas</span>
                                </div>
                                @endif
                                @endif
                                @if($pay->notes)
                                <div style="font-size:0.78rem; color:var(--muted); margin-top:4px;">{{ $pay->notes }}</div>
                                @endif
                            </div>
                            @endforeach
                        @endif

                        {{-- Barra progreso --}}
                        <div class="consign-progress-wrap">
                            <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--muted); margin-bottom:6px;">
                                <span>Cobrado: ${{ number_format($cPaid, 0, ',', '.') }}</span>
                                <span>{{ $cPct }}%</span>
                            </div>
                            <div class="consign-progress-bar">
                                <div class="consign-progress-fill" style="width:{{ $cPct }}%; background:{{ $cSaldo <= 0 ? '#22c55e' : '#f59e0b' }};"></div>
                            </div>
                            @if($cSaldo > 0)
                            <div style="font-size:0.78rem; color:#b91c1c; margin-top:6px; font-weight:600;">
                                Saldo pendiente: ${{ number_format($cSaldo, 0, ',', '.') }}
                            </div>
                            @endif
                        </div>
                    </div>

                </div>{{-- /card-body --}}
            </div>
            @endforeach

        </div>
        @endif

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

.consign-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}
@media (max-width: 600px) { .consign-summary-grid { grid-template-columns: 1fr; } }

.consign-summary-card {
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 12px;
    padding: 20px 22px;
    box-shadow: var(--shadow-soft);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.consign-summary-card--pending { border-color: rgba(180,60,60,0.15); background: #fff8f8; }
.consign-summary-card--ok      { border-color: rgba(60,160,60,0.15); background: #f8fff8; }

.consign-summary-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    font-weight: 600;
}
.consign-summary-val {
    font-family: var(--font-serif);
    font-size: 1.5rem;
    color: var(--brand);
    font-weight: 400;
}
.consign-summary-val--paid    { color: #15803d; }
.consign-summary-val--pending { color: #b91c1c; }
.consign-summary-val--ok      { color: #15803d; }

.consign-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    margin-bottom: 24px;
}

.consign-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding: 18px 24px;
    background: rgba(74,59,82,0.03);
    border-bottom: 1px solid var(--border);
}

.consign-card-date {
    font-size: 0.82rem;
    color: var(--muted);
    margin-right: 10px;
}

.consign-card-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.badge-active { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-closed { background:#f5f5f5; color:#666; border:1px solid #e5e5e5; }
.badge-saldado { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:50px; }
.badge-debe { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:50px; }

.consign-card-totals {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.consign-card-total-label { font-size:0.78rem; color:var(--muted); }
.consign-card-total-val { font-weight:700; color:var(--brand); font-size:1rem; }

.consign-card-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}
@media (max-width: 700px) { .consign-card-body { grid-template-columns: 1fr; } }

.consign-col {
    padding: 20px 24px;
}
.consign-col:first-child {
    border-right: 1px solid var(--border);
}
@media (max-width: 700px) { .consign-col:first-child { border-right: none; border-bottom: 1px solid var(--border); } }

.consign-col-title {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--brand);
    margin-bottom: 14px;
}

.consign-mini-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.83rem;
}
.consign-mini-table th {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--muted);
    padding: 4px 8px 8px 0;
    border-bottom: 1px solid var(--border);
    text-align: left;
}
.consign-mini-table td {
    padding: 8px 8px 8px 0;
    border-bottom: 1px solid rgba(74,59,82,0.04);
    color: var(--text);
}
.consign-mini-table tr:last-child td { border-bottom: none; }

.consign-pay-row {
    padding: 12px 0;
    border-bottom: 1px solid rgba(74,59,82,0.05);
}
.consign-pay-row:last-of-type { border-bottom: none; }

.consign-pay-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.consign-pay-date { font-size:0.8rem; color:var(--muted); }
.consign-pay-amount { font-weight:700; color:#15803d; font-size:0.95rem; }

.consign-pay-items {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.consign-pay-tag {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 50px;
}
.consign-pay-tag-units {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 50px;
}

.consign-progress-wrap {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
}
.consign-progress-bar {
    background: #e5e7eb;
    border-radius: 99px;
    height: 8px;
}
.consign-progress-fill {
    height: 8px;
    border-radius: 99px;
    transition: width 0.4s ease;
    min-width: 4px;
}

.consign-report-wrap { margin-bottom: 40px; }

.report-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
}
.report-badge--blue   { background:#eff6ff; color:#1d4ed8; }
.report-badge--purple { background:#faf5ff; color:#7e22ce; }
.report-badge--gray   { background:#f5f5f5; color:#555; }
.report-badge--green  { background:#f0fdf4; color:#15803d; }
.report-badge--red    { background:#fef2f2; color:#b91c1c; }
</style>
@endpush
