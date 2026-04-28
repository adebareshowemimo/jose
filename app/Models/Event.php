<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
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

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }
}
