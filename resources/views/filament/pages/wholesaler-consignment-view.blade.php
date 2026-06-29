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
.wc-title { font-size: 1.4rem; font-weight: 800; color: #3b1f6e; margin-bottom: 4px; }
.wc-sub   { font-size: 0.82rem; color: #7c3aed; }

.wc-tiles {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 36px;
}
@media(max-width:900px) { .wc-tiles { grid-template-columns: repeat(2,1fr); } }
@media(max-width:500px) { .wc-tiles { grid-template-columns: 1fr; } }

.wc-tile {
    background: #fff;
    border: 1px solid #ede9f5;
    border-radius: 14px;
    padding: 20px 20px 16px;
    box-shadow: 0 1px 4px rgba(74,59,82,0.06);
    display: flex; flex-direction: column; gap: 4px;
}
.wc-tile--blue   { border-top: 3px solid #6366f1; }
.wc-tile--orange { border-top: 3px solid #f97316; }
.wc-tile--green  { border-top: 3px solid #22c55e; }
.wc-tile--purple { border-top: 3px solid #9333ea; }

.wc-tile-n {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    color: #3b1f6e;
}
.wc-tile--orange .wc-tile-n { color: #c2410c; }
.wc-tile--green  .wc-tile-n { color: #15803d; }
.wc-tile--purple .wc-tile-n { color: #7e22ce; }

.wc-tile-l {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #7c3aed;
    margin-top: 8px;
}

.wc-section-title {
    font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.09em; color: #7c3aed;
    margin: 32px 0 12px; padding-bottom: 8px; border-bottom: 1px solid #ede9f5;
}

.wc-table { width:100%; border-collapse:collapse; font-size:0.85rem; }
.wc-table th {
    font-size:0.66rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;
    color:#999; padding:8px 14px 10px; border-bottom:1px solid #ede9f5; text-align:left;
}
.wc-table td { padding:12px 14px; border-bottom:1px solid rgba(124,58,237,0.05); color:#3b1f6e; vertical-align:middle; }
.wc-table tr:last-child td { border-bottom:none; }
.wc-table tr:hover td { background:#faf8ff; }

.wc-receipt-btn { display:inline-block; background:#f3e8ff; color:#7c3aed; border:1px solid #ddd6fe; font-size:0.72rem; font-weight:600; padding:3px 10px; border-radius:50px; text-decoration:none; }
.wc-receipt-btn:hover { background:#ede9fe; }
.wc-empty { color:#aaa; font-size:0.85rem; padding:20px 0; }
</style>

@php
    $totals     = $this->getTotals();
    $deliveries = $this->getDeliveries();
    $payments   = $this->getPayments();
@endphp

{{-- Header --}}
<div class="wc-header">
    <div>
        <div class="wc-title">{{ $record->business_name }}</div>
        <div class="wc-sub">{{ $record->city }}, {{ $record->province }} · {{ $record->email }}</div>
    </div>
</div>

{{-- Tiles --}}
<div class="wc-tiles">
    <div class="wc-tile wc-tile--blue">
        <div class="wc-tile-n">{{ $totals['entregadas'] }}</div>
        <div class="wc-tile-l">Entregadas</div>
    </div>
    <div class="wc-tile wc-tile--orange">
        <div class="wc-tile-n">{{ $totals['vendidas'] }}</div>
        <div class="wc-tile-l">Vendidas</div>
    </div>
    <div class="wc-tile wc-tile--green">
        <div class="wc-tile-n">{{ $totals['quedan'] }}</div>
        <div class="wc-tile-l">Quedan en stock</div>
    </div>
    <div class="wc-tile wc-tile--purple">
        <div class="wc-tile-n">${{ number_format($totals['total_pagado'], 0, ',', '.') }}</div>
        <div class="wc-tile-l">Total pagado</div>
    </div>
</div>

{{-- Entregas --}}
<div class="wc-section-title">Entregas registradas</div>
@if($deliveries->isEmpty())
    <p class="wc-empty">Sin entregas registradas todavía.</p>
@else
<table class="wc-table">
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
            <td style="color:#888; font-size:0.83rem;">{{ $d->notes ?: '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Pagos --}}
<div class="wc-section-title">Pagos registrados</div>
@if($payments->isEmpty())
    <p class="wc-empty">Sin pagos registrados todavía.</p>
@else
<table class="wc-table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Cant. vendida</th>
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
            <td>
                @if($p->receipt)
                    <a href="{{ asset('storage/' . $p->receipt) }}" target="_blank" class="wc-receipt-btn">Ver comprobante</a>
                @else
                    <span style="color:#ccc;">—</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

</x-filament-panels::page>
