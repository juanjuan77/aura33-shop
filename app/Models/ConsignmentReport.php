<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentReport extends Model
{
    protected $fillable = [
        'wholesale_request_id', 'consignment_id', 'description',
        'amount', 'receipt', 'status', 'confirmed_at', 'admin_notes',
    ];

    protected $casts = ['confirmed_at' => 'datetime'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }

    public function consignment()
    {
        return $this->belongsTo(Consignment::class);
    }
}
