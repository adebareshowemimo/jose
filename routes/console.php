<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily candidate reminders (CV upload + profile completion).
// Set the schedule:run cron to run every minute and this fires once per day,
// at the admin-configured time (Admin → Settings → Reminders).
Schedule::command('emails:send-candidate-reminders')
    ->dailyAt(setting('reminders.send_at_time', '09:00'))
    ->withoutOverlapping();

// Daily event reminders — emails registered attendees per each event's
// configured lead-time + repeat cadence.
Schedule::command('emails:send-event-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping();

// Sweep lapsed candidate boosts from 'active' to 'expired'.
// Hourly rather than daily so the admin screens stay accurate through the
// day. Placement itself already lapses via candidates.featured_until; this
// only keeps the boost rows truthful.
Schedule::command('boosts:expire')
    ->hourly()
    ->withoutOverlapping();
