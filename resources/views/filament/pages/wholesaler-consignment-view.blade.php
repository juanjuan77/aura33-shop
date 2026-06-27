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
.wc-table td { padding:12px 14px; border-bottom:1px solid rgba(124,58,237,0.05); color:#3b1f6e; vertical-align:top; }
.wc-table tr:last-child td { border-bottom:none; }
.wc-table tr:hover td { background:#faf8ff; }
.wc-pay-tag { display:inline-block; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.7rem; font-weight:600; padding:2px 8px; border-radius:50px; margin:2px 2px 2px 0; }
.wc-receipt-btn { display:inline-block; background:#f3e8ff; color:#7c3aed; border:1px solid #ddd6fe; font-size:0.72rem; font-weight:600; padding:3px 10px; border-radius:50px; text-decoration:none; }
.wc-receipt-btn:hover { background:#ede9fe; }
</style>

@php
    $payments = \App\Models\ConsignmentPayment::where('wholesale_request_id', $this->record->id)
        ->orderByDesc('created_at')
        ->get();
    $allItems = \App\Models\ConsignmentItem::whereHas('consignment', fn($q) =>
            $q->where('wholesale_request_id', $this->record->id)
        )->with('product')->get()->keyBy('id');
    $totalPagado = $payments->sum('amount');
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

{{-- Pagos --}}
<div class="wc-section-title">💳 Pagos registrados</div>

@if($payments->isEmpty())
    <p style="color:#aaa; font-size:0.85rem; padding:20px 0;">Sin pagos registrados todavía. Usá "💳 Registrar pago" para agregar uno.</p>
@else
<div style="background:white; border:1px solid #ede9f5; border-radius:14px; overflow:hidden; box-shadow:0 2px 8px rgba(124,58,237,0.05);">
    <table class="wc-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Productos vendidos</th>
                <th style="text-align:right;">Monto</th>
                <th>Comprobante</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $pay)
            @php
                $sold = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            @endphp
            <tr>
                <td style="white-space:nowrap; font-weight:700;">{{ $pay->created_at->format('d/m/Y') }}</td>
                <td>
                    @if(is_array($sold) && count($sold))
                        @foreach($sold as $s)
                        @php
                            $sid  = (int)($s['consignment_item_id'] ?? 0);
                            $item = $allItems->get($sid);
                            $name = $item ? ($item->product?->name ?? $item->product_name) : '?';
                            $qty  = $s['qty_sold'] ?? 0;
                            $qp   = $s['qty_paid'] ?? $qty;
                        @endphp
                        <span class="wc-pay-tag">
                            {{ $name }} ×{{ $qty }}{{ $qp < $qty ? ' (paga '.($qp).')' : '' }}
                        </span>
                        @endforeach
                    @else
                        <span style="color:#ccc;">—</span>
                    @endif
                </td>
                <td style="text-align:right; font-weight:800; color:#15803d; font-size:1rem; white-space:nowrap;">
                    ${{ number_format($pay->amount, 0, ',', '.') }}
                </td>
                <td>
                    @if($pay->receipt)
                        <a href="/storage/{{ $pay->receipt }}" target="_blank" class="wc-receipt-btn">Ver 📎</a>
                    @else
                        <span style="color:#ccc; font-size:0.78rem;">Sin comprobante</span>
                    @endif
                </td>
                <td style="font-size:0.8rem; color:#888;">{{ $pay->notes ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#faf8ff;">
                <td colspan="2" style="padding:14px; font-weight:800; color:#3b1f6e;">TOTAL COBRADO</td>
                <td style="text-align:right; font-weight:800; color:#15803d; font-size:1.05rem; padding:14px;">${{ number_format($totalPagado, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

</x-filament-panels::page>
