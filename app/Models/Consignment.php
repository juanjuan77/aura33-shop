<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
{
    protected $fillable = ['wholesale_request_id', 'notes', 'status'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }

    public function items()
    {
        return $this->hasMany(ConsignmentItem::class);
    }

    public function reports()
    {
        return $this->hasMany(ConsignmentReport::class);
    }

    public function totalDelivered(): float
    {
        return $this->items->sum(fn($i) => $i->quantity * $i->unit_price);
    }
}
