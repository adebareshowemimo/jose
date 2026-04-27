<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily candidate reminders (CV upload + profile completion).
// Set the schedule:run cron to run every minute and this fires once per day.
Schedule::command('emails:send-candidate-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping();
