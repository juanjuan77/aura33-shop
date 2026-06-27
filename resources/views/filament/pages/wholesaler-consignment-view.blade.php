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
    margin-bottom: 24px;
}
.wc-title { font-size: 1.5rem; font-weight: 800; color: #3b1f6e; margin-bottom: 4px; }
.wc-sub   { font-size: 0.82rem; color: #7c3aed; }
.wc-section-title {
    font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.09em; color: #7c3aed;
    margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #ede9f5;
}
.wc-table { width:100%; border-collapse:collapse; font-size:0.85rem; }
.wc-table th {
    font-size:0.66rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;
    color:#999; padding:6px 14px 10px; border-bottom:1px solid #ede9f5; text-align:left;
}
.wc-table td { padding:12px 14px; border-bottom:1px solid rgba(124,58,237,0.05); color:#3b1f6e; }
.wc-table tr:last-child td { border-bottom:none; }
.wc-table tr:hover td { background:#faf8ff; }
.wc-edit-btn {
    font-size:0.72rem; color:#7c3aed; padding:4px 12px;
    border:1px solid #ddd6fe; border-radius:50px;
    text-decoration:none; white-space:nowrap;
    transition: background 0.15s;
}
.wc-edit-btn:hover { background:#f3e8ff; }
.wc-badge-active { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }
.wc-badge-closed { background:#f5f5f5; color:#666; border:1px solid #e5e5e5; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }
.wc-badge-debe   { background:#fff5f5; color:#b91c1c; border:1px solid #fecaca; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }
.wc-badge-ok     { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }
</style>

@php
    $consignments = $this->getConsignments();
@endphp

{{-- Header del local --}}
<div class="wc-header">
    <div>
        <div class="wc-title">{{ $record->business_name }}</div>
        <div class="wc-sub">{{ $record->city }}, {{ $record->province }} · {{ $record->email }}</div>
    </div>
    <a href="{{ \App\Filament\Resources\WholesalerConsignmentResource::getUrl('index') }}"
       style="font-size:0.78rem; color:#a78bfa; text-decoration:none;">← Todos los locales</a>
</div>

{{-- Tabla de entregas --}}
<div class="wc-section-title">📦 Entregas registradas</div>

@if($consignments->isEmpty())
    <p style="color:#aaa; font-size:0.85rem; padding:20px 0;">No hay entregas. Usá "+ Nueva entrega" para registrar la primera.</p>
@else
<div style="background:white; border:1px solid #ede9f5; border-radius:14px; overflow:hidden; box-shadow:0 2px 8px rgba(124,58,237,0.05);">
    <table class="wc-table">
        <thead>
            <tr>
                <th>Fecha entrega</th>
                <th>Productos</th>
                <th style="text-align:right;">Total entregado</th>
                <th style="text-align:right;">Cobrado</th>
                <th style="text-align:center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignments as $c)
            @php
                $cEntregado = $c->items->sum(fn($i) => $i->quantity * $i->unit_price);
                $cCobrado   = $c->payments->sum('amount');
                $cSaldo     = $cEntregado - $cCobrado;
            @endphp
            <tr>
                <td style="font-weight:700; white-space:nowrap;">
                    {{ $c->delivery_date?->format('d/m/Y') ?? $c->created_at->format('d/m/Y') }}
                    @if($c->notes)
                    <div style="font-size:0.72rem; color:#a78bfa; margin-top:2px;">{{ $c->notes }}</div>
                    @endif
                </td>
                <td style="font-size:0.8rem; color:#555;">
                    @foreach($c->items as $item)
                        <div>{{ $item->product?->name ?? $item->product_name }} ×{{ $item->quantity }}</div>
                    @endforeach
                </td>
                <td style="text-align:right; font-weight:700;">${{ number_format($cEntregado,0,',','.') }}</td>
                <td style="text-align:right; color:#15803d; font-weight:700;">${{ number_format($cCobrado,0,',','.') }}</td>
                <td style="text-align:center;">
                    <div style="display:flex; gap:5px; justify-content:center; flex-wrap:wrap;">
                        <span class="{{ $c->status==='active' ? 'wc-badge-active' : 'wc-badge-closed' }}">
                            {{ $c->status==='active' ? 'Activa' : 'Cerrada' }}
                        </span>
                        @if($cSaldo > 0)
                            <span class="wc-badge-debe">Debe ${{ number_format($cSaldo,0,',','.') }}</span>
                        @else
                            <span class="wc-badge-ok">✓</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

</x-filament-panels::page>
