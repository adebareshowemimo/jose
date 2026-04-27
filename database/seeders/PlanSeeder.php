<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $employerRole = Role::where('name', 'employer')->first();

        $plans = [
            [
                'name'              => 'Free',
                'description'       => 'Get started at no cost. Ideal for small businesses exploring the platform.',
                'monthly_price'     => 0.00,
                'annual_price'      => 0.00,
                'max_job_posts'     => 2,
                'max_featured_jobs' => 0,
                'resume_access'     => false,
                'is_active'         => true,
                'is_recommended'    => false,
                'sort_order'        => 1,
                'role_id'           => $employerRole?->id,
            ],
            [
                'name'              => 'Starter',
                'description'       => 'Perfect for growing businesses. Post more jobs and access basic features.',
                'monthly_price'     => 29.00,
                'annual_price'      => 290.00,
                'max_job_posts'     => 10,
                'max_featured_jobs' => 2,
                'resume_access'     => false,
                'is_active'         => true,
                'is_recommended'    => false,
                'sort_order'        => 2,
                'role_id'           => $employerRole?->id,
            ],
            [
                'name'              => 'Professional',
                'description'       => 'The most popular plan for active hiring teams. Full feature access included.',
                'monthly_price'     => 79.00,
                'annual_price'      => 790.00,
                'max_job_posts'     => 50,
                'max_featured_jobs' => 10,
                'resume_access'     => true,
                'is_active'         => true,
                'is_recommended'    => true,
                'sort_order'        => 3,
                'role_id'           => $employerRole?->id,
            ],
            [
                'name'              => 'Enterprise',
                'description'       => 'Unlimited posting, full resume database, and dedicated account support.',
                'monthly_price'     => 199.00,
                'annual_price'      => 1990.00,
                'max_job_posts'     => 9999,
                'max_featured_jobs' => 9999,
                'resume_access'     => true,
                'is_active'         => true,
                'is_recommended'    => false,
                'sort_order'        => 4,
                'role_id'           => $employerRole?->id,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
