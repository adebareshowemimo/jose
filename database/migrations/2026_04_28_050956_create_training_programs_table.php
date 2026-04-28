<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('type')->default('training')->index(); // training | apprenticeship
            $table->string('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('duration')->nullable();
            $table->string('level')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('enrol_deadline')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};
