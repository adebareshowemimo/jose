<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds "admin has seen this company" tracking so the sidebar Companies badge
 * clears as the admin opens each company, matching the job/contact badges.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies') && ! Schema::hasColumn('companies', 'admin_viewed_at')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->timestamp('admin_viewed_at')->nullable()->after('is_verified')->index();
            });

            // Treat already-verified companies as seen; leave unverified ones unread so
            // the badge keeps its current value and decrements as the admin opens each.
            DB::table('companies')
                ->where('is_verified', true)
                ->whereNull('admin_viewed_at')
                ->update(['admin_viewed_at' => DB::raw('created_at')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('companies', 'admin_viewed_at')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('admin_viewed_at');
            });
        }
    }
};
