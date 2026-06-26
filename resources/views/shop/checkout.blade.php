@extends('layouts.app')
@section('title', 'Checkout — AURA33')

@section('content')
<div style="padding: 70px 0 100px;">
    <div class="container">

        <div class="section-header">
            <span class="section-subtitle">✦ Último paso ✦</span>
            <h1 class="section-title">Finalizar Pedido</h1>
            <div class="divider"></div>
        </div>

        @if($errors->any())
        <div style="background:rgba(210,80,80,0.08); border:1px solid rgba(210,80,80,0.18); border-radius:10px; padding:16px 20px; margin-bottom:28px; max-width:860px; margin-left:auto; margin-right:auto;">
            <ul style="list-style:none; color:#a03030; font-size:0.88rem; display:flex; flex-direction:column; gap:4px;">
                @foreach($errors->all() as $e)
                    <li>⚠ {{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
            @csrf
            <div class="checkout-layout">

                <div class="checkout-left">

                    {{-- Datos personales --}}
                    <div class="form-section">
                        <h3 class="form-section-title">Tus Datos</h3>
                        <div class="form-grid">
                            <div class="form-group form-full">
                                <label>Nombre y Apellido *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $wholesaler?->name ?? '') }}" class="form-input" placeholder="María González" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email', $wholesaler?->email ?? '') }}" class="form-input" placeholder="maria@email.com" required>
                            </div>
                            <div class="form-group">
                                <label>Teléfono / WhatsApp</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone', $wholesaler?->phone ?? '') }}" class="form-input" placeholder="+54 341 000 0000">
                            </div>
                        </div>
                    </div>

                    {{-- Envío --}}
                    <div class="form-section">
                        <h3 class="form-section-title">Datos de Envío</h3>
                        <div class="form-grid">
                            <div class="form-group form-full">
                                <label>Dirección *</label>
                                <input type="text" name="shipping_address" value="{{ old('shipping_address') }}" class="form-input" placeholder="Av. San Martín 1234, Piso 3" required>
                            </div>
                            <div class="form-group">
                                <label>Ciudad *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" class="form-input" placeholder="Funes" required>
                            </div>
                            <div class="form-group">
                                <label>Código Postal *</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="form-input" placeholder="2132" required maxlength="10">
                            </div>
                            <div class="form-group">
                                <label>Provincia *</label>
                                <select name="shipping_province" id="shipping_province" class="form-input" required onchange="recalcShipping()">
                                    <option value="" disabled selected>Seleccioná tu provincia</option>
                                    @foreach(\App\Models\ShippingZone::allProvinces() as $prov)
                                        <option value="{{ $prov }}" {{ old('shipping_province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group form-full">
                                <label>Notas de envío</label>
                                <textarea name="shipping_notes" class="form-input" rows="2" placeholder="Ej: Llamar antes, dejar en portería...">{{ old('shipping_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Método de pago --}}
                    @if($wholesaler)
                    {{-- Mayorista: sin pago online --}}
                    <input type="hidden" name="payment_method" value="wholesale">
                    <div class="form-section" style="background:rgba(74,59,82,0.03); border-color:rgba(74,59,82,0.15);">
                        <h3 class="form-section-title">Pago y Envío</h3>
                        <div style="display:flex; align-items:flex-start; gap:14px;">
                            <span style="font-size:2rem; flex-shrink:0;">📦</span>
                            <div>
                                <p style="font-size:0.9rem; color:var(--text); line-height:1.7; margin:0 0 6px;">
                                    <strong>Pedido mayorista</strong> — una vez recibida tu solicitud te contactamos para coordinar disponibilidad, pago y envío.
                                </p>
                                <p style="font-size:0.78rem; color:var(--muted); margin:0;">No se realiza ningún cobro en este paso.</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="form-section">
                        <h3 class="form-section-title">Método de Pago</h3>
                        <div class="payment-selector">
                            <label class="payment-option" id="pay-transfer-label">
                                <input type="radio" name="payment_method" value="transfer" checked onchange="updatePayment()">
                                <div class="payment-card">
                                    <span style="font-size:1.5rem; display:block; margin-bottom:6px;">🏦</span>
                                    <strong>Transferencia bancaria</strong>
                                    <p>Subís el comprobante después</p>
                                </div>
                            </label>
                            <label class="payment-option" id="pay-mp-label">
                                <input type="radio" name="payment_method" value="mercadopago" onchange="updatePayment()">
                                <div class="payment-card">
                                    <span style="font-size:1.5rem; display:block; margin-bottom:6px;">💳</span>
                                    <strong>MercadoPago</strong>
                                    <p>Tarjeta, débito o saldo MP</p>
                                    <span class="mp-badge">+ 5% recargo</span>
                                </div>
                            </label>
                        </div>

                        {{-- Info transferencia --}}
                        <div id="transfer-info" style="margin-top:16px; background:rgba(74,59,82,0.03); border:1px solid rgba(74,59,82,0.10); border-radius:8px; padding:16px 18px;">
                            @foreach([['Banco', $bankData['banco']], ['Titular', $bankData['titular']], ['CBU', $bankData['cbu']], ['Alias', $bankData['alias']]] as [$l, $v])
                            <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid rgba(74,59,82,0.06); font-size:0.84rem;">
                                <span style="color:var(--muted);">{{ $l }}</span>
                                <strong style="color:var(--brand);">{{ $v }}</strong>
                            </div>
                            @endforeach
                            <p style="font-size:0.76rem; color:var(--muted); margin-top:10px; line-height:1.6; font-weight:300;">
                                Realizá la transferencia y subí el comprobante en la página de confirmación.
                            </p>
                        </div>

                        {{-- Cupón de descuento --}}
                    <div style="margin-top:16px;">
                        <label style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:var(--muted); display:block; margin-bottom:6px;">
                            ¿Tenés un cupón de descuento?
                        </label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" id="coupon-input" placeholder="Ej: VERANO25"
                                class="form-input" style="flex:1; text-transform:uppercase;" maxlength="20">
                            <button type="button" id="coupon-btn" class="btn" style="padding:10px 18px; white-space:nowrap;">
                                Aplicar
                            </button>
                        </div>
                        <div id="coupon-status" style="margin-top:8px; font-size:0.82rem; display:none;"></div>
                        <input type="hidden" name="coupon_code" id="coupon-code-hidden">
                    </div>

                    {{-- Info MP --}}
                        <div id="mp-info" style="display:none; margin-top:16px; background:rgba(0,158,227,0.04); border:1px solid rgba(0,158,227,0.15); border-radius:8px; padding:14px 18px;">
                            <p style="font-size:0.83rem; color:var(--muted); line-height:1.6; margin:0;">
                                Al confirmar serás redirigido a MercadoPago para completar el pago. Se aplica un recargo del <strong style="color:var(--brand);">5%</strong> para cubrir las comisiones de la plataforma.
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="form-section">
                        <h3 class="form-section-title">Notas del Pedido <span style="font-weight:300; font-size:0.85rem; color:var(--muted);">(opcional)</span></h3>
                        <textarea name="notes" class="form-input" rows="3" placeholder="¿Querés dedicatoria? ¿Algo especial para incluir?">{{ old('notes') }}</textarea>
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="checkout-sidebar">

                    <div class="checkout-box" style="margin-bottom:16px;">
                        <h3 style="font-family:var(--font-serif); font-size:1.3rem; color:var(--brand); margin-bottom:16px; font-weight:400;">Tu Pedido</h3>

                        @foreach($items as $item)
                        @php $qty = session('cart')[$item->id] ?? 0; $unitPrice = ($type ?? 'retail') === 'wholesale' ? $item->price_wholesale : $item->price_retail; @endphp
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(74,59,82,0.06); font-size:0.87rem;">
                            <span style="color:var(--text);">{{ $item->name }} <span style="color:var(--muted)">×{{ $qty }}</span></span>
                            <span style="color:var(--brand); font-weight:500;">
                                ${{ number_format($unitPrice * $qty, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach

                        <div style="display:flex; justify-content:space-between; padding:8px 0; font-size:0.87rem; color:var(--muted);">
                            <div>
                                <span>Envío</span>
                                <span id="shipping-zone-label" style="display:block; font-size:0.72rem; opacity:0.7;"></span>
                            </div>
                            <span id="shipping-display">
                                @if($totals['free_shipping'])
                                    <span style="color:#5a9a5a; font-weight:500;">Gratis ✓</span>
                                @else
                                    <span style="font-size:0.8rem; color:var(--muted);">Seleccioná provincia</span>
                                @endif
                            </span>
                        </div>

                        {{-- Descuento cupón --}}
                        <div id="coupon-discount-row" style="display:none; justify-content:space-between; padding:6px 0; font-size:0.84rem; color:var(--muted); border-bottom:1px solid rgba(74,59,82,0.06);">
                            <span>Descuento cupón (<span id="coupon-pct"></span>%)</span>
                            <span id="coupon-discount-amount" style="color:#5a9a5a;"></span>
                        </div>

                        {{-- Recargo MP --}}
                        <div id="mp-surcharge-row" style="display:none; justify-content:space-between; padding:6px 0; font-size:0.84rem; color:var(--muted); border-bottom:1px solid rgba(74,59,82,0.06);">
                            <span>Recargo MercadoPago (5%)</span>
                            <span id="mp-surcharge-amount" style="color:#e08020;"></span>
                        </div>

                        <div style="border-top:1px solid rgba(74,59,82,0.08); padding-top:12px; margin-top:4px; display:flex; justify-content:space-between; font-size:1.1rem; font-weight:600;">
                            <span style="color:var(--brand);">Total</span>
                            <span style="color:var(--brand);" id="total-display">
                                @if($totals['free_shipping'])
                                    ${{ number_format($totals['subtotal'], 0, ',', '.') }}
                                @else
                                    <span style="font-size:0.82rem; font-weight:400; color:var(--muted);">Seleccioná provincia</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn" style="width:100%; padding:16px; font-size:0.9rem;" id="submit-btn">
                        {{ $wholesaler ? 'Enviar Solicitud Mayorista' : 'Confirmar Pedido' }}
                    </button>

                    <a href="{{ route('cart') }}" style="display:block; text-align:center; color:var(--muted); font-size:0.82rem; margin-top:14px;">
                        ← Volver al carrito
                    </a>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection

@push('styles')
<style>
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 36px;
    align-items: start;
}

@media (max-width: 900px) { .checkout-layout { grid-template-columns: 1fr; } }

.checkout-left { display: flex; flex-direction: column; gap: 18px; }

.form-section {
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 12px;
    padding: 26px;
    box-shadow: var(--shadow-soft);
}

.form-section-title {
    font-family: var(--font-serif);
    font-size: 1.2rem;
    color: var(--brand);
    margin-bottom: 18px;
    font-weight: 400;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }

.form-full { grid-column: 1 / -1; }

.form-group label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin-bottom: 6px;
}

.form-input {
    width: 100%;
    background: var(--bg);
    border: 1px solid rgba(74,59,82,0.15);
    border-radius: 6px;
    padding: 11px 14px;
    color: var(--text);
    font-family: var(--font-sans);
    font-size: 0.9rem;
    font-weight: 300;
    outline: none;
    transition: border-color 0.2s;
    resize: vertical;
}

.form-input:focus { border-color: var(--brand); background: var(--white); }
.form-input::placeholder { color: rgba(110,100,115,0.5); }

.payment-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.payment-option input { display: none; }

.payment-card {
    border: 1px solid rgba(74,59,82,0.15);
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.payment-card strong { display: block; font-size: 0.9rem; color: var(--brand); margin-bottom: 3px; }
.payment-card p { font-size: 0.76rem; color: var(--muted); font-weight: 300; margin: 0; }

.payment-option input:checked + .payment-card {
    border-color: var(--brand);
    background: rgba(74,59,82,0.04);
}

.mp-badge {
    display: inline-block;
    margin-top: 6px;
    background: rgba(224,128,32,0.12);
    color: #c06010;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    letter-spacing: 0.05em;
}

.checkout-sidebar { position: sticky; top: 90px; }

.checkout-box {
    background: var(--white);
    border: 1px solid rgba(74,59,82,0.08);
    border-radius: 12px;
    padding: 22px;
    box-shadow: var(--shadow-soft);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subtotalBase  = {{ $totals['subtotal'] }};
    const freeShipping  = {{ $totals['free_shipping'] ? 'true' : 'false' }};

    let currentShipping  = freeShipping ? 0 : null;
    let couponPercent    = 0;
    let appliedCoupon    = '';

    function getPaymentMethod() {
        return document.querySelector('input[name="payment_method"]:checked').value;
    }

    function updateTotalDisplay() {
        if (currentShipping === null) {
            document.getElementById('total-display').innerHTML =
                '<span style="font-size:0.82rem; font-weight:400; color:var(--muted);">Seleccioná provincia</span>';
            document.getElementById('mp-surcharge-row').style.display = 'none';
            document.getElementById('coupon-discount-row').style.display = 'none';
            return;
        }

        const base         = subtotalBase + currentShipping;
        const discount     = couponPercent > 0 ? Math.round(subtotalBase * couponPercent / 100) : 0;
        const afterDiscount = base - discount;
        const isMp         = getPaymentMethod() === 'mercadopago';
        const surcharge    = isMp ? Math.round((subtotalBase - discount) * 0.05) : 0;
        const total        = afterDiscount + surcharge;

        // Fila descuento
        const discountRow = document.getElementById('coupon-discount-row');
        if (discount > 0) {
            discountRow.style.display = 'flex';
            document.getElementById('coupon-pct').textContent = couponPercent;
            document.getElementById('coupon-discount-amount').textContent = '-$' + discount.toLocaleString('es-AR');
        } else {
            discountRow.style.display = 'none';
        }

        // Fila recargo MP
        const surchargeRow = document.getElementById('mp-surcharge-row');
        if (isMp && surcharge > 0) {
            surchargeRow.style.display = 'flex';
            document.getElementById('mp-surcharge-amount').textContent = '+$' + surcharge.toLocaleString('es-AR');
        } else {
            surchargeRow.style.display = 'none';
        }

        document.getElementById('total-display').textContent = '$' + total.toLocaleString('es-AR');
    }

    // Cupón
    document.getElementById('coupon-btn').addEventListener('click', function () {
        const code = document.getElementById('coupon-input').value.trim().toUpperCase();
        if (!code) return;

        const btn    = this;
        const status = document.getElementById('coupon-status');
        btn.textContent = 'Verificando...';
        btn.disabled = true;

        const email = document.querySelector('input[name="customer_email"]').value.trim();

        fetch('{{ route('coupon.apply') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ code, email })
        })
        .then(r => r.json())
        .then(data => {
            status.style.display = 'block';
            if (data.valid) {
                status.style.color = '#5a9a5a';
                status.textContent = data.message;
                couponPercent = data.percent;
                appliedCoupon = data.code;
                document.getElementById('coupon-code-hidden').value = data.code;
                document.getElementById('coupon-input').disabled = true;
                btn.textContent = '✓ Aplicado';
                btn.disabled = true;
                updateTotalDisplay();
            } else {
                status.style.color = '#a03030';
                status.textContent = data.message;
                btn.textContent = 'Aplicar';
                btn.disabled = false;
            }
        })
        .catch(() => {
            btn.textContent = 'Aplicar';
            btn.disabled = false;
        });
    });

    window.updatePayment = function() {
        const isMp = getPaymentMethod() === 'mercadopago';
        document.getElementById('transfer-info').style.display = isMp ? 'none' : 'block';
        document.getElementById('mp-info').style.display       = isMp ? 'block' : 'none';
        document.getElementById('submit-btn').textContent      = isMp
            ? 'Ir a pagar con MercadoPago →'
            : 'Confirmar Pedido';
        updateTotalDisplay();
    };

    window.recalcShipping = function() {
        const prov = document.getElementById('shipping_province').value;
        if (!prov) return;

        fetch('{{ route('shipping.calculate') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ province: prov, subtotal: subtotalBase })
        })
        .then(r => r.json())
        .then(data => {
            const shippingEl = document.getElementById('shipping-display');
            const zoneEl     = document.getElementById('shipping-zone-label');

            currentShipping = data.free ? 0 : data.shipping;

            if (data.free) {
                shippingEl.innerHTML = '<span style="color:#5a9a5a;font-weight:500;">Gratis ✓</span>';
            } else {
                shippingEl.textContent = '$' + data.shipping.toLocaleString('es-AR');
            }
            zoneEl.textContent = data.zone;
            updateTotalDisplay();
        });
    };

    // Si hay provincia guardada (old input), recalcular
    const savedProv = document.getElementById('shipping_province').value;
    if (savedProv) recalcShipping();
});
</script>
@endpush
