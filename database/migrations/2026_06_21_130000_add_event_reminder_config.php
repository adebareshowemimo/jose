<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-event email reminder configuration + per-attendee send tracking.
 *
 * Events carry a "lead time + repeat" cadence (mirrors the candidate-reminder
 * cadence): start sending `reminder_lead_days` before the event, then repeat
 * every `reminder_repeat_days` (null = once only), capped at `reminder_max_count`
 * reminders per attendee. The chosen email template is `reminder_template_key`.
 *
 * Each registration tracks its own progress (count + last sent) so the daily
 * sender command stays idempotent and respects the per-attendee cadence.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (! Schema::hasColumn('events', 'reminders_enabled')) {
                    $table->boolean('reminders_enabled')->default(false)->after('status');
                    $table->string('reminder_template_key')->nullable()->after('reminders_enabled');
                    $table->unsignedSmallInteger('reminder_lead_days')->default(7)->after('reminder_template_key');
                    $table->unsignedSmallInteger('reminder_repeat_days')->nullable()->after('reminder_lead_days');
                    $table->unsignedTinyInteger('reminder_max_count')->default(3)->after('reminder_repeat_days');
                }
            });
        }

        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (! Schema::hasColumn('event_registrations', 'reminder_count')) {
                    $table->unsignedInteger('reminder_count')->default(0)->after('registered_at');
                    $table->timestamp('last_reminded_at')->nullable()->after('reminder_count');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'reminders_enabled')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn([
                    'reminders_enabled',
                    'reminder_template_key',
                    'reminder_lead_days',
                    'reminder_repeat_days',
                    'reminder_max_count',
                ]);
            });
        }

        if (Schema::hasColumn('event_registrations', 'reminder_count')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropColumn(['reminder_count', 'last_reminded_at']);
            });
        }
    }
};
