<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RecruitmentRequest extends Model
{
    public const SERVICE_TYPES = [
        'cv_sourcing' => 'CV Sourcing',
        'partial_recruitment' => 'Partial Recruitment (Screening & Interviews)',
        'full_recruitment' => 'Full Recruitment (End-to-End)',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'quote_sent' => 'Quote Sent',
        'paid' => 'Paid',
        'in_progress' => 'In Progress',
        'candidates_delivered' => 'Candidates Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'company_id', 'requested_by_user_id',
        'service_type', 'cv_count',
        'job_title', 'category_id', 'job_type_id', 'location_id',
        'experience_level', 'salary_min', 'salary_max', 'salary_currency',
        'description', 'skills_list', 'needed_by', 'jd_file_path',
        'status', 'admin_notes', 'assigned_to_admin_user_id',
        'quoted_amount', 'quoted_at', 'order_id',
    ];

    protected function casts(): array
    {
        return [
            'cv_count' => 'integer',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'skills_list' => 'array',
            'needed_by' => 'date',
            'quoted_amount' => 'decimal:2',
            'quoted_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
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

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_admin_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(RecruitmentRequestCandidate::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function isCancellable(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled'], true);
    }

    public function isMutableByAdmin(): bool
    {
        return $this->status !== 'cancelled';
    }
}
