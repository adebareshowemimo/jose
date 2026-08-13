<?php

namespace App\Support;

use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\CandidateBoost;
use App\Models\User;

/**
 * Decides whether a candidate may buy a boost, and why not.
 *
 * The rules live here rather than in the controller so the boost page and the
 * purchase endpoint cannot drift apart - a page that offers a package the
 * purchase then rejects is worse than not offering it.
 */
class BoostEligibility
{
    public function __construct(private Settings $settings)
    {
    }

    public function featureEnabled(): bool
    {
        return (bool) $this->setting('boost.enabled', true);
    }

    /**
     * Every reason this candidate cannot buy right now. Empty means eligible.
     *
     * @return array<int, string>
     */
    public function blockers(User $user, ?Candidate $candidate): array
    {
        $reasons = [];

        if (! $candidate) {
            return ['Complete your candidate profile before boosting your visibility.'];
        }

        if ((bool) $this->setting('boost.require_verified_email', false)
            && ! $user->hasVerifiedEmail()) {
            $reasons[] = 'Verify your email address before boosting your profile.';
        }

        $minCompletion = (int) $this->setting('boost.min_profile_completion', 0);
        if ($minCompletion > 0) {
            $completion = $user->profileCompletion();
            if ($completion < $minCompletion) {
                $reasons[] = "Your profile is {$completion}% complete. Boosting requires at least {$minCompletion}%.";
            }
        }

        if ((bool) $this->setting('boost.require_cv', false)
            && $candidate->resumes()->count() === 0) {
            $reasons[] = 'Upload a CV before boosting your profile.';
        }

        if ((bool) $this->setting('boost.block_when_active', false)
            && $this->hasRunningBoost($candidate)) {
            $reasons[] = 'You already have an active boost. You can buy another once it ends.';
        }

        $maxStacked = (int) $this->setting('boost.max_stacked_days', 0);
        if ($maxStacked > 0) {
            $remaining = $this->remainingDays($candidate);
            if ($remaining >= $maxStacked) {
                $reasons[] = "You already have {$remaining} days of boost queued, and the limit is {$maxStacked}.";
            }
        }

        $cooldown = (int) $this->setting('boost.cooldown_days', 0);
        if ($cooldown > 0) {
            $lastEnd = CandidateBoost::where('candidate_id', $candidate->id)
                ->whereIn('status', [CandidateBoost::STATUS_EXPIRED])
                ->whereNotNull('ends_at')
                ->orderByDesc('ends_at')
                ->value('ends_at');

            if ($lastEnd && $lastEnd->copy()->addDays($cooldown)->isFuture()) {
                $available = $lastEnd->copy()->addDays($cooldown)->format('M d, Y');
                $reasons[] = "You can boost again from {$available}.";
            }
        }

        $maxPerYear = (int) $this->setting('boost.max_per_year', 0);
        if ($maxPerYear > 0) {
            $thisYear = CandidateBoost::where('candidate_id', $candidate->id)
                ->where('status', '!=', CandidateBoost::STATUS_REFUNDED)
                ->where('created_at', '>=', now()->subYear())
                ->count();

            if ($thisYear >= $maxPerYear) {
                $reasons[] = "You have reached the limit of {$maxPerYear} boosts per year.";
            }
        }

        return $reasons;
    }

    public function passes(User $user, ?Candidate $candidate): bool
    {
        return $this->blockers($user, $candidate) === [];
    }

    /**
     * Buying this package would exceed the stacked-days cap.
     */
    public function exceedsStackCap(?Candidate $candidate, BoostPackage $package): bool
    {
        $maxStacked = (int) $this->setting('boost.max_stacked_days', 0);

        if ($maxStacked <= 0 || ! $candidate) {
            return false;
        }

        return ($this->remainingDays($candidate) + $package->days) > $maxStacked;
    }

    /**
     * Days of featured placement the candidate still has queued.
     */
    public function remainingDays(?Candidate $candidate): int
    {
        if (! $candidate?->featured_until || $candidate->featured_until->isPast()) {
            return 0;
        }

        return (int) ceil(now()->floatDiffInDays($candidate->featured_until));
    }

    private function hasRunningBoost(Candidate $candidate): bool
    {
        return CandidateBoost::where('candidate_id', $candidate->id)
            ->where('status', CandidateBoost::STATUS_ACTIVE)
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->exists();
    }

    private function setting(string $key, $default)
    {
        return $this->settings->get($key, $default);
    }
}
