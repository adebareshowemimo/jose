<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\EmailLog;
use App\Models\Role;
use App\Models\User;
use App\Support\EmailDispatcher;
use Illuminate\Console\Command;

class SendCandidateReminders extends Command
{
    protected $signature = 'emails:send-candidate-reminders {--dry-run : Show who would be reminded without sending}';

    protected $description = 'Send CV-upload and profile-completion reminder emails per the configured cadence.';

    public function handle(EmailDispatcher $dispatcher): int
    {
        $firstAfterDays   = (int) setting('reminders.first_after_days', 3);
        $repeatEveryDays  = max(1, (int) setting('reminders.repeat_every_days', 7));
        $maxCount         = max(1, (int) setting('reminders.max_count', 3));
        $profileThreshold = (int) setting('reminders.profile_threshold_percent', 70);
        $dryRun = (bool) $this->option('dry-run');

        $candidateRole = Role::where('name', 'candidate')->first();
        if (! $candidateRole) {
            $this->warn('No candidate role found. Aborting.');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($firstAfterDays);
        $users = User::where('role_id', $candidateRole->id)
            ->where('status', 'active')
            ->where('created_at', '<=', $cutoff)
            ->with('candidate.skills', 'candidate.resumes')
            ->get();

        $cvSent = 0;
        $profileSent = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $candidate = $user->candidate;

            // CV reminder ────────────────────────────────────────
            $hasCv = $candidate && $candidate->resumes->count() > 0;
            if (! $hasCv) {
                if ($this->shouldSend($user, 'reminder.cv_upload', $repeatEveryDays, $maxCount)) {
                    $this->line("  → CV reminder to {$user->email}");
                    if (! $dryRun) {
                        $dispatcher->send('reminder.cv_upload', $user, [
                            'cv_url' => url('/user/cv-manager'),
                        ]);
                    }
                    $cvSent++;
                } else {
                    $skipped++;
                }
            }

            // Profile completion reminder ────────────────────────
            $percent = $this->profileCompletion($user, $candidate);
            if ($percent < $profileThreshold) {
                if ($this->shouldSend($user, 'reminder.profile_completion', $repeatEveryDays, $maxCount)) {
                    $this->line("  → Profile reminder to {$user->email} ({$percent}%)");
                    if (! $dryRun) {
                        $dispatcher->send('reminder.profile_completion', $user, [
                            'completion_percent' => $percent,
                            'profile_url' => url('/user/candidate/profile'),
                        ]);
                    }
                    $profileSent++;
                }
            }
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Done. CV reminders: {$cvSent}, profile reminders: {$profileSent}, skipped: {$skipped}.");
        return self::SUCCESS;
    }

    protected function shouldSend(User $user, string $key, int $repeatEveryDays, int $maxCount): bool
    {
        $logs = EmailLog::where('user_id', $user->id)
            ->where('template_key', $key)
            ->where('status', 'sent')
            ->orderByDesc('sent_at')
            ->get();

        if ($logs->count() >= $maxCount) {
            return false;
        }
        if ($logs->isNotEmpty() && $logs->first()->sent_at?->gt(now()->subDays($repeatEveryDays))) {
            return false;
        }
        return true;
    }

    protected function profileCompletion(User $user, ?Candidate $candidate): int
    {
        $completion = 0;
        $fields = 3;
        if ($user->name) $completion++;
        if ($user->email) $completion++;
        if ($user->avatar) $completion++;

        if ($candidate) {
            $fields += 8;
            if ($candidate->title) $completion++;
            if ($candidate->bio) $completion++;
            if ($candidate->education && count($candidate->education)) $completion++;
            if ($candidate->experience && count($candidate->experience)) $completion++;
            if ($candidate->skills->count()) $completion++;
            if ($candidate->location_id) $completion++;
            if ($candidate->resumes->count()) $completion++;
            if ($candidate->social_links && count($candidate->social_links)) $completion++;
        }

        return $fields > 0 ? (int) round(($completion / $fields) * 100) : 0;
    }
}
