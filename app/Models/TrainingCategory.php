<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrainingCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'short_description', 'intro_html',
        'bullet_points', 'hero_image_path', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'bullet_points' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $row) {
            if (blank($row->slug) && filled($row->name)) {
                $row->slug = Str::slug($row->name);
            }
        });
    }

    public function programs(): HasMany
    {
        return $this->hasMany(TrainingProgram::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image_path
            ? Storage::disk('public')->url($this->hero_image_path)
            : null;
    }
}
