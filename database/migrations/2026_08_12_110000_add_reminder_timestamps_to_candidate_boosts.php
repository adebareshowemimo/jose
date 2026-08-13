<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-boost reminder stamps.
 *
 * Dedup cannot key on user + template alone, as the existing reminder command
 * does: a candidate who buys a second boost must be reminded again about that
 * one. Stamping the boost row makes each reminder at-most-once per boost, and
 * keeps a re-run or an overlapping cron from double-sending.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_boosts', function (Blueprint $table) {
            $table->timestamp('expiring_reminder_sent_at')->nullable()->after('status');
            $table->timestamp('expired_notice_sent_at')->nullable()->after('expiring_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_boosts', function (Blueprint $table) {
            $table->dropColumn(['expiring_reminder_sent_at', 'expired_notice_sent_at']);
        });
    }
};
