<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesalePayment extends Model
{
    protected $fillable = ['wholesale_request_id', 'date', 'product_name', 'quantity', 'amount', 'receipt'];
    protected $casts = ['date' => 'date'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }
}
