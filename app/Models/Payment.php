<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'voucher_id', 'plan_id', 'amount', 'method', 'gateway',
        'gateway_reference', 'customer_phone', 'mobile_network', 'status',
        'received_by_admin_id', 'gateway_payload', 'paid_at',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
