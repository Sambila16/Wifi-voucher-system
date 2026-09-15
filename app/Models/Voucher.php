<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'password', 'plan_id', 'router_id', 'status', 'source',
        'generated_by_admin_id', 'customer_phone', 'issued_at', 'activated_at',
        'expires_at', 'revoked_at', 'mac_address',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function isUsable(): bool
    {
        return in_array($this->status, ['issued', 'active'])
            && (! $this->expires_at || $this->expires_at->isFuture());
    }
}
