<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesalePayment extends Model
{
    protected $fillable = ['wholesale_request_id', 'product_name', 'quantity', 'amount', 'receipt'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }
}
