<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Integration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class IntegrationConfig extends Model
{
    protected $fillable = [
        'module',
        'provider',
        'key_name',
        'encrypted_value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Automatically encrypt/decrypt value
    protected function encryptedValue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }
}
