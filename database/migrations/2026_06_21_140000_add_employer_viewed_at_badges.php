<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Per-employer "has the employer seen this" tracking, so the employer dashboard
 * sidebar can show unread-style badges that clear as the employer reviews each
 * thing. This is separate from admin_viewed_at (admin and employer see different
 * inboxes of the same records).
 *
 *   - job_applications.employer_viewed_at   → new applicants badge
 *   - recruitment_requests.employer_viewed_at → hiring-services updates badge
 *   - payments.employer_viewed_at           → payments & receipts badge
 *   - receipts.employer_viewed_at           → payments & receipts badge
 *
 * Existing rows are backfilled as already-seen so the badges only count genuinely
 * new activity from here on.
 */
return new class extends Migration
{
    /**
     * @var array<string,string> table => column to backfill employer_viewed_at from
     */
    private array $tables = [
        'job_applications' => 'created_at',
        'recruitment_requests' => 'updated_at',
        'payments' => 'updated_at',
        'receipts' => 'created_at',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $backfillFrom) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'employer_viewed_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $t) {
                $t->timestamp('employer_viewed_at')->nullable()->index();
            });

            DB::table($table)
                ->whereNull('employer_viewed_at')
                ->update(['employer_viewed_at' => DB::raw($backfillFrom)]);
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            if (Schema::hasColumn($table, 'employer_viewed_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('employer_viewed_at');
                });
            }
        }
    }
};
