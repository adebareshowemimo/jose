<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds "admin has seen this application" tracking so the sidebar Applications badge
 * clears when the admin opens the applications list, matching the other badges.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_applications') && ! Schema::hasColumn('job_applications', 'admin_viewed_at')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->timestamp('admin_viewed_at')->nullable()->after('status')->index();
            });

            // Already-actioned applications (anything past the initial "applied" state)
            // count as seen; brand-new ones stay unread so the badge keeps its value.
            DB::table('job_applications')
                ->where('status', '!=', 'applied')
                ->whereNull('admin_viewed_at')
                ->update(['admin_viewed_at' => DB::raw('created_at')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_applications', 'admin_viewed_at')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropColumn('admin_viewed_at');
            });
        }
    }
};
