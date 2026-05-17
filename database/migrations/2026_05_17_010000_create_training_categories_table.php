<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('short_description')->nullable();
            $table->longText('intro_html')->nullable();
            $table->json('bullet_points')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $now = now();

        DB::table('training_categories')->insert([
            [
                'name' => 'Soft Skills Training',
                'slug' => 'soft-skills',
                'icon' => 'lucide:users',
                'short_description' => 'Communication, leadership, and workplace effectiveness programs for maritime and energy professionals.',
                'intro_html' => '<p>Our soft skills programs develop the interpersonal and professional competencies that make maritime and energy professionals effective in diverse, high-pressure environments.</p>',
                'bullet_points' => json_encode([
                    'Communication & presentation skills',
                    'Team leadership & crew resource management',
                    'Conflict resolution & negotiation',
                    'Professional etiquette & workplace conduct',
                    'Time management & personal effectiveness',
                    'Customer service & stakeholder engagement',
                ]),
                'hero_image_path' => null,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Technical & Non-Technical Skills',
                'slug' => 'technical',
                'icon' => 'lucide:graduation-cap',
                'short_description' => 'Industry-aligned technical and operational skills training for maritime and energy sector professionals.',
                'intro_html' => "<p>JCL's technical and non-technical training programs prepare maritime and energy professionals for the rigors of international operations — from statutory certificates to advanced operational competencies.</p>",
                'bullet_points' => json_encode([
                    'STCW Basic Safety Training (BST)',
                    'Offshore Safety & Emergency Response (BOSIET)',
                    'NEBOSH International General Certificate',
                    'Port & Terminal Operations Management',
                    'Maritime Leadership & Crew Resource Management',
                    'Energy Sector Workforce Readiness',
                ]),
                'hero_image_path' => null,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('training_categories');
    }
};
