<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key', 'name', 'category', 'subject', 'body_html', 'variables', 'is_active', 'is_custom',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_custom' => 'boolean',
    ];

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }
}
