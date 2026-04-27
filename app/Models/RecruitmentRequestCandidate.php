<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentRequestCandidate extends Model
{
    public const DECISIONS = [
        'pending' => 'Awaiting Decision',
        'shortlisted' => 'Shortlisted',
        'contacted' => 'Contacted',
        'rejected' => 'Rejected',
        'hired' => 'Hired',
    ];

    protected $fillable = [
        'recruitment_request_id', 'candidate_id',
        'external_name', 'external_email', 'external_phone', 'external_cv_path',
        'summary', 'employer_decision', 'employer_feedback', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
        ];
    }

    public function recruitmentRequest(): BelongsTo
    {
        return $this->belongsTo(RecruitmentRequest::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function displayName(): string
    {
        if ($this->candidate?->user?->name) {
            return $this->candidate->user->name;
        }
        return $this->external_name ?: 'Unnamed Candidate';
    }

    public function displayEmail(): ?string
    {
        return $this->candidate?->user?->email ?: $this->external_email;
    }

    public function isPlatformCandidate(): bool
    {
        return $this->candidate_id !== null;
    }
}
