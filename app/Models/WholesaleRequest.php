<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Models\Consignment;

class WholesaleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'business_name', 'cuit',
        'city', 'province', 'business_type', 'notes',
        'status', 'admin_notes', 'reviewed_at',
    ];

    protected $hidden = ['password'];

    protected $casts = ['reviewed_at' => 'datetime'];

    const STATUSES = [
        'pending'  => 'Pendiente de revisión',
        'approved' => 'Aprobado',
        'rejected' => 'Rechazado',
    ];

    const BUSINESS_TYPES = [
        'tienda_fisica'  => 'Tienda física',
        'tienda_online'  => 'Tienda online',
        'terapeuta'      => 'Terapeuta holístico/a',
        'revendedor'     => 'Revendedor/a independiente',
        'otro'           => 'Otro',
    ];

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    // Verifica si un email ya está aprobado como mayorista
    public static function isEmailApproved(string $email): bool
    {
        return self::where('email', $email)->where('status', 'approved')->exists();
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_email', 'email')
            ->where('customer_type', 'wholesale')
            ->latest();
    }

    public function consignments()
    {
        return $this->hasMany(Consignment::class, 'wholesale_request_id');
    }
}
