<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'properties', 'price_retail', 'price_wholesale', 'stock',
        'sku', 'image', 'images', 'featured', 'active', 'sort_order',
    ];

    protected $casts = [
        'properties' => 'array',
        'images'     => 'array',
        'featured'   => 'boolean',
        'active'     => 'boolean',
        'price_retail' => 'decimal:2',
        'price_wholesale' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helpers
    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return '';
        }
        // Si empieza con "img/" es un path relativo a public/
        if (str_starts_with($this->image, 'img/')) {
            return asset($this->image);
        }
        // Si no, es un archivo subido a storage
        return asset('storage/' . $this->image);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(fn() => Cache::forget('home.featured'));
        static::deleted(fn() => Cache::forget('home.featured'));
    }
}
