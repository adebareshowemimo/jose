<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Support\EmailDispatcher;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'emails:send-event-reminders {--dry-run : Show who would be reminded without sending}';

    protected $description = 'Send per-event email reminders to registered attendees per each event\'s configured cadence.';

    public function handle(EmailDispatcher $dispatcher): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $today = now()->startOfDay();

        // Only upcoming events with reminders switched on and a real date.
        $events = Event::query()
            ->where('reminders_enabled', true)
            ->whereIn('status', ['upcoming', 'active'])
            ->whereNotNull('starts_at')
            ->whereDate('starts_at', '>=', $today)
            ->get();

        $totalSent = 0;
        $eventsTouched = 0;

        foreach ($events as $event) {
            $eventDate = $event->starts_at->copy()->startOfDay();
            $daysUntil = (int) $today->diffInDays($eventDate, false);

            // Act only once we're inside the lead window (and the event hasn't passed).
            if ($daysUntil < 0 || $daysUntil > (int) $event->reminder_lead_days) {
                continue;
            }

            $templateKey = $event->reminder_template_key ?: Event::DEFAULT_REMINDER_TEMPLATE;
            $maxCount = max(1, (int) $event->reminder_max_count);
            $repeatDays = (int) ($event->reminder_repeat_days ?? 0); // 0 / null = once only
            $sentForEvent = 0;

            $registrations = $event->remindableRegistrations()
                ->whereNotNull('buyer_email')
                ->get();

            foreach ($registrations as $reg) {
                if (! $this->isDue($reg, $repeatDays, $maxCount, $today)) {
                    continue;
                }

                $this->line("  → {$event->title}: reminder to {$reg->buyer_email} ({$daysUntil}d before)");

                if ($dryRun) {
                    $sentForEvent++;
                    $totalSent++;
                    continue;
                }

                $ok = $dispatcher->send($templateKey, [$reg->buyer_email, $reg->user_id, $reg->buyer_name], [
                    'name' => $reg->buyer_name,
                    'event_title' => $event->title,
                    'event_date' => $event->display_date ?: $eventDate->format('D, M j, Y'),
                    'event_location' => $event->location,
                    'days_until' => $daysUntil,
                    'ticket_count' => $reg->ticket_count,
                    'event_url' => route('events.register.show', $event),
                ]);

                if ($ok) {
                    $reg->forceFill([
                        'reminder_count' => $reg->reminder_count + 1,
                        'last_reminded_at' => now(),
                    ])->save();
                    $sentForEvent++;
                    $totalSent++;
                }
            }

            if ($sentForEvent > 0) {
                $eventsTouched++;
            }
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Done. Events processed: {$eventsTouched}, reminders sent: {$totalSent}.");

        return self::SUCCESS;
    }

    /**
     * A registration is due for a reminder when it's under the cap and either has
     * never been reminded, or — for repeating cadences — enough days have passed
     * since the last one. A non-repeating cadence sends exactly once.
     */
    protected function isDue($reg, int $repeatDays, int $maxCount, $today): bool
    {
        if ($reg->reminder_count >= $maxCount) {
            return false;
        }
        if (! $reg->last_reminded_at) {
            return true;
        }
        if ($repeatDays <= 0) {
            return false;
        }

        return $reg->last_reminded_at->copy()->startOfDay()
            ->lte($today->copy()->subDays($repeatDays));
    }
}
