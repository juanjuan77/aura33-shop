<x-filament-panels::page>
<style>
.wc-header {
    background: rgb(var(--color-primary-50) / 0.4);
    border: 1px solid rgb(var(--color-gray-200));
    border-radius: 16px;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.dark .wc-header { background: rgb(var(--color-gray-800) / 0.5); border-color: rgb(var(--color-gray-700)); }
.wc-title { font-size: 1.4rem; font-weight: 800; color: rgb(var(--color-gray-900)); margin-bottom: 4px; }
.dark .wc-title { color: rgb(var(--color-gray-100)); }
.wc-sub { font-size: 0.82rem; color: rgb(var(--color-primary-600)); }
.dark .wc-sub { color: rgb(var(--color-primary-400)); }

.wc-tiles {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 36px;
}
@media(max-width:900px) { .wc-tiles { grid-template-columns: repeat(2,1fr); } }
@media(max-width:500px) { .wc-tiles { grid-template-columns: 1fr; } }

.wc-tile {
    background: rgb(var(--color-white));
    border: 1px solid rgb(var(--color-gray-200));
    border-radius: 14px;
    padding: 20px 20px 16px;
    box-shadow: 0 1px 4px rgb(0 0 0 / 0.06);
    display: flex; flex-direction: column; gap: 4px;
}
.dark .wc-tile { background: rgb(var(--color-gray-800)); border-color: rgb(var(--color-gray-700)); }
.wc-tile--blue   { border-top: 3px solid #6366f1; }
.wc-tile--orange { border-top: 3px solid #f97316; }
.wc-tile--green  { border-top: 3px solid #22c55e; }
.wc-tile--purple { border-top: 3px solid #9333ea; }

.wc-tile-n {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    color: rgb(var(--color-gray-900));
}
.dark .wc-tile-n { color: rgb(var(--color-gray-100)); }
.wc-tile--orange .wc-tile-n { color: #f97316; }
.wc-tile--green  .wc-tile-n { color: #22c55e; }
.wc-tile--purple .wc-tile-n { color: #a855f7; }

.wc-tile-l {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: rgb(var(--color-gray-500));
    margin-top: 8px;
}

.wc-section-title {
    font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.09em;
    color: rgb(var(--color-gray-500));
    margin: 32px 0 12px; padding-bottom: 8px;
    border-bottom: 1px solid rgb(var(--color-gray-200));
}
.dark .wc-section-title { border-color: rgb(var(--color-gray-700)); }

.wc-table { width:100%; border-collapse:collapse; font-size:0.85rem; }
.wc-table th {
    font-size:0.66rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;
    color: rgb(var(--color-gray-400));
    padding:8px 14px 10px;
    border-bottom: 1px solid rgb(var(--color-gray-200));
    text-align:left;
}
.dark .wc-table th { border-color: rgb(var(--color-gray-700)); }
.wc-table td {
    padding:12px 14px;
    border-bottom: 1px solid rgb(var(--color-gray-100));
    color: rgb(var(--color-gray-800));
    vertical-align:middle;
}
.dark .wc-table td { border-color: rgb(var(--color-gray-700)); color: rgb(var(--color-gray-200)); }
.wc-table tr:last-child td { border-bottom:none; }
.wc-table tr:hover td { background: rgb(var(--color-gray-50)); }
.dark .wc-table tr:hover td { background: rgb(var(--color-gray-700) / 0.4); }

.wc-receipt-btn { display:inline-block; background: rgb(var(--color-primary-50)); color: rgb(var(--color-primary-700)); border: 1px solid rgb(var(--color-primary-200)); font-size:0.72rem; font-weight:600; padding:3px 10px; border-radius:50px; text-decoration:none; }
.dark .wc-receipt-btn { background: rgb(var(--color-primary-900) / 0.3); color: rgb(var(--color-primary-300)); border-color: rgb(var(--color-primary-700)); }
.wc-receipt-btn:hover { opacity: 0.85; }
.wc-empty { color: rgb(var(--color-gray-400)); font-size:0.85rem; padding:20px 0; }
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
            <td>{{ ($d->date ?? $d->created_at)->format('d/m/Y') }}</td>
            <td><strong>{{ $d->quantity }}</strong></td>
            <td class="wc-empty" style="padding:12px 14px; font-size:0.83rem;">{{ $d->notes ?: '—' }}</td>
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
            <td>{{ ($p->date ?? $p->created_at)->format('d/m/Y') }}</td>
            <td>{{ $p->product_name }}</td>
            <td>{{ $p->quantity }}</td>
            <td><strong>${{ number_format($p->amount, 0, ',', '.') }}</strong></td>
            <td>
                @if($p->receipt)
                    <a href="{{ asset('storage/' . $p->receipt) }}" target="_blank" class="wc-receipt-btn">Ver comprobante</a>
                @else
                    <span class="wc-empty">—</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

</x-filament-panels::page>
