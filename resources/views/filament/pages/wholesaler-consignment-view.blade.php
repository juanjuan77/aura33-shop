<x-filament-panels::page>
<style>
.wc-header {
    background: linear-gradient(135deg, #faf8ff, #f5f0fb);
    border: 1px solid #ede9f5;
    border-radius: 16px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.wc-title { font-size: 1.5rem; font-weight: 800; color: #3b1f6e; margin-bottom: 4px; }
.wc-sub   { font-size: 0.82rem; color: #7c3aed; }

.wc-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 28px; }
@media(max-width:640px){ .wc-stats { grid-template-columns: 1fr; } }

.wc-stat { border-radius: 14px; padding: 18px 20px; text-align: center; }
.wc-stat--blue   { background:#eff6ff; border:1px solid #bfdbfe; }
.wc-stat--green  { background:#f0fdf4; border:1px solid #bbf7d0; }
.wc-stat--red    { background:#fff5f5; border:1px solid #fecaca; }
.wc-stat-n { font-size:1.8rem; font-weight:800; line-height:1; }
.wc-stat--blue .wc-stat-n   { color:#1d4ed8; }
.wc-stat--green .wc-stat-n  { color:#15803d; }
.wc-stat--red .wc-stat-n    { color:#b91c1c; }
.wc-stat-l { font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-top:6px; opacity:0.75; }

.wc-delivery {
    background: white;
    border: 1px solid #ede9f5;
    border-radius: 16px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(124,58,237,0.06);
}
.wc-delivery-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding: 16px 22px;
    background: linear-gradient(135deg, #faf8ff, #f5f0fb);
    border-bottom: 1px solid #ede9f5;
    cursor: pointer;
}
.wc-delivery-date { font-size:1rem; font-weight:800; color:#3b1f6e; }
.wc-delivery-meta { font-size:0.75rem; color:#7c3aed; margin-top:2px; }
.wc-badge-status {
    font-size:0.7rem; font-weight:700; padding:3px 10px;
    border-radius:50px; text-transform:uppercase; letter-spacing:0.05em;
}
.wc-badge-active { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.wc-badge-closed { background:#f5f5f5; color:#666; border:1px solid #e5e5e5; }
.wc-badge-debe   { background:#fff5f5; color:#b91c1c; border:1px solid #fecaca; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:50px; }
.wc-badge-ok     { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:50px; }

.wc-delivery-body { display:grid; grid-template-columns:1fr 1fr; }
@media(max-width:700px){ .wc-delivery-body { grid-template-columns:1fr; } }

.wc-col { padding:18px 22px; }
.wc-col:first-child { border-right:1px solid #ede9f5; }
.wc-col-title { font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#7c3aed; margin-bottom:12px; }

.wc-mini-table { width:100%; border-collapse:collapse; font-size:0.82rem; }
.wc-mini-table th { font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#999; padding:3px 6px 8px 0; border-bottom:1px solid #ede9f5; text-align:left; }
.wc-mini-table td { padding:8px 6px 8px 0; border-bottom:1px solid rgba(124,58,237,0.04); color:#3b1f6e; }
.wc-mini-table tr:last-child td { border-bottom:none; }
.wc-cat-chip { display:inline-block; font-size:0.65rem; background:#ede9fe; color:#6d28d9; border-radius:50px; padding:1px 7px; margin-bottom:3px; }

.wc-pay-row { padding:10px 0; border-bottom:1px solid rgba(124,58,237,0.05); }
.wc-pay-row:last-child { border-bottom:none; }
.wc-pay-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
.wc-pay-date { font-size:0.78rem; color:#999; }
.wc-pay-amount { font-weight:800; color:#15803d; font-size:0.95rem; }
.wc-pay-tags { display:flex; flex-wrap:wrap; gap:5px; }
.wc-pay-tag { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.7rem; font-weight:600; padding:2px 8px; border-radius:50px; }

.wc-progress { margin-top:16px; padding-top:14px; border-top:1px solid #ede9f5; }
.wc-progress-bar-bg { background:#f0edf8; border-radius:99px; height:7px; }
.wc-progress-bar-fill { height:7px; border-radius:99px; background:linear-gradient(90deg,#7c3aed,#a855f7); }

.wc-empty { text-align:center; padding:40px; color:#a78bfa; font-size:0.88rem; }
.wc-add-btn {
    display:flex; align-items:center; justify-content:center; gap:8px;
    padding:14px; border:2px dashed #c4b5fd; border-radius:14px;
    color:#7c3aed; font-size:0.88rem; font-weight:600; cursor:pointer;
    transition:all 0.2s; text-decoration:none; margin-top:4px;
}
.wc-add-btn:hover { background:#faf8ff; border-color:#7c3aed; }
</style>

@php
    $consignments = $this->getConsignments();
    $totalEntregado = $consignments->sum(fn($c) => $c->items->sum(fn($i) => $i->quantity * $i->unit_price));
    $totalCobrado   = $consignments->sum(fn($c) => $c->payments->sum('amount'));
    $saldo          = $totalEntregado - $totalCobrado;
@endphp

{{-- Header del local --}}
<div class="wc-header">
    <div>
        <div class="wc-title">{{ $record->business_name }}</div>
        <div class="wc-sub">{{ $record->city }}, {{ $record->province }} · {{ $record->email }}</div>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="{{ \App\Filament\Resources\WholesalerConsignmentResource::getUrl('index') }}" style="font-size:0.78rem; color:#a78bfa;">← Todos los locales</a>
    </div>
</div>

{{-- Stats globales --}}
<div class="wc-stats">
    <div class="wc-stat wc-stat--blue">
        <div class="wc-stat-n">${{ number_format($totalEntregado, 0, ',', '.') }}</div>
        <div class="wc-stat-l">Total entregado</div>
    </div>
    <div class="wc-stat wc-stat--green">
        <div class="wc-stat-n">${{ number_format($totalCobrado, 0, ',', '.') }}</div>
        <div class="wc-stat-l">Total cobrado</div>
    </div>
    <div class="wc-stat {{ $saldo > 0 ? 'wc-stat--red' : 'wc-stat--green' }}">
        <div class="wc-stat-n">{{ $saldo > 0 ? '$'.number_format($saldo,0,',','.') : '✓' }}</div>
        <div class="wc-stat-l">{{ $saldo > 0 ? 'Saldo pendiente' : 'Al día' }}</div>
    </div>
</div>

{{-- Entregas --}}
@if($consignments->isEmpty())
<div class="wc-empty">
    <div style="font-size:3rem; margin-bottom:12px;">📦</div>
    <p>No hay entregas registradas todavía.</p>
</div>
@else
@foreach($consignments as $c)
@php
    $cEntregado = $c->items->sum(fn($i) => $i->quantity * $i->unit_price);
    $cCobrado   = $c->payments->sum('amount');
    $cSaldo     = $cEntregado - $cCobrado;
    $cPct       = $cEntregado > 0 ? min(100, round($cCobrado / $cEntregado * 100)) : 0;
    $itemMap    = $c->items->keyBy('id');
@endphp
<div class="wc-delivery">
    <div class="wc-delivery-header">
        <div>
            <div class="wc-delivery-date">
                📅 {{ $c->delivery_date?->format('d/m/Y') ?? $c->created_at->format('d/m/Y') }}
                <span style="font-size:0.78rem; color:#a78bfa; font-weight:400; margin-left:8px;">{{ $c->items->count() }} producto{{ $c->items->count()!=1?'s':'' }} · {{ $c->items->sum('quantity') }} unid.</span>
            </div>
            @if($c->notes)
            <div class="wc-delivery-meta">📝 {{ $c->notes }}</div>
            @endif
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <span class="wc-badge-status {{ $c->status==='active' ? 'wc-badge-active' : 'wc-badge-closed' }}">
                {{ $c->status==='active' ? 'Activa' : 'Cerrada' }}
            </span>
            @if($cSaldo > 0)
                <span class="wc-badge-debe">Debe ${{ number_format($cSaldo,0,',','.') }}</span>
            @else
                <span class="wc-badge-ok">✓ Saldada</span>
            @endif
            <a href="{{ \App\Filament\Resources\ConsignmentResource::getUrl('edit', ['record' => $c->id]) }}"
               style="font-size:0.72rem; color:#7c3aed; padding:3px 10px; border:1px solid #ddd6fe; border-radius:50px; text-decoration:none;">
                ✏️ Editar
            </a>
        </div>
    </div>

    <div class="wc-delivery-body">
        {{-- Productos --}}
        <div class="wc-col">
            <div class="wc-col-title">📦 Productos entregados</div>
            <table class="wc-mini-table">
                <thead><tr><th>Producto</th><th>Cat.</th><th style="text-align:center;">Cant.</th><th style="text-align:right;">Precio</th><th style="text-align:right;">Total</th></tr></thead>
                <tbody>
                    @foreach($c->items as $item)
                    <tr>
                        <td style="font-weight:600;">{{ $item->product?->name ?? $item->product_name }}</td>
                        <td><span class="wc-cat-chip">{{ $item->product?->category?->name ?? '—' }}</span></td>
                        <td style="text-align:center; font-weight:700; color:#1d4ed8;">{{ $item->quantity }}</td>
                        <td style="text-align:right; color:#555;">${{ number_format($item->unit_price,0,',','.') }}</td>
                        <td style="text-align:right; font-weight:700;">${{ number_format($item->quantity*$item->unit_price,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="font-weight:800; color:#7c3aed; border-top:1px solid #ede9f5; padding-top:10px;">Total</td>
                        <td style="text-align:center; font-weight:800; color:#1d4ed8; border-top:1px solid #ede9f5; padding-top:10px;">{{ $c->items->sum('quantity') }}</td>
                        <td></td>
                        <td style="text-align:right; font-weight:800; color:#3b1f6e; border-top:1px solid #ede9f5; padding-top:10px;">${{ number_format($cEntregado,0,',','.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagos --}}
        <div class="wc-col">
            <div class="wc-col-title">💳 Pagos recibidos</div>
            @if($c->payments->isEmpty())
                <p style="font-size:0.82rem; color:#aaa; padding:8px 0;">Sin pagos registrados.</p>
            @else
                @foreach($c->payments as $pay)
                @php $sold = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold; @endphp
                <div class="wc-pay-row">
                    <div class="wc-pay-top">
                        <span class="wc-pay-date">{{ $pay->created_at->format('d/m/Y') }}</span>
                        <span class="wc-pay-amount">${{ number_format($pay->amount,0,',','.') }}</span>
                    </div>
                    @if(is_array($sold) && count($sold))
                    <div class="wc-pay-tags">
                        @foreach($sold as $s)
                        @php
                            $sid  = (int)($s['consignment_item_id'] ?? 0);
                            $name = $itemMap->has($sid) ? ($itemMap->get($sid)->product?->name ?? $itemMap->get($sid)->product_name) : null;
                            $qty  = $s['qty_sold'] ?? 0;
                            $qp   = $s['qty_paid'] ?? $qty;
                        @endphp
                        @if($name)
                        <span class="wc-pay-tag">{{ $name }} ×{{ $qty }}{{ $qp < $qty ? ' (pag:'.($qp).')' : '' }}</span>
                        @endif
                        @endforeach
                    </div>
                    @endif
                    @if($pay->notes)
                    <div style="font-size:0.72rem; color:#999; margin-top:4px;">{{ $pay->notes }}</div>
                    @endif
                </div>
                @endforeach
            @endif

            {{-- Barra progreso --}}
            <div class="wc-progress">
                <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:#999; margin-bottom:5px;">
                    <span>Cobrado ${{ number_format($cCobrado,0,',','.') }} de ${{ number_format($cEntregado,0,',','.') }}</span>
                    <span>{{ $cPct }}%</span>
                </div>
                <div class="wc-progress-bar-bg">
                    <div class="wc-progress-bar-fill" style="width:{{ max($cPct,2) }}%; background:{{ $cSaldo<=0 ? '#22c55e' : 'linear-gradient(90deg,#7c3aed,#a855f7)' }};"></div>
                </div>
                @if($cSaldo > 0)
                <div style="font-size:0.75rem; color:#b91c1c; font-weight:700; margin-top:6px;">Saldo: ${{ number_format($cSaldo,0,',','.') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

</x-filament-panels::page>
