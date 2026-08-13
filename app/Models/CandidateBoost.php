<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateBoost extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'candidate_id', 'order_id', 'boost_package_id', 'days', 'starts_at',
        'ends_at', 'status', 'price', 'currency',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'days' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(BoostPackage::class, 'boost_package_id');
    }

    /**
     * Still running: marked active and not yet past its end date.
     *
     * Checks the date as well as the status so a row the expiry command has
     * not yet swept is not reported as active.
     */
    public function isCurrentlyActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->ends_at
            && $this->ends_at->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Active rows whose end date has passed - the backlog for boosts:expire.
     */
    public function scopeLapsed($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now());
    }
}
