<?php

namespace App\Console\Commands;

use App\Models\CandidateBoost;
use Illuminate\Console\Command;

/**
 * Moves lapsed boosts from 'active' to 'expired'.
 *
 * Nothing previously performed this transition, so every boost row stayed
 * 'active' forever and any admin count or filter built on the status column
 * was wrong. This does not touch candidates.featured_until: actual placement
 * is already driven by that date and lapses correctly on its own.
 */
class ExpireCandidateBoosts extends Command
{
    protected $signature = 'boosts:expire {--dry-run : List what would be expired without writing}';

    protected $description = 'Mark candidate boosts as expired once their end date has passed.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $query = CandidateBoost::lapsed();
        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No boosts to expire.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("DRY RUN - {$total} boost(s) would be marked expired:");

            (clone $query)->with('candidate.user')->orderBy('ends_at')
                ->chunkById(200, function ($boosts) {
                    foreach ($boosts as $boost) {
                        $this->line(sprintf(
                            '  #%d  candidate=%s  ended=%s',
                            $boost->id,
                            $boost->candidate?->user?->email ?? 'unknown',
                            $boost->ends_at?->toDateTimeString() ?? '-'
                        ));
                    }
                });

            return self::SUCCESS;
        }

        // Chunked so a large backlog cannot exhaust memory. chunkById is safe
        // here even though the update removes rows from the result set.
        $expired = 0;
        $query->select('id')->chunkById(200, function ($boosts) use (&$expired) {
            $ids = $boosts->pluck('id')->all();
            $expired += CandidateBoost::whereIn('id', $ids)
                ->update(['status' => CandidateBoost::STATUS_EXPIRED]);
        });

        $this->info("Expired {$expired} boost(s).");

        return self::SUCCESS;
    }
}
