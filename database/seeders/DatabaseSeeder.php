<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // roles first (users depend on them)
            JobTypeSeeder::class,
            IndustrySeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            SkillSeeder::class,
            PlanSeeder::class,      // depends on roles
            UserSeeder::class,      // depends on roles
            EventSeeder::class,
            NewsArticleSeeder::class,
            EmailTemplateSeeder::class,
            RecruitmentEmailTemplateSeeder::class,
            PaymentEmailTemplateSeeder::class,
            NewsletterEmailTemplateSeeder::class,
            MonetizationEmailTemplateSeeder::class,
            CandidatePlanSeeder::class,
            TrainingProgramSeeder::class,
        ]);
    }
}
