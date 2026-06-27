<x-filament-panels::page>

@php $requests = $this->getRequests(); @endphp

@if($requests->isEmpty())
    <div style="text-align:center; padding:3rem; color:var(--gray-400);">
        <div style="font-size:2.5rem;">📭</div>
        <p style="margin-top:0.5rem;">No hay pedidos de reposición por ahora.</p>
    </div>
@else

<div style="display:flex; flex-direction:column; gap:1rem;">
@foreach($requests as $req)
@php
    $statusColor = match($req->status) {
        'pending'   => '#d97706',
        'seen'      => '#2563eb',
        'completed' => '#16a34a',
        default     => '#6b7280',
    };
    $statusLabel = match($req->status) {
        'pending'   => '🔔 Pendiente',
        'seen'      => '👁 Visto',
        'completed' => '✅ Completado',
        default     => $req->status,
    };
@endphp
<div style="background:var(--gray-800,#1e293b); border:1px solid var(--gray-700,#334155); border-radius:12px; padding:1.25rem 1.5rem;">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem; margin-bottom:1rem;">
        <div>
            <span style="font-weight:700; font-size:1rem;">
                {{ $req->wholesaler?->business_name ?? '—' }}
            </span>
            <span style="color:var(--gray-400); font-size:0.82rem; margin-left:0.75rem;">
                {{ $req->wholesaler?->city }} · {{ $req->wholesaler?->email }}
            </span>
            <br>
            <span style="font-size:0.78rem; color:var(--gray-500);">
                {{ $req->created_at->format('d/m/Y H:i') }}
            </span>
        </div>
        <span style="background:{{ $statusColor }}22; color:{{ $statusColor }}; border:1px solid {{ $statusColor }}55; border-radius:99px; padding:0.2rem 0.8rem; font-size:0.78rem; font-weight:600;">
            {{ $statusLabel }}
        </span>
    </div>

    {{-- Productos --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:1rem;">
        <thead>
            <tr style="font-size:0.75rem; color:var(--gray-400); text-align:left;">
                <th style="padding:0.3rem 0.5rem;">Producto</th>
                <th style="padding:0.3rem 0.5rem;">Categoría</th>
                <th style="padding:0.3rem 0.5rem; text-align:right;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
        @foreach($req->items as $item)
            <tr style="border-top:1px solid var(--gray-700,#334155); font-size:0.88rem;">
                <td style="padding:0.45rem 0.5rem; font-weight:500;">{{ $item['product_name'] ?? '?' }}</td>
                <td style="padding:0.45rem 0.5rem; color:var(--gray-400);">{{ $item['category'] ?? '' }}</td>
                <td style="padding:0.45rem 0.5rem; text-align:right; font-weight:700;">{{ $item['quantity'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($req->notes)
    <p style="font-size:0.83rem; color:var(--gray-400); margin:0 0 1rem; font-style:italic;">
        💬 {{ $req->notes }}
    </p>
    @endif

    {{-- Acciones --}}
    <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
        @if($req->status === 'pending')
        <button wire:click="markSeen({{ $req->id }})"
                style="padding:0.35rem 1rem; background:#2563eb; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:0.82rem;">
            👁 Marcar visto
        </button>
        @endif
        @if($req->status !== 'completed')
        <button wire:click="markCompleted({{ $req->id }})"
                style="padding:0.35rem 1rem; background:#16a34a; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:0.82rem;">
            ✅ Completado
        </button>
        @endif
        <button wire:click="deletePedido({{ $req->id }})"
                wire:confirm="¿Eliminar este pedido?"
                style="padding:0.35rem 1rem; background:transparent; color:#ef4444; border:1px solid #ef444455; border-radius:8px; cursor:pointer; font-size:0.82rem;">
            Eliminar
        </button>
    </div>

</div>
@endforeach
</div>

@endif

</x-filament-panels::page>
