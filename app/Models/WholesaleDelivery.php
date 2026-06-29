<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleDelivery extends Model
{
    protected $fillable = ['wholesale_request_id', 'date', 'quantity', 'notes'];
    protected $casts = ['date' => 'date'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }
}
