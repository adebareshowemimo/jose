<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'category',
        'display_date',
        'starts_at',
        'ends_at',
        'location',
        'description',
        'image_path',
        'register_url',
        'price',
        'currency',
        'capacity',
        'seats_sold',
        'questions',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'seats_sold' => 'integer',
        'questions' => 'array',
    ];

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['upcoming', 'active', 'completed']);
    }

    public function scopeHosted($query)
    {
        return $query->where('category', 'hosted');
    }

    public function scopeIndustry($query)
    {
        return $query->where('category', 'industry');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }

    public function hasExternalUrl(): bool
    {
        return ! empty($this->register_url);
    }

    public function isPaid(): bool
    {
        return ! $this->hasExternalUrl() && $this->price !== null && (float) $this->price > 0;
    }

    public function isFreeInternal(): bool
    {
        return ! $this->hasExternalUrl() && ($this->price === null || (float) $this->price <= 0);
    }

    public function seatsRemaining(): ?int
    {
        if ($this->capacity === null) return null;
        return max(0, (int) $this->capacity - (int) $this->seats_sold);
    }

    public function isSoldOut(): bool
    {
        return $this->seatsRemaining() === 0;
    }
}
