<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['name', 'provinces', 'price', 'free_from', 'sort_order'];

    protected $casts = ['provinces' => 'array', 'price' => 'decimal:2', 'free_from' => 'decimal:2'];

    public static function forProvince(string $province): ?self
    {
        return self::all()->first(function ($zone) use ($province) {
            return in_array($province, $zone->provinces);
        });
    }

    public static function allProvinces(): array
    {
        return [
            'Buenos Aires', 'CABA', 'Catamarca', 'Chaco', 'Chubut',
            'Córdoba', 'Corrientes', 'Entre Ríos', 'Formosa', 'Jujuy',
            'La Pampa', 'La Rioja', 'Mendoza', 'Misiones', 'Neuquén',
            'Río Negro', 'Salta', 'San Juan', 'San Luis', 'Santa Cruz',
            'Santa Fe', 'Santiago del Estero', 'Tierra del Fuego', 'Tucumán',
        ];
    }
}
