<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentRestockRequest extends Model
{
    protected $fillable = ['wholesale_request_id', 'items', 'status', 'notes'];

    protected $casts = ['items' => 'array'];

    public function wholesaler()
    {
        return $this->belongsTo(WholesaleRequest::class, 'wholesale_request_id');
    }
}
