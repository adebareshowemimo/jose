<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfileView extends Model
{
    protected $fillable = [
        'candidate_id', 'viewer_user_id', 'source',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_user_id');
    }

    /**
     * Record a profile view, de-duplicated to one view per viewer per candidate per day.
     * A candidate viewing their own profile is never counted.
     */
    public static function record(Candidate $candidate, ?User $viewer, string $source = 'public'): void
    {
        if ($viewer && $viewer->id === $candidate->user_id) {
            return;
        }

        $query = static::query()
            ->where('candidate_id', $candidate->id)
            ->whereDate('created_at', now()->toDateString());

        $viewer
            ? $query->where('viewer_user_id', $viewer->id)
            : $query->whereNull('viewer_user_id');

        if ($query->exists()) {
            return;
        }

        static::create([
            'candidate_id' => $candidate->id,
            'viewer_user_id' => $viewer?->id,
            'source' => $source,
        ]);
    }
}
