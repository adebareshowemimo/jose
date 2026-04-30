<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    public const TYPE_EMPLOYER_CANDIDATE = 'employer_candidate';
    public const TYPE_ADMIN_CANDIDATE = 'admin_candidate';
    public const TYPE_ADMIN_EMPLOYER = 'admin_employer';

    protected $fillable = [
        'type',
        'company_id',
        'candidate_id',
        'recruitment_request_candidate_id',
        'started_by_user_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function recruitmentRequestCandidate(): BelongsTo
    {
        return $this->belongsTo(RecruitmentRequestCandidate::class);
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function contextLabel(): string
    {
        if (in_array($this->type, [self::TYPE_ADMIN_CANDIDATE, self::TYPE_ADMIN_EMPLOYER], true)) {
            return 'Admin conversation';
        }

        return $this->recruitmentRequestCandidate?->recruitmentRequest?->job_title ?: 'Recruitment candidate';
    }
}
