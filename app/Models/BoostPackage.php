<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoostPackage extends Model
{
    protected $fillable = [
        'label', 'tagline', 'days', 'price', 'perks', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'days' => 'integer',
            'price' => 'decimal:2',
            'perks' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function boosts(): HasMany
    {
        return $this->hasMany(CandidateBoost::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('days');
    }

    /**
     * A package is only purchasable if it is active and actually costs
     * something. A zero price would otherwise hand out free boosts.
     */
    public function isPurchasable(): bool
    {
        return $this->is_active && (float) $this->price > 0;
    }

    public function hasPerk(string $key): bool
    {
        return (bool) ($this->perks[$key] ?? false);
    }
}
