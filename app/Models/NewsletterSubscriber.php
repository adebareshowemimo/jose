<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    protected $fillable = [
        'email', 'name', 'status', 'token', 'source',
        'subscribed_at', 'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if (empty($row->token)) {
                $row->token = self::makeToken();
            }
        });
    }

    public static function makeToken(): string
    {
        do {
            $token = Str::random(48);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function unsubscribeUrl(): string
    {
        return route('newsletter.unsubscribe', $this->token);
    }
}
