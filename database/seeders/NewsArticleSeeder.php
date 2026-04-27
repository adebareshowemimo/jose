<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Seeder;

class NewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'slug' => 'offshore-safety-updates-2026',
                'title' => 'Offshore Safety Updates for 2026',
                'excerpt' => 'A practical summary of new compliance expectations impacting offshore crews and operators.',
                'author' => 'JCL Editorial',
                'published_at' => '2026-03-20',
                'category' => 'Safety',
                'content' => [
                    'Offshore safety requirements continue to evolve as operators respond to tighter compliance expectations, higher client scrutiny, and more complex deployment environments.',
                    'Crews should expect stronger emphasis on documented risk assessment, incident reporting, permit-to-work discipline, and recurring emergency response drills.',
                    'Employers can reduce delays by confirming that worker certifications, medical records, and safety training evidence are complete before mobilization.',
                ],
            ],
            [
                'slug' => 'global-maritime-hiring-trends',
                'title' => 'Global Maritime Hiring Trends This Quarter',
                'excerpt' => 'Demand is rising for deck officers, engineers, and dynamic positioning specialists.',
                'author' => 'Market Insights Team',
                'published_at' => '2026-03-12',
                'category' => 'Hiring',
                'content' => [
                    'Global maritime hiring remains active, with employers prioritizing qualified deck officers, marine engineers, offshore support crews, and dynamic positioning specialists.',
                    'Verified documents and current competency records are increasingly important because employers are shortening recruitment windows for urgent placements.',
                    'Candidates who maintain updated profiles, clear availability dates, and validated certificates are being matched faster across maritime and offshore roles.',
                ],
            ],
            [
                'slug' => 'stcw-certification-pathways',
                'title' => 'STCW Certification Pathways Explained',
                'excerpt' => 'Understanding the route from basic safety to advanced endorsements and deployment readiness.',
                'author' => 'Training Desk',
                'published_at' => '2026-03-03',
                'category' => 'Training',
                'content' => [
                    'STCW certification provides the foundation for safe and compliant seafaring work, starting with basic safety training and progressing into role-specific endorsements.',
                    'Professionals should understand renewal timelines, refresher requirements, and the supporting medical and identity documentation needed for deployment.',
                    'A structured certification pathway helps candidates plan training investments and gives employers greater confidence in workforce readiness.',
                ],
            ],
        ];

        foreach ($articles as $index => $article) {
            NewsArticle::updateOrCreate(
                ['slug' => $article['slug']],
                array_merge($article, [
                    'status' => 'published',
                    'sort_order' => $index + 1,
                    'is_featured' => $index === 0,
                ])
            );
        }
    }
}
