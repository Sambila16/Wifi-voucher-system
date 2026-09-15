<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'price', 'data_limit_mb', 'time_limit_minutes',
        'validity_days', 'mikrotik_profile', 'shared_users', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
