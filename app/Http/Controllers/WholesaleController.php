<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\ConsignmentPayment;
use App\Models\ConsignmentReport;
use App\Models\Order;
use App\Models\Product;
use App\Models\WholesaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            ->with('items')
            ->latest()
            ->get();

        $reports = ConsignmentReport::where('wholesale_request_id', $wholesaler->id)
            ->latest()
            ->get();

        $payments = ConsignmentPayment::where('wholesale_request_id', $wholesaler->id)
            ->latest()
            ->get();

        $totalDebt    = $reports->where('status', 'confirmed')->sum('amount');
        $totalPaid    = $payments->sum('amount');
        $pendingBalance = $totalDebt - $totalPaid;

        return view('shop.wholesale.portal', compact(
            'wholesaler', 'orders', 'consignments', 'reports', 'payments',
            'totalDebt', 'totalPaid', 'pendingBalance'
        ));
    }

    private function getWholesaler(): ?WholesaleRequest
    {
        $id = session('wholesale_user');
        if (! $id) return null;
        return WholesaleRequest::find($id);
    }
}
