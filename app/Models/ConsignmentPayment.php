<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentPayment extends Model
{
    protected $fillable = ['wholesale_request_id', 'amount', 'receipt', 'notes'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }
}
