<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_ATTENDED = 'attended';

    protected $fillable = [
        'event_id', 'user_id', 'order_id', 'status', 'ticket_count',
        'buyer_name', 'buyer_email', 'buyer_phone', 'answers',
        'registered_at', 'admin_viewed_at', 'reminder_count', 'last_reminded_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'registered_at' => 'datetime',
            'ticket_count' => 'integer',
            'admin_viewed_at' => 'datetime',
            'reminder_count' => 'integer',
            'last_reminded_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
