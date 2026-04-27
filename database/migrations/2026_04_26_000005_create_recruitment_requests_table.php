<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('service_type', ['cv_sourcing', 'partial_recruitment', 'full_recruitment']);
            $table->unsignedInteger('cv_count')->default(1);

            $table->string('job_title');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('experience_level')->nullable();
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->text('description');
            $table->json('skills_list')->nullable();
            $table->date('needed_by')->nullable();
            $table->string('jd_file_path')->nullable();

            $table->enum('status', [
                'pending', 'quote_sent', 'paid', 'in_progress',
                'candidates_delivered', 'completed', 'cancelled',
            ])->default('pending');

            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to_admin_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('quoted_amount', 12, 2)->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_requests');
    }
};
