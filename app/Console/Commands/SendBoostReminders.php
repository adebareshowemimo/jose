<?php

namespace App\Console\Commands;

use App\Models\CandidateBoost;
use App\Support\EmailDispatcher;
use Illuminate\Console\Command;

/**
 * Emails candidates whose boost is about to lapse, and confirms once it has.
 *
 * Dedup is stamped on the boost row rather than keyed on user + template, so a
 * candidate who buys a second boost is reminded about that one too, while a
 * re-run or an overlapping cron cannot double-send for the same boost.
 */
class SendBoostReminders extends Command
{
    protected $signature = 'boosts:send-reminders
                            {--dry-run : List who would be emailed without sending}';

    protected $description = 'Email candidates whose profile boost is expiring soon or has just expired.';

    public function handle(EmailDispatcher $dispatcher): int
    {
        if (! (bool) setting('boost.reminders_enabled', true)) {
            $this->info('Boost reminders are disabled in settings. Nothing to do.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $leadDays = max(1, (int) setting('boost.reminder_lead_days', 3));

        $expiring = $this->sendExpiringSoon($dispatcher, $leadDays, $dryRun);
        $expired = $this->sendExpired($dispatcher, $dryRun);

        $prefix = $dryRun ? 'DRY RUN - would send' : 'Sent';
        $this->info("{$prefix} {$expiring} expiring-soon and {$expired} expired notice(s).");

        return self::SUCCESS;
    }

    private function sendExpiringSoon(EmailDispatcher $dispatcher, int $leadDays, bool $dryRun): int
    {
        $boosts = CandidateBoost::with(['candidate.user', 'package'])
            ->where('status', CandidateBoost::STATUS_ACTIVE)
            ->whereNull('expiring_reminder_sent_at')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays($leadDays)])
            ->get();

        $sent = 0;

        foreach ($boosts as $boost) {
            $user = $boost->candidate?->user;
            if (! $user) {
                continue;
            }

            $daysLeft = max(1, (int) ceil(now()->floatDiffInDays($boost->ends_at)));

            if ($dryRun) {
                $this->line("  expiring: {$user->email} ({$daysLeft}d left)");
                $sent++;
                continue;
            }

            $dispatcher->send('candidate.boost_expiring', $user, [
                'days_left' => $daysLeft,
                'ends_at' => $boost->ends_at->format('M d, Y'),
                'package_label' => $boost->package?->label ?? 'profile',
                'renew_url' => route('candidate.boost.index'),
            ]);

            // Stamped after dispatch so a send failure is retried next run.
            $boost->update(['expiring_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }

    private function sendExpired(EmailDispatcher $dispatcher, bool $dryRun): int
    {
        // Only boosts the expiry sweep has already closed, so this cannot fire
        // for a boost that is somehow still running.
        $boosts = CandidateBoost::with(['candidate.user', 'package'])
            ->where('status', CandidateBoost::STATUS_EXPIRED)
            ->whereNull('expired_notice_sent_at')
            ->whereNotNull('ends_at')
            // Anything older than a week is backlog from before this command
            // existed; emailing about it now would only confuse people.
            ->where('ends_at', '>=', now()->subDays(7))
            ->where('ends_at', '<=', now())
            ->get();

        $sent = 0;

        foreach ($boosts as $boost) {
            $user = $boost->candidate?->user;
            if (! $user) {
                continue;
            }

            if ($dryRun) {
                $this->line("  expired: {$user->email}");
                $sent++;
                continue;
            }

            $dispatcher->send('candidate.boost_expired', $user, [
                'ends_at' => $boost->ends_at->format('M d, Y'),
                'package_label' => $boost->package?->label ?? 'profile',
                'renew_url' => route('candidate.boost.index'),
            ]);

            $boost->update(['expired_notice_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }
}
