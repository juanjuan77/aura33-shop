<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_name', 'customer_email', 'customer_phone',
        'customer_type', 'shipping_address', 'shipping_city', 'shipping_province',
        'shipping_postal_code', 'shipping_notes', 'subtotal', 'shipping_cost', 'total', 'status',
        'payment_method', 'transfer_receipt', 'mp_preference_id', 'mp_payment_id',
        'mp_surcharge', 'coupon_code', 'discount_amount', 'notes', 'confirmed_at',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total'         => 'decimal:2',
        'mp_surcharge'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'confirmed_at'  => 'datetime',
    ];

    const STATUSES = [
        'pending'           => 'Pendiente',
        'transfer_pending'  => 'Esperando comprobante',
        'receipt_received'  => 'Comprobante recibido',
        'mp_pending'        => 'Pago MP en proceso',
        'confirmed'         => 'Confirmado',
        'shipped'           => 'Enviado',
        'delivered'         => 'Entregado',
        'cancelled'         => 'Cancelado',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (! $order->order_number) {
                $order->order_number = 'A33-' . strtoupper(uniqid());
            }
        });
    }
}
