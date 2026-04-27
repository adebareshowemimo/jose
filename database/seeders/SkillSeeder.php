<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Programming Languages
            'PHP', 'Python', 'JavaScript', 'TypeScript', 'Java', 'C#', 'C++',
            'Go', 'Rust', 'Swift', 'Kotlin', 'Ruby', 'Scala', 'R',

            // Web Frameworks
            'Laravel', 'Symfony', 'Django', 'Flask', 'FastAPI',
            'React', 'Vue.js', 'Angular', 'Next.js', 'Nuxt.js',
            'Node.js', 'Express.js', 'Spring Boot', 'ASP.NET',

            // Mobile
            'React Native', 'Flutter', 'iOS Development', 'Android Development',

            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite',
            'Elasticsearch', 'DynamoDB', 'Firebase',

            // Cloud & DevOps
            'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes',
            'CI/CD', 'Terraform', 'Ansible', 'Linux', 'Git',

            // Data & AI
            'Machine Learning', 'Deep Learning', 'TensorFlow', 'PyTorch',
            'Data Analysis', 'Tableau', 'Power BI', 'Excel', 'SQL',

            // Design
            'Figma', 'Adobe XD', 'Photoshop', 'Illustrator', 'UI/UX Design',

            // Marketing
            'SEO', 'Google Ads', 'Facebook Ads', 'Content Writing',
            'Email Marketing', 'Copywriting', 'Social Media Marketing',

            // Finance & Business
            'Accounting', 'Financial Modeling', 'QuickBooks', 'SAP',
            'Business Analysis', 'Project Management', 'Scrum', 'Agile',

            // Soft Skills
            'Communication', 'Leadership', 'Problem Solving',
            'Team Collaboration', 'Time Management', 'Critical Thinking',
        ];

        foreach ($skills as $name) {
            Skill::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
