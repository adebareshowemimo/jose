<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CandidatePlanSeeder extends Seeder
{
    public function run(): void
    {
        $candidateRoleId = optional(Role::where('name', 'candidate')->first())->id;

        Plan::updateOrCreate(
            ['name' => 'Candidate Premium'],
            [
                'description' => 'Always-featured profile, priority support, and profile analytics for serious candidates.',
                'monthly_price' => 12.00,
                'annual_price' => 99.00,
                'max_job_posts' => 0,
                'max_featured_jobs' => 0,
                'resume_access' => false,
                'role_id' => $candidateRoleId,
                'is_recommended' => true,
                'is_active' => true,
                'sort_order' => 100,
                'benefits' => [
                    'always_featured' => true,
                    'priority_support' => true,
                    'profile_analytics' => true,
                    'boost_credits_per_month' => 0,
                ],
            ]
        );
    }
}
