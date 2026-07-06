<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_listings') || ! Schema::hasColumn('job_listings', 'qualification')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE job_listings MODIFY qualification TEXT NULL');
            return;
        }

        Schema::table('job_listings', function (Blueprint $table) {
            $table->text('qualification')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_listings') || ! Schema::hasColumn('job_listings', 'qualification')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE job_listings MODIFY qualification VARCHAR(255) NULL');
            return;
        }

        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('qualification')->nullable()->change();
        });
    }
};
