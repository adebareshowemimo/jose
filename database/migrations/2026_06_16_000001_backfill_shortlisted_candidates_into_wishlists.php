<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill the Saved CVs shortlist (wishlists) from candidates that employers had
     * already marked "shortlisted" on their recruitment requests before the two were
     * wired together. Going forward this mirroring happens in
     * RecruitmentRequestController::decide(); this migration covers existing rows so
     * they show up under /employer/resumes?saved=1 immediately.
     *
     * Idempotent: the wishlists (user_id, wishlistable_type, wishlistable_id) unique
     * index means insertOrIgnore skips any candidate already saved.
     */
    public function up(): void
    {
        $rows = DB::table('recruitment_request_candidates as rc')
            ->join('recruitment_requests as rr', 'rr.id', '=', 'rc.recruitment_request_id')
            ->where('rc.employer_decision', 'shortlisted')
            ->whereNotNull('rc.candidate_id')               // external (off-platform) candidates have no profile to show
            ->whereNotNull('rr.requested_by_user_id')
            ->get(['rr.requested_by_user_id as user_id', 'rc.candidate_id']);

        if ($rows->isEmpty()) {
            return;
        }

        $now = now();

        $payload = $rows->map(fn ($r) => [
            'user_id' => $r->user_id,
            // Matches the morph value stored by toggleSavedCandidate / browseResumes.
            'wishlistable_type' => 'App\\Models\\Candidate',
            'wishlistable_id' => $r->candidate_id,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DB::table('wishlists')->insertOrIgnore($payload);
    }

    /**
     * No-op: backfilled saves are indistinguishable from manual saves, so we cannot
     * safely remove only what this migration added.
     */
    public function down(): void
    {
    }
};
