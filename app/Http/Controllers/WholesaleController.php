<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ConsignmentPayment;
use App\Models\ConsignmentReport;
use App\Models\ConsignmentRestockRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\WholesaleRequest;
use App\Notifications\RestockRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class WholesaleController extends Controller
{
    public function info()
    {
        return view('shop.wholesale.info');
    }

    public function register()
    {
        return view('shop.wholesale.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:wholesale_requests,email',
            'password'      => 'required|string|min:6|confirmed',
            'phone'         => 'required|string|max:50',
            'business_name' => 'required|string|max:255',
            'cuit'          => 'nullable|string|max:30',
            'city'          => 'required|string|max:100',
            'province'      => 'required|string|max:100',
            'business_type' => 'required|string',
            'notes'         => 'nullable|string|max:1000',
        ], [
            'name.required'              => 'El nombre es obligatorio.',
            'email.required'             => 'El email es obligatorio.',
            'email.unique'               => 'Ya existe una solicitud con ese email.',
            'password.required'          => 'Elegí una contraseña.',
            'password.min'               => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'         => 'Las contraseñas no coinciden.',
            'phone.required'             => 'El teléfono es obligatorio.',
            'business_name.required'     => 'El nombre del negocio es obligatorio.',
            'city.required'              => 'La ciudad es obligatoria.',
            'province.required'          => 'La provincia es obligatoria.',
            'business_type.required'     => 'El tipo de negocio es obligatorio.',
        ]);

        $data['password'] = Hash::make($data['password']);

        WholesaleRequest::create($data);

        return redirect()->route('wholesale.thanks')
            ->with('success', '¡Solicitud enviada! Te contactaremos en 48 horas hábiles.');
    }

    public function thanks()
    {
        return view('shop.wholesale.thanks');
    }

    // Ajax: verifica si un email está aprobado (usado en checkout)
    public function verify(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $approved = WholesaleRequest::isEmailApproved($request->email);
        return response()->json(['approved' => $approved]);
    }

    // ── Portal mayorista ────────────────────────────────────

    public function loginForm()
    {
        if (session('wholesale_user')) {
            return redirect()->route('wholesale.portal');
        }
        return view('shop.wholesale.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Ingresá tu email.',
            'password.required' => 'Ingresá tu contraseña.',
        ]);

        $wholesaler = WholesaleRequest::where('email', $request->email)->first();

        if (! $wholesaler || ! Hash::check($request->password, $wholesaler->password)) {
            return back()->withErrors(['email' => 'Email o contraseña incorrectos.'])->withInput();
        }

        if ($wholesaler->status === 'pending') {
            return back()->withErrors(['email' => 'Tu solicitud aún está siendo revisada. Te avisamos cuando esté aprobada.'])->withInput();
        }

        if ($wholesaler->status === 'rejected') {
            return back()->withErrors(['email' => 'Tu solicitud fue rechazada. Contactanos por WhatsApp para más información.'])->withInput();
        }

        session(['wholesale_user' => $wholesaler->id]);

        return redirect()->route('wholesale.portal')->with('success', "¡Bienvenida, {$wholesaler->name}! 💎");
    }

    public function logout()
    {
        session()->forget('wholesale_user');
        return redirect()->route('wholesale.login')->with('success', 'Sesión cerrada.');
    }

    public function portal()
    {
        $wholesaler = $this->getWholesaler();
        if (! $wholesaler) return redirect()->route('wholesale.login');

        $orders = Order::where('customer_email', $wholesaler->email)
            ->where('customer_type', 'wholesale')
            ->latest()
            ->get();

        $consignments = Consignment::where('wholesale_request_id', $wholesaler->id)
            ->with(['items.product.category', 'payments'])
            ->latest()
            ->get();

        $reports = ConsignmentReport::where('wholesale_request_id', $wholesaler->id)
            ->latest()
            ->get();

        $payments = ConsignmentPayment::where('wholesale_request_id', $wholesaler->id)
            ->latest()
            ->get();

        $totalDebt      = $consignments->sum(fn($c) => $c->totalDelivered());
        $totalPaid      = $payments->sum('amount');
        $pendingBalance = $totalDebt - $totalPaid;

        // Build per-product report usando TODOS los pagos del mayorista
        $reportMap = [];
        $allItems  = $consignments->flatMap(fn($c) => $c->items);

        foreach ($allItems as $item) {
            $pid = $item->product_id ?? ('manual_' . $item->id);
            if (! isset($reportMap[$pid])) {
                $reportMap[$pid] = [
                    'product_name' => $item->product?->name ?? $item->product_name ?? '?',
                    'category'     => $item->product?->category?->name ?? 'Sin categoría',
                    'unit_price'   => $item->unit_price,
                    'delivered'    => 0, 'sold' => 0, 'paid_qty' => 0,
                    '_item_ids'    => [],
                ];
            }
            $reportMap[$pid]['delivered']  += $item->quantity;
            $reportMap[$pid]['_item_ids'][] = $item->id;
        }

        foreach ($payments as $pay) {
            $soldItems = is_string($pay->items_sold) ? json_decode($pay->items_sold, true) : $pay->items_sold;
            if (! is_array($soldItems)) continue;
            foreach ($soldItems as $s) {
                $sid = (int)($s['consignment_item_id'] ?? 0);
                foreach ($reportMap as $pid => &$row) {
                    if (in_array($sid, $row['_item_ids'])) {
                        $row['sold']     += (int)($s['qty_sold'] ?? 0);
                        $row['paid_qty'] += (int)($s['qty_paid'] ?? 0);
                        break;
                    }
                }
                unset($row);
            }
        }
        $allReportByProduct = collect($reportMap)->map(function ($r) {
            $r['stock']       = max(0, $r['delivered'] - $r['sold']);
            $r['debe']        = max(0, $r['sold'] - $r['paid_qty']);
            $r['debe_amount'] = $r['debe'] * $r['unit_price'];
            return $r;
        })->sortBy('category')->values();

        $availableCategories = $allReportByProduct->pluck('category')->unique()->sort()->values();
        $defaultCat          = $availableCategories->first(fn($c) => str_contains(strtolower($c), 'botella')) ?? $availableCategories->first();
        $selectedCategory    = request('cat', $defaultCat);
        $reportByProduct     = $selectedCategory
            ? $allReportByProduct->filter(fn($r) => $r['category'] === $selectedCategory)->values()
            : $allReportByProduct;

        $allPayments   = $payments;
        $globalItemMap = $consignments->flatMap(fn($c) => $c->items)->keyBy('id');

        return view('shop.wholesale.portal', compact(
            'wholesaler', 'orders', 'consignments', 'reports', 'payments',
            'totalDebt', 'totalPaid', 'pendingBalance', 'reportByProduct',
            'allPayments', 'globalItemMap', 'availableCategories', 'selectedCategory'
        ));
    }

    public function restockForm()
    {
        $wholesaler = $this->getWholesaler();
        if (! $wholesaler) return redirect()->route('wholesale.login');

        $products = Product::with('category')
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => $p->category?->name ?? 'Sin categoría');

        return view('shop.wholesale.restock', compact('wholesaler', 'products'));
    }

    public function restockStore(Request $request)
    {
        $wholesaler = $this->getWholesaler();
        if (! $wholesaler) return redirect()->route('wholesale.login');

        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'notes'            => 'nullable|string|max:500',
        ], [
            'items.required'           => 'Agregá al menos un producto.',
            'items.*.product_id.required' => 'Seleccioná el producto.',
            'items.*.quantity.min'     => 'La cantidad mínima es 1.',
        ]);

        $items = collect($request->items)->map(function ($row) {
            $p = Product::with('category')->find($row['product_id']);
            return [
                'product_id'   => $p->id,
                'product_name' => $p->name,
                'category'     => $p->category?->name ?? 'Sin categoría',
                'quantity'     => (int) $row['quantity'],
            ];
        })->toArray();

        $restock = ConsignmentRestockRequest::create([
            'wholesale_request_id' => $wholesaler->id,
            'items'                => $items,
            'status'               => 'pending',
            'notes'                => $request->notes,
        ]);

        try {
            $adminEmail = config('mail.from.address', 'ventas@aura33.com.ar');
            Notification::route('mail', $adminEmail)
                ->notify(new RestockRequestNotification($restock, $wholesaler));
        } catch (\Exception $e) {
            // No interrumpir si falla el mail
        }

        return redirect()->route('wholesale.portal')
            ->with('success', '¡Pedido de reposición enviado! Te contactamos pronto. 📦');
    }

    public function changePassword(Request $request)
    {
        $wholesaler = $this->getWholesaler();
        if (! $wholesaler) return redirect()->route('wholesale.login');

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Ingresá tu contraseña actual.',
            'password.required'         => 'Ingresá la nueva contraseña.',
            'password.min'              => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        if (! Hash::check($request->current_password, $wholesaler->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])->with('show_password_form', true);
        }

        $wholesaler->update(['password' => Hash::make($request->password)]);

        return redirect()->route('wholesale.portal')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    private function getWholesaler(): ?WholesaleRequest
    {
        $id = session('wholesale_user');
        if (! $id) return null;
        return WholesaleRequest::find($id);
    }
}
