<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrainingProgram extends Model
{
    public const TYPE_TRAINING = 'training';
    public const TYPE_APPRENTICESHIP = 'apprenticeship';

    protected $fillable = [
        'slug', 'title', 'type', 'short_description', 'long_description',
        'image_path', 'price', 'currency', 'duration', 'level', 'capacity',
        'starts_at', 'enrol_deadline', 'category', 'is_active', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'starts_at' => 'date',
            'enrol_deadline' => 'date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'capacity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $row) {
            if (blank($row->slug) && filled($row->title)) {
                $row->slug = Str::slug($row->title);
            }
        });
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(TrainingEnrolment::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }

    public function isFree(): bool
    {
        return $this->price === null || (float) $this->price <= 0;
    }
}
