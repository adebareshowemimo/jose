<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records which package a boost was bought from, so the admin screens can
 * group and report by tier.
 *
 * Nullable and nullOnDelete on purpose: the price paid is copied onto the
 * order and the boost row, never read back through this relation. Deleting a
 * retired package must never orphan or rewrite a historical sale.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_boosts', function (Blueprint $table) {
            $table->foreignId('boost_package_id')
                ->nullable()
                ->after('order_id')
                ->constrained('boost_packages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidate_boosts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('boost_package_id');
        });
    }
};
