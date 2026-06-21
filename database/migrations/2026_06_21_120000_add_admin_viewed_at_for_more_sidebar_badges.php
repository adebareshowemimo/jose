<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the sidebar "admin has seen this record" tracking to the remaining
 * inbound-activity sections so each can show an unread-style count badge that
 * clears as the admin reviews the items:
 *   - orders                → new orders
 *   - payments              → new payments
 *   - event_registrations   → new event registrations (drives the Events badge)
 *   - training_enrolments   → new enrolments (drives the Training badge)
 *   - newsletter_subscribers→ new subscribers
 *
 * Contract Staffing reuses job_applications.admin_viewed_at (already present), so
 * it needs no column here.
 *
 * Every existing row is backfilled as already-seen so the badges only count
 * genuinely new activity from here on.
 */
return new class extends Migration
{
    /**
     * @var array<string,string> table => column to anchor the new column after
     */
    private array $tables = [
        'orders' => 'status',
        'payments' => 'status',
        'event_registrations' => 'status',
        'training_enrolments' => 'status',
        'newsletter_subscribers' => 'status',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $after) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'admin_viewed_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $t) use ($after) {
                $t->timestamp('admin_viewed_at')->nullable()->after($after)->index();
            });

            // Pre-existing records predate view-tracking — treat them as seen.
            DB::table($table)
                ->whereNull('admin_viewed_at')
                ->update(['admin_viewed_at' => DB::raw('created_at')]);
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            if (Schema::hasColumn($table, 'admin_viewed_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('admin_viewed_at');
                });
            }
        }
    }
};
