<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentItem extends Model
{
    protected $fillable = ['consignment_id', 'product_name', 'quantity', 'unit_price'];

    public function consignment()
    {
        return $this->belongsTo(Consignment::class);
    }
}
