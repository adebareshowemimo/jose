<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Boost packages were a hardcoded array in CandidateBoostController, with the
 * durations repeated in the validation rule. This moves them into data so an
 * admin can change prices, add a tier, or run a promo without a deploy.
 *
 * The seed below reproduces the previous hardcoded tiers exactly, so the
 * public boost page looks identical the moment this runs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boost_packages', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('tagline')->nullable();
            $table->unsignedInteger('days');
            $table->decimal('price', 10, 2)->default(0);

            // Per-tier perks. Only flags the app actually reads should be set;
            // a perk nothing honours is worse than no perk at all.
            $table->json('perks')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        // Preserve the previous behaviour: the same three tiers, same prices,
        // same order. Prices match the old fallbacks in packages().
        $now = now();
        DB::table('boost_packages')->insert([
            [
                'label' => 'Quick boost',
                'tagline' => 'Try it for a week',
                'days' => 7,
                'price' => 9,
                'perks' => json_encode(['top_of_search' => true]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'label' => 'Standard',
                'tagline' => 'Best value · 30 days top placement',
                'days' => 30,
                'price' => 29,
                'perks' => json_encode(['top_of_search' => true, 'most_popular' => true]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'label' => 'Quarter',
                'tagline' => 'Stay featured for 3 months',
                'days' => 90,
                'price' => 69,
                'perks' => json_encode(['top_of_search' => true]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('boost_packages');
    }
};
