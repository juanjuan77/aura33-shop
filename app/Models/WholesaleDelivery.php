<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleDelivery extends Model
{
    protected $fillable = ['wholesale_request_id', 'quantity', 'notes'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }
}
