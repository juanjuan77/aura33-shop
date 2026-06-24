<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\WholesaleRequest;
use App\Notifications\AdminNotifiable;
use App\Notifications\NewOrderNotification;
use App\Notifications\ReceiptUploadedNotification;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    public function index()
    {
        $cart   = $this->getCart();
        $items  = $this->resolveCartItems($cart);
        $type   = session('wholesale_user') ? 'wholesale' : 'retail';
        $totals = $this->calculateTotals($items, $cart, $type);
        return view('shop.cart', compact('items', 'totals'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'sometimes|integer|min:1|max:99']);
        $qty  = $request->quantity ?? 1;
        $cart = $this->getCart();
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + $qty, $product->stock);
        $this->saveCart($cart);
        return redirect()->back()->with('success', "\"{$product->name}\" agregado al carrito ✨");
    }

    public function update(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);
        $cart = $this->getCart();
        if ($request->quantity == 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($request->quantity, $product->stock);
        }
        $this->saveCart($cart);
        return redirect()->route('cart')->with('success', 'Carrito actualizado.');
    }

    public function remove(Product $product)
    {
        $cart = $this->getCart();
        unset($cart[$product->id]);
        $this->saveCart($cart);
        return redirect()->route('cart')->with('success', 'Producto eliminado.');
    }

    public function checkout()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Tu carrito está vacío.');
        }
        $wholesaler = session('wholesale_user') ? WholesaleRequest::find(session('wholesale_user')) : null;
        $type       = $wholesaler ? 'wholesale' : 'retail';
        $items      = $this->resolveCartItems($cart);
        $totals     = $this->calculateTotals($items, $cart, $type);
        $bankData   = $this->bankData();
        return view('shop.checkout', compact('items', 'totals', 'bankData', 'wholesaler', 'type'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_email'    => 'required|email',
            'customer_phone'    => 'nullable|string|max:50',
            'shipping_address'     => 'required|string|max:255',
            'shipping_city'        => 'required|string|max:100',
            'shipping_province'    => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_notes'       => 'nullable|string|max:500',
            'payment_method'    => 'required|in:transfer,mercadopago,wholesale',
            'coupon_code'       => 'nullable|string|max:20',
            'notes'             => 'nullable|string|max:500',
        ]);

        $cart = $this->getCart();
        if (empty($cart)) return redirect()->route('shop')->with('error', 'Tu carrito está vacío.');

        $wholesaler = session('wholesale_user') ? WholesaleRequest::find(session('wholesale_user')) : null;
        $type       = $wholesaler ? 'wholesale' : 'retail';
        $items      = $this->resolveCartItems($cart);
        $totals     = $this->calculateTotals($items, $cart, $type, $request->shipping_province);

        // Cupón de descuento
        $coupon         = null;
        $discountAmount = 0;
        if ($request->coupon_code) {
            $coupon = Coupon::findValid($request->coupon_code, $request->customer_email);
            if ($coupon) {
                $discountAmount = $coupon->applyTo($totals['subtotal']);
            }
        }

        $surcharge = 0;
        $total     = $totals['total'] - $discountAmount;
        if ($request->payment_method === 'mercadopago') {
            $surcharge = round(($totals['subtotal'] - $discountAmount) * 0.05, 2);
            $total     = $total + $surcharge;
        }

        // Mayorista: no paga online, estado pendiente de contacto
        $isWholesale = $request->payment_method === 'wholesale';

        $order = Order::create([
            'customer_name'     => $request->customer_name,
            'customer_email'    => $request->customer_email,
            'customer_phone'    => $request->customer_phone,
            'customer_type'     => $type,
            'shipping_address'     => $request->shipping_address,
            'shipping_city'        => $request->shipping_city,
            'shipping_province'    => $request->shipping_province,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_notes'       => $request->shipping_notes,
            'subtotal'          => $totals['subtotal'],
            'shipping_cost'     => $totals['shipping'],
            'total'             => $total,
            'mp_surcharge'      => $surcharge,
            'coupon_code'       => $coupon?->code,
            'discount_amount'   => $discountAmount,
            'status'            => $isWholesale ? 'pending' : ($request->payment_method === 'transfer' ? 'transfer_pending' : 'mp_pending'),
            'payment_method'    => $request->payment_method,
            'notes'             => $request->notes,
        ]);

        foreach ($items as $item) {
            $qty       = $cart[$item->id];
            $unitPrice = $type === 'wholesale' ? $item->price_wholesale : $item->price_retail;
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->id,
                'product_name' => $item->name,
                'quantity'     => $qty,
                'unit_price'   => $unitPrice,
                'subtotal'     => $unitPrice * $qty,
            ]);
            $item->decrement('stock', $qty);
        }

        session()->forget('cart');
        if ($coupon) $coupon->recordUse($request->customer_email);

        // Notificar al admin
        $this->notifyAdmin(new NewOrderNotification($order->load('items')));

        if ($isWholesale) {
            return redirect()->route('order.thanks', $order)->with('success', '¡Solicitud mayorista recibida! Te contactamos a la brevedad. 📦');
        }

        if ($request->payment_method === 'mercadopago') {
            try {
                $mp   = new MercadoPagoService();
                $pref = $mp->createPreference($order->load('items'));
                $order->update(['mp_preference_id' => $pref['id']]);
                return redirect($pref['init_point']);
            } catch (\Exception $e) {
                // Si MP falla, dejamos el pedido creado y mostramos gracias con error
                return redirect()->route('order.thanks', $order)
                    ->with('mp_error', 'No pudimos conectar con MercadoPago. Podés pagar por transferencia usando los datos de abajo.');
            }
        }

        return redirect()->route('order.thanks', $order)->with('success', '¡Pedido realizado! 🌟');
    }

    public function uploadReceipt(Request $request, Order $order)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        $path = $request->file('receipt')->store('receipts', 'public');
        $order->update([
            'transfer_receipt' => $path,
            'status'           => 'receipt_received',
        ]);

        $this->notifyAdmin(new ReceiptUploadedNotification($order));

        return redirect()->route('order.thanks', $order)->with('receipt_ok', '¡Comprobante recibido! Te confirmamos en 24hs hábiles.');
    }

    // MercadoPago callbacks
    public function mpSuccess(Request $request)
    {
        $order = Order::where('order_number', $request->external_reference)->first();
        if ($order) {
            $order->update([
                'mp_payment_id' => $request->payment_id,
                'status'        => 'confirmed',
            ]);
        }
        return redirect()->route('order.thanks', $order)->with('success', '¡Pago confirmado! 🎉');
    }

    public function mpPending(Request $request)
    {
        $order = Order::where('order_number', $request->external_reference)->first();
        return redirect()->route('order.thanks', $order)->with('mp_pending', 'Tu pago está siendo procesado. Te avisamos por email cuando se confirme.');
    }

    public function mpFailure(Request $request)
    {
        $order = Order::where('order_number', $request->external_reference)->first();
        if ($order) {
            $order->update(['status' => 'cancelled']);
        }
        return redirect()->route('order.thanks', $order)->with('mp_failure', 'El pago no se pudo procesar. Podés intentarlo de nuevo o pagar por transferencia.');
    }

    public function mpWebhook(Request $request)
    {
        if ($request->type === 'payment' && $request->input('data.id')) {
            try {
                $mp      = new MercadoPagoService();
                $payment = $mp->getPayment($request->input('data.id'));
                $order   = Order::where('order_number', $payment['external_reference'] ?? '')->first();
                if ($order) {
                    $status = match($payment['status'] ?? '') {
                        'approved' => 'confirmed',
                        'pending', 'in_process' => 'mp_pending',
                        'rejected', 'cancelled' => 'cancelled',
                        default => $order->status,
                    };
                    $order->update(['status' => $status, 'mp_payment_id' => $payment['id']]);
                }
            } catch (\Exception) {}
        }
        return response()->json(['ok' => true]);
    }

    public function thanks(Order $order)
    {
        $bankData = $this->bankData();
        return view('shop.thanks', compact('order', 'bankData'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string', 'email' => 'nullable|email']);

        $email  = $request->email ?? '';
        $coupon = Coupon::findValid($request->code, $email);

        if (! $coupon) {
            // Distinguir si el cupón existe pero ya fue usado por este email
            $exists = Coupon::where('code', strtoupper(trim($request->code)))->where('active', true)->first();
            if ($exists && $email && $exists->alreadyUsedBy($email)) {
                return response()->json(['valid' => false, 'message' => 'Este cupón ya fue usado con ese email.']);
            }
            return response()->json(['valid' => false, 'message' => 'Cupón inválido o vencido.']);
        }

        return response()->json([
            'valid'   => true,
            'code'    => $coupon->code,
            'percent' => (float) $coupon->discount_percent,
            'message' => "¡Cupón aplicado! {$coupon->discount_percent}% de descuento.",
        ]);
    }

    public function calculateShipping(Request $request)
    {
        $request->validate(['province' => 'required|string', 'subtotal' => 'required|numeric']);

        $zone = ShippingZone::forProvince($request->province);
        if (! $zone) {
            return response()->json(['shipping' => 9000, 'free' => false, 'zone' => 'Todo el país']);
        }

        $free = $request->subtotal >= $zone->free_from;
        return response()->json([
            'shipping'  => $free ? 0 : (float) $zone->price,
            'free'      => $free,
            'zone'      => $zone->name,
            'free_from' => number_format($zone->free_from, 0, ',', '.'),
        ]);
    }

    private function resolveCartItems(array $cart)
    {
        if (empty($cart)) return collect();
        return Product::whereIn('id', array_keys($cart))->get();
    }

    private function calculateTotals($items, array $cart, string $type = 'retail', string $province = ''): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $price     = $type === 'wholesale' ? $item->price_wholesale : $item->price_retail;
            $subtotal += $price * ($cart[$item->id] ?? 0);
        }

        $zone     = $province ? ShippingZone::forProvince($province) : null;
        $freeFrom = $zone ? $zone->free_from : 50000;
        $baseRate = $zone ? $zone->price : 5500;
        $shipping = $subtotal >= $freeFrom ? 0 : $baseRate;

        return [
            'subtotal'      => $subtotal,
            'shipping'      => $shipping,
            'total'         => $subtotal + $shipping,
            'free_shipping' => $subtotal >= $freeFrom,
            'free_from'     => $freeFrom,
            'zone'          => $zone?->name ?? '',
        ];
    }

    private function notifyAdmin($notification): void
    {
        $email = config('app.admin_email');
        if ($email) {
            try {
                Notification::send(new AdminNotifiable($email), $notification);
            } catch (\Exception) {
                // No romper el flujo si el mail falla
            }
        }
    }

    private function bankData(): array
    {
        return [
            'banco'   => 'Banco Galicia',
            'titular' => 'AURA33',
            'cbu'     => '0070999520000004567890',
            'alias'   => 'AURA33.CRISTALES',
        ];
    }
}
