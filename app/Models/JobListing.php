<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'posted_by', 'title', 'slug', 'description', 'qualification',
        'category_id', 'job_type_id', 'location_id', 'address', 'latitude', 'longitude',
        'salary_min', 'salary_max', 'salary_type', 'experience_required',
        'gender_preference', 'deadline', 'apply_method', 'apply_url', 'apply_email',
        'vacancies', 'thumbnail', 'gallery', 'video_url', 'hours', 'hours_type',
        'is_featured', 'is_urgent', 'is_approved', 'status',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'deadline' => 'date',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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
