<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->foreignId('job_application_id')
                ->nullable()
                ->after('recruitment_request_candidate_id')
                ->constrained('job_applications')
                ->nullOnDelete();
            $table->unique(['type', 'job_application_id']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropUnique(['type', 'job_application_id']);
            $table->dropConstrainedForeignId('job_application_id');
        });
    }
};
