<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->json('education')->nullable();
            $table->json('experience')->nullable();
            $table->json('awards')->nullable();
            $table->json('languages')->nullable();
            $table->string('education_level')->nullable();
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->enum('salary_type', ['hourly', 'monthly', 'yearly'])->nullable();
            $table->string('website')->nullable();
            $table->string('video_url')->nullable();
            $table->json('social_links')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('allow_search')->default(true);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
