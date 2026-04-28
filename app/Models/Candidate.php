<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'bio', 'gender', 'date_of_birth',
        'education', 'experience', 'awards', 'languages', 'education_level',
        'experience_years', 'expected_salary', 'salary_type', 'website',
        'video_url', 'social_links', 'location_id', 'address',
        'latitude', 'longitude', 'allow_search', 'is_available', 'skills_list',
        'featured_until',
    ];

    protected function casts(): array
    {
        return [
            'education' => 'array',
            'experience' => 'array',
            'awards' => 'array',
            'languages' => 'array',
            'social_links' => 'array',
            'skills_list' => 'array',
            'date_of_birth' => 'date',
            'expected_salary' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'allow_search' => 'boolean',
            'is_available' => 'boolean',
            'featured_until' => 'datetime',
        ];
    }

    public function boosts(): HasMany
    {
        return $this->hasMany(CandidateBoost::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function scopeFeatured($query)
    {
        return $query->whereNotNull('featured_until')->where('featured_until', '>', now());
    }

    public function isFeatured(): bool
    {
        return $this->featured_until && $this->featured_until->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skill');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'candidate_category');
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function wishlists(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }
}
