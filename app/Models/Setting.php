<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'is_encrypted'];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($raw) {
                if ($raw === null || $raw === '') {
                    return null;
                }
                $decoded = json_decode($raw, true);
                if ($this->is_encrypted && is_string($decoded) && $decoded !== '') {
                    try {
                        return Crypt::decryptString($decoded);
                    } catch (\Throwable $e) {
                        return null;
                    }
                }
                return $decoded;
            },
            set: function ($value) {
                if ($this->is_encrypted && is_string($value) && $value !== '') {
                    $value = Crypt::encryptString($value);
                }
                return json_encode($value);
            },
        );
    }
}
