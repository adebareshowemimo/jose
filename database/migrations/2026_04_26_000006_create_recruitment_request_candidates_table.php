<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_request_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();

            $table->string('external_name')->nullable();
            $table->string('external_email')->nullable();
            $table->string('external_phone')->nullable();
            $table->string('external_cv_path')->nullable();

            $table->text('summary')->nullable();
            $table->enum('employer_decision', [
                'pending', 'shortlisted', 'contacted', 'rejected', 'hired',
            ])->default('pending');
            $table->text('employer_feedback')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['recruitment_request_id', 'employer_decision'], 'rrc_request_decision_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_request_candidates');
    }
};
