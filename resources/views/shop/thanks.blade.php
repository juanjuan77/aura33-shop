@extends('layouts.app')

@section('title', 'Pedido Confirmado — AURA33')

@section('content')
<div style="padding: 5rem 0;">
    <div class="container" style="max-width:650px;">

        {{-- Íconos y estado --}}
        @if(session('mp_failure'))
            <div style="font-size:3.5rem; margin-bottom:1rem; text-align:center;">😔</div>
            <p class="section-label" style="text-align:center;">Algo salió mal</p>
            <h1 class="section-title" style="text-align:center;">Pago no procesado</h1>
        @elseif(session('mp_pending'))
            <div style="font-size:3.5rem; margin-bottom:1rem; text-align:center; animation: pulse 2s infinite;">⏳</div>
            <p class="section-label" style="text-align:center;">En proceso</p>
            <h1 class="section-title" style="text-align:center;">Pago pendiente</h1>
        @else
            <div style="font-size:3.5rem; margin-bottom:1rem; text-align:center; animation: pulse 2s infinite;">✨</div>
            <p class="section-label" style="text-align:center;">¡Recibimos tu pedido!</p>
            <h1 class="section-title" style="text-align:center;">Muchas gracias</h1>
        @endif

        <div class="divider"></div>

        {{-- Mensajes de sesión --}}
        @foreach(['success','receipt_ok','mp_error','mp_pending','mp_failure'] as $key)
            @if(session($key))
            <div style="margin: 1.2rem 0; padding: 14px 18px; border-radius: 8px;
                background: {{ in_array($key, ['mp_failure','mp_error']) ? 'rgba(210,80,80,0.07)' : ($key === 'mp_pending' ? 'rgba(201,168,76,0.08)' : 'rgba(90,154,90,0.08)') }};
                border: 1px solid {{ in_array($key, ['mp_failure','mp_error']) ? 'rgba(210,80,80,0.2)' : ($key === 'mp_pending' ? 'rgba(201,168,76,0.25)' : 'rgba(90,154,90,0.2)') }};
                font-size: 0.88rem; color: var(--text); line-height: 1.6; text-align:center;">
                {{ session($key) }}
            </div>
            @endif
        @endforeach

        {{-- Número de pedido --}}
        <div style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:1.5rem 2rem; margin: 1.5rem 0; text-align:center;">
            <div style="font-size:0.72rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Número de Pedido</div>
            <div style="font-family:'Cormorant Garamond',serif; font-size:1.8rem; color:var(--gold); letter-spacing:0.1em;">{{ $order->order_number }}</div>
        </div>

        {{-- Resumen --}}
        <div style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:2rem; margin-bottom:1.5rem; text-align:left;">
            <h3 style="font-family:'Cormorant Garamond',serif; font-size:1.3rem; margin-bottom:1rem; color:var(--text);">Resumen del pedido</h3>

            @foreach($order->items as $item)
            <div style="display:flex; justify-content:space-between; padding:0.5rem 0; border-bottom:1px solid var(--border); font-size:0.88rem;">
                <span style="color:var(--text);">{{ $item->product_name }} <span style="color:var(--muted)">× {{ $item->quantity }}</span></span>
                <span style="color:var(--gold)">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div style="display:flex; justify-content:space-between; padding:0.5rem 0; font-size:0.87rem; color:var(--muted);">
                <span>Envío</span>
                @if($order->shipping_cost == 0)
                    <span style="color:#7ee8a2">Gratis</span>
                @else
                    <span>${{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                @endif
            </div>

            @if($order->discount_amount > 0)
            <div style="display:flex; justify-content:space-between; padding:0.5rem 0; font-size:0.87rem; color:var(--muted);">
                <span>Descuento cupón ({{ $order->coupon_code }})</span>
                <span style="color:#5a9a5a;">-${{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif

            @if($order->mp_surcharge > 0)
            <div style="display:flex; justify-content:space-between; padding:0.5rem 0; font-size:0.87rem; color:var(--muted);">
                <span>Recargo MercadoPago (5%)</span>
                <span style="color:#e08020;">+${{ number_format($order->mp_surcharge, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="divider" style="margin:0.5rem 0;"></div>
            <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:600;">
                <span style="color:var(--text)">Total</span>
                <span style="color:var(--gold)">${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Bloque según método de pago --}}
        @if($order->payment_method === 'wholesale')

            @php
                $waPhone = preg_replace('/[^0-9]/', '', $order->customer_phone ?? '');
                // Si no tiene código de país, asumir Argentina (54)
                if ($waPhone && !str_starts_with($waPhone, '54')) {
                    $waPhone = '54' . ltrim($waPhone, '0');
                }
                $waMsg = urlencode("Hola {$order->customer_name}! Recibimos tu solicitud mayorista #{$order->order_number}. Te escribimos para coordinar disponibilidad, pago y envío. 📦");
                $waUrl = $waPhone ? "https://wa.me/{$waPhone}?text={$waMsg}" : null;
            @endphp

            <div style="background:rgba(74,59,82,0.04); border:1px solid rgba(74,59,82,0.15); border-radius:8px; padding:1.8rem; margin-bottom:1.5rem; text-align:center;">
                <div style="font-size:2rem; margin-bottom:0.8rem;">📦</div>
                <p style="color:var(--text); font-size:0.9rem; line-height:1.7; margin:0 0 1.2rem;">
                    Recibimos tu solicitud mayorista. En breve te contactamos para coordinar disponibilidad, pago y envío.
                </p>
                @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank"
                   style="display:inline-flex; align-items:center; gap:10px; background:#25d366; color:#fff; font-size:0.88rem; font-weight:600; padding:12px 22px; border-radius:8px; text-decoration:none; letter-spacing:0.03em;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Contactar a {{ $order->customer_name }} por WhatsApp
                </a>
                @else
                <p style="font-size:0.82rem; color:var(--muted); margin:0;">
                    El cliente no ingresó número de teléfono. Contactalo por email: <strong>{{ $order->customer_email }}</strong>
                </p>
                @endif
            </div>

        @elseif($order->payment_method === 'mercadopago')

            @if($order->status === 'confirmed')
                <div style="background:rgba(90,154,90,0.07); border:1px solid rgba(90,154,90,0.2); border-radius:8px; padding:1.5rem; margin-bottom:1.5rem; text-align:center;">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">✅</div>
                    <p style="color:var(--text); font-size:0.9rem; margin:0; line-height:1.7;">
                        Tu pago fue <strong>aprobado por MercadoPago</strong>. Ya estamos preparando tu pedido.<br>
                        Te avisamos por email cuando lo enviemos.
                    </p>
                </div>
            @else
                <div style="background:rgba(201,168,76,0.07); border:1px solid rgba(201,168,76,0.2); border-radius:8px; padding:1.5rem; margin-bottom:1.5rem; text-align:center;">
                    <p style="color:var(--muted); font-size:0.88rem; margin:0; line-height:1.7;">
                        Si el pago no se completó, podés intentarlo nuevamente o pagar por transferencia con los datos de abajo.
                    </p>
                </div>

                {{-- Datos bancarios como alternativa --}}
                <div style="background:rgba(123,94,167,0.07); border:1px solid rgba(123,94,167,0.18); border-radius:8px; padding:1.5rem; margin-bottom:1.5rem; text-align:left;">
                    <h3 style="font-size:0.78rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:var(--gold); margin-bottom:1rem;">🏦 Alternativa: Transferencia</h3>
                    @foreach([['Banco', $bankData['banco']], ['Titular', $bankData['titular']], ['CBU', $bankData['cbu']], ['Alias', $bankData['alias']]] as [$label, $val])
                    <div style="display:flex; justify-content:space-between; padding:0.5rem 0; border-bottom:1px solid rgba(123,94,167,0.08); font-size:0.86rem;">
                        <span style="color:var(--muted)">{{ $label }}</span>
                        <strong style="color:var(--text)">{{ $val }}</strong>
                    </div>
                    @endforeach
                </div>
            @endif

        @else

            {{-- Transferencia --}}
            @if($order->transfer_receipt)
                <div style="background:rgba(90,154,90,0.07); border:1px solid rgba(90,154,90,0.2); border-radius:8px; padding:1.5rem; margin-bottom:1.5rem; text-align:center;">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">📎</div>
                    <p style="color:var(--text); font-size:0.88rem; margin:0;">Comprobante recibido. Te confirmamos en 24hs hábiles.</p>
                </div>
            @else
                {{-- Upload comprobante --}}
                <div style="background:rgba(123,94,167,0.07); border:1px solid rgba(123,94,167,0.18); border-radius:8px; padding:1.8rem; margin-bottom:1.5rem;">
                    <h3 style="font-size:0.78rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:var(--gold); margin-bottom:1.2rem;">💳 Realizá la Transferencia</h3>

                    @foreach([['Banco', $bankData['banco']], ['Titular', $bankData['titular']], ['CBU', $bankData['cbu']], ['Alias', $bankData['alias']]] as [$label, $val])
                    <div style="display:flex; justify-content:space-between; padding:0.5rem 0; border-bottom:1px solid rgba(123,94,167,0.08); font-size:0.86rem;">
                        <span style="color:var(--muted)">{{ $label }}</span>
                        <strong style="color:var(--text)">{{ $val }}</strong>
                    </div>
                    @endforeach

                    <div style="margin-top:1.5rem;">
                        <p style="font-size:0.82rem; color:var(--muted); margin-bottom:1rem; line-height:1.6;">
                            Una vez realizada la transferencia, subí el comprobante acá para agilizar la confirmación:
                        </p>

                        <form action="{{ route('order.receipt', $order) }}" method="POST" enctype="multipart/form-data" id="receipt-form">
                            @csrf
                            <label class="upload-zone" id="upload-zone">
                                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf,.webp" id="receipt-input" style="display:none;" onchange="previewFile(this)">
                                <div id="upload-placeholder">
                                    <div style="font-size:2rem; margin-bottom:0.5rem;">📤</div>
                                    <p style="font-size:0.82rem; color:var(--muted); margin:0;">Hacé clic para subir el comprobante</p>
                                    <p style="font-size:0.72rem; color:var(--muted); margin-top:4px; opacity:0.7;">JPG, PNG, PDF o WebP · Máx 5MB</p>
                                </div>
                                <div id="upload-preview" style="display:none; font-size:0.85rem; color:var(--brand);"></div>
                            </label>

                            <button type="submit" class="btn btn-gold" id="receipt-submit"
                                style="width:100%; margin-top:12px; display:none; padding:13px;">
                                Subir Comprobante
                            </button>
                        </form>
                    </div>
                </div>

                <p style="font-size:0.78rem; color:var(--muted); text-align:center; margin-bottom:1.5rem; line-height:1.6;">
                    También podés enviarlo por WhatsApp mencionando el número <strong style="color:var(--gold)">{{ $order->order_number }}</strong>
                </p>

                <a href="https://wa.me/5493415000000?text=Hola!%20Mi%20pedido%20es%20{{ $order->order_number }}%20y%20quiero%20enviar%20el%20comprobante"
                   target="_blank" class="btn btn-ghost" style="width:100%; margin-bottom:1rem; padding:12px; text-align:center;">
                    📲 Enviar por WhatsApp
                </a>
            @endif

        @endif

        <a href="{{ route('shop') }}" class="btn btn-ghost" style="width:100%; padding:12px; text-align:center;">
            Seguir comprando
        </a>

    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.upload-zone {
    display: block;
    border: 2px dashed rgba(123,94,167,0.3);
    border-radius: 8px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
}
.upload-zone:hover { border-color: var(--brand); background: rgba(123,94,167,0.04); }
</style>
@endpush

@push('scripts')
<script>
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('upload-placeholder').style.display = 'none';
    const preview = document.getElementById('upload-preview');
    preview.style.display = 'block';
    preview.innerHTML = '📎 ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    document.getElementById('receipt-submit').style.display = 'block';
}
</script>
@endpush
