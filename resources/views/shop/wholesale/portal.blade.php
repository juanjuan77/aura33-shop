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
            <h2 style="font-family:var(--font-serif); font-size:1.6rem; color:var(--brand); font-weight:400; margin-bottom:28px;">
                Mi cuenta en consignación
            </h2>

            {{-- 1. RESUMEN EN 4 TILES --}}
            @php
                $totalUnidadesEntregadas = $consignments->sum(fn($c) => $c->items->sum('quantity'));
                $totalUnidadesVendidas   = $reportByProduct->sum('sold');
                $totalEnStock            = $reportByProduct->sum('stock');
            @endphp
            <div class="cp-tiles">
                <div class="cp-tile cp-tile--brand">
                    <div class="cp-tile-n">${{ number_format($totalDebt, 0, ',', '.') }}</div>
                    <div class="cp-tile-l">Total en consignación</div>
                    <div class="cp-tile-sub">{{ $totalUnidadesEntregadas }} unidades entregadas</div>
                </div>
                <div class="cp-tile cp-tile--purple">
                    <div class="cp-tile-n">{{ $totalEnStock }}</div>
                    <div class="cp-tile-l">Unidades en tu local</div>
                    <div class="cp-tile-sub">todavía no vendidas</div>
                </div>
                <div class="cp-tile cp-tile--green">
                    <div class="cp-tile-n">${{ number_format($totalPaid, 0, ',', '.') }}</div>
                    <div class="cp-tile-l">Total pagado</div>
                    <div class="cp-tile-sub">{{ $allPayments->count() }} pago{{ $allPayments->count()!=1?'s':'' }} registrados</div>
                </div>
                <div class="cp-tile {{ $pendingBalance > 0 ? 'cp-tile--red' : 'cp-tile--ok' }}">
                    <div class="cp-tile-n">{{ $pendingBalance > 0 ? '$'.number_format($pendingBalance,0,',','.') : '✓' }}</div>
                    <div class="cp-tile-l">{{ $pendingBalance > 0 ? 'Saldo que debés' : 'Al día' }}</div>
                    <div class="cp-tile-sub">{{ $pendingBalance > 0 ? 'ventas realizadas sin pagar' : 'todo pagado' }}</div>
                </div>
            </div>

            {{-- 2. STOCK ACTUAL POR PRODUCTO --}}
            @if($reportByProduct->isNotEmpty())
            <div class="cp-section">
                <div class="cp-section-title">📦 Stock actual en tu local</div>
                <div class="orders-table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align:center;">Recibiste</th>
                                <th style="text-align:center;">Vendiste</th>
                                <th style="text-align:center;">Te quedan</th>
                                <th style="text-align:center;">Pagaste</th>
                                <th style="text-align:right;">Debés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportByProduct as $row)
                            <tr>
                                <td>
                                    <div style="font-weight:600; font-size:0.88rem; color:var(--brand);">{{ $row['product_name'] }}</div>
                                    <div style="font-size:0.72rem; color:var(--muted);">{{ $row['category'] }}</div>
                                </td>
                                <td style="text-align:center;"><span class="rp-badge rp-badge--blue">{{ $row['delivered'] }}</span></td>
                                <td style="text-align:center;"><span class="rp-badge rp-badge--purple">{{ $row['sold'] }}</span></td>
                                <td style="text-align:center;"><span class="rp-badge {{ $row['stock'] > 0 ? 'rp-badge--gray' : 'rp-badge--ok' }}">{{ $row['stock'] }}</span></td>
                                <td style="text-align:center;"><span class="rp-badge rp-badge--green">{{ $row['paid_qty'] }}</span></td>
                                <td style="text-align:right; font-weight:700; font-size:0.88rem;">
                                    @if($row['debe'] > 0)
                                        <span style="color:#b91c1c;">${{ number_format($row['debe_amount'],0,',','.') }}</span>
                                        <span style="font-size:0.72rem; color:#b91c1c; font-weight:400;"> ({{ $row['debe'] }} u.)</span>
                                    @else
                                        <span style="color:#15803d; font-weight:700;">✓ Al día</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- 3. MIS PAGOS --}}
            <div class="cp-section">
                <div class="cp-section-title">💳 Mis pagos realizados</div>
                @if($allPayments->isEmpty())
                    <div class="portal-empty" style="padding:32px;">
                        <p style="color:var(--muted); font-size:0.88rem;">Todavía no hay pagos registrados.</p>
                    </div>
                @else
                <div class="orders-table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Productos vendidos en ese pago</th>
                                <th style="text-align:right;">Monto pagado</th>
                                <th style="text-align:center;">Comprobante</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allPayments as $pay)
                            @php
                                $soldArr = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
                            @endphp
                            <tr>
                                <td style="white-space:nowrap; font-size:0.85rem; color:var(--muted);">{{ $pay->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if(is_array($soldArr) && count($soldArr))
                                    <div style="display:flex; flex-wrap:wrap; gap:5px;">
                                        @foreach($soldArr as $s)
                                        @php
                                            $sid  = (int)($s['consignment_item_id'] ?? 0);
                                            $item = $globalItemMap->get($sid);
                                            $name = $item ? ($item->product?->name ?? $item->product_name) : null;
                                            $qty  = $s['qty_sold'] ?? 0;
                                        @endphp
                                        @if($name && $qty)
                                        <span class="rp-tag">{{ $name }} ×{{ $qty }}</span>
                                        @endif
                                        @endforeach
                                    </div>
                                    @else
                                        <span style="color:var(--muted); font-size:0.82rem;">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:700; color:#15803d; font-size:0.95rem;">${{ number_format($pay->amount,0,',','.') }}</td>
                                <td style="text-align:center;">
                                    @if($pay->receipt)
                                        <a href="/storage/{{ $pay->receipt }}" target="_blank" class="rp-receipt-btn">Ver 📎</a>
                                    @else
                                        <span style="color:var(--muted); font-size:0.78rem;">—</span>
                                    @endif
                                </td>
                                <td style="font-size:0.82rem; color:var(--muted);">{{ $pay->notes ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:rgba(74,59,82,0.03);">
                                <td colspan="2" style="padding:14px 18px; font-weight:700; color:var(--brand);">TOTAL PAGADO</td>
                                <td style="text-align:right; font-weight:800; color:#15803d; font-size:1rem; padding:14px 18px;">${{ number_format($totalPaid,0,',','.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

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

/* ── Consignment portal nuevo ── */
.cp-tiles {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 40px;
}
@media (max-width: 900px) { .cp-tiles { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 500px) { .cp-tiles { grid-template-columns: 1fr; } }

.cp-tile {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 22px 20px 18px;
    box-shadow: var(--shadow-soft);
    display: flex; flex-direction: column; gap: 4px;
}
.cp-tile--brand { border-top: 3px solid var(--brand); }
.cp-tile--purple { border-top: 3px solid #9333ea; }
.cp-tile--green  { border-top: 3px solid #22c55e; }
.cp-tile--red    { border-top: 3px solid #ef4444; }
.cp-tile--ok     { border-top: 3px solid #22c55e; }

.cp-tile-n {
    font-family: var(--font-serif);
    font-size: 1.6rem;
    color: var(--brand);
    font-weight: 400;
    line-height: 1;
}
.cp-tile--green  .cp-tile-n { color: #15803d; }
.cp-tile--red    .cp-tile-n { color: #b91c1c; }
.cp-tile--ok     .cp-tile-n { color: #15803d; font-size: 2rem; }

.cp-tile-l {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--brand);
    margin-top: 8px;
}
.cp-tile-sub {
    font-size: 0.72rem;
    color: var(--muted);
}

.cp-section {
    margin-bottom: 40px;
}
.cp-section-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: var(--brand);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}

.rp-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
}
.rp-badge--blue   { background:#eff6ff; color:#1d4ed8; }
.rp-badge--purple { background:#faf5ff; color:#7e22ce; }
.rp-badge--gray   { background:#f5f5f5; color:#555; }
.rp-badge--green  { background:#f0fdf4; color:#15803d; }
.rp-badge--red    { background:#fef2f2; color:#b91c1c; }
.rp-badge--ok     { background:#f0fdf4; color:#15803d; }

.rp-tag {
    display: inline-block;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 50px;
}

.rp-receipt-btn {
    display: inline-block;
    background: #faf5ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 50px;
    text-decoration: none;
    transition: background 0.2s;
}
.rp-receipt-btn:hover { background: #f3e8ff; color: #6b21a8; }
</style>
@endpush
