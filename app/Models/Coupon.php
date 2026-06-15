<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'discount_percent', 'valid_from', 'valid_until',
        'max_uses', 'uses_count', 'active',
    ];

    protected $casts = [
        'valid_from'       => 'date',
        'valid_until'      => 'date',
        'discount_percent' => 'decimal:2',
        'active'           => 'boolean',
    ];

    public function uses()
    {
        return $this->hasMany(CouponUse::class);
    }

    public static function findValid(string $code, string $email = ''): ?self
    {
        $coupon = self::where('code', strtoupper(trim($code)))
            ->where('active', true)
            ->where(fn($q) => $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', now()))
            ->where(fn($q) => $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', now()))
            ->first();

        if (! $coupon) return null;

        // Verificar si el email ya lo usó
        if ($email && $coupon->uses()->where('email', strtolower($email))->exists()) {
            return null;
        }

        return $coupon;
    }

    public function alreadyUsedBy(string $email): bool
    {
        return $this->uses()->where('email', strtolower($email))->exists();
    }

    public function applyTo(float $amount): float
    {
        return round($amount * $this->discount_percent / 100, 2);
    }

    public function recordUse(string $email): void
    {
        $this->uses()->firstOrCreate(['email' => strtolower($email)]);
        $this->increment('uses_count');
    }
}
