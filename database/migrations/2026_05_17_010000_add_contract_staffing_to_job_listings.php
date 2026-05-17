<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->boolean('is_contract_staffing')->default(false)->index()->after('is_approved');
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->change();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropIndex(['is_contract_staffing']);
            $table->dropColumn('is_contract_staffing');
        });
    }
};
