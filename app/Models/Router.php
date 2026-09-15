<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Router extends Model
{
    protected $fillable = [
        'name', 'ip_address', 'api_port', 'use_ssl', 'username',
        'password', 'hotspot_server', 'default_profile',
        'is_active', 'last_connected_at',
    ];

    protected $casts = [
        'use_ssl' => 'boolean',
        'is_active' => 'boolean',
        'last_connected_at' => 'datetime',
    ];

    protected $hidden = ['password_encrypted'];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    // Store password encrypted at rest, but expose it decrypted for service use only.
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password_encrypted'] = Crypt::encryptString($value);
    }

    public function getDecryptedPasswordAttribute(): string
    {
        return Crypt::decryptString($this->password_encrypted);
    }
}
