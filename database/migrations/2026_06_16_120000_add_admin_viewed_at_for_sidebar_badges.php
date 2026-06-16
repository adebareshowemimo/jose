<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds "admin has seen this record" tracking so the sidebar can show unread-style
 * count badges (new job listings, new contact messages) that clear as the admin
 * opens each record. Chat already tracks this via chat_messages.read_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_listings') && ! Schema::hasColumn('job_listings', 'admin_viewed_at')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->timestamp('admin_viewed_at')->nullable()->after('status')->index();
            });

            // Existing listings predate view-tracking; treat them as already seen so
            // the badge only counts genuinely new postings from here on.
            DB::table('job_listings')
                ->whereNull('admin_viewed_at')
                ->update(['admin_viewed_at' => DB::raw('created_at')]);
        }

        if (Schema::hasTable('contact_submissions') && ! Schema::hasColumn('contact_submissions', 'admin_viewed_at')) {
            Schema::table('contact_submissions', function (Blueprint $table) {
                $table->timestamp('admin_viewed_at')->nullable()->after('status')->index();
            });

            // Keep only still-"new" submissions unread, so the new sidebar badge
            // starts aligned with the existing "new messages" signal.
            DB::table('contact_submissions')
                ->where('status', '!=', 'new')
                ->whereNull('admin_viewed_at')
                ->update(['admin_viewed_at' => DB::raw('created_at')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_listings', 'admin_viewed_at')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->dropColumn('admin_viewed_at');
            });
        }

        if (Schema::hasColumn('contact_submissions', 'admin_viewed_at')) {
            Schema::table('contact_submissions', function (Blueprint $table) {
                $table->dropColumn('admin_viewed_at');
            });
        }
    }
};
