<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            ['name' => 'Technology',              'icon' => 'laptop'],
            ['name' => 'Healthcare',              'icon' => 'heart-pulse'],
            ['name' => 'Finance & Banking',       'icon' => 'bank'],
            ['name' => 'Education',               'icon' => 'graduation-cap'],
            ['name' => 'Construction',            'icon' => 'hard-hat'],
            ['name' => 'Manufacturing',           'icon' => 'factory'],
            ['name' => 'Retail & E-Commerce',     'icon' => 'shopping-cart'],
            ['name' => 'Transportation & Logistics', 'icon' => 'truck'],
            ['name' => 'Hospitality & Tourism',   'icon' => 'hotel'],
            ['name' => 'Media & Entertainment',   'icon' => 'film'],
            ['name' => 'Real Estate',             'icon' => 'building'],
            ['name' => 'Legal',                   'icon' => 'scale'],
            ['name' => 'Marketing & Advertising', 'icon' => 'megaphone'],
            ['name' => 'Human Resources',         'icon' => 'users'],
            ['name' => 'Non-Profit',              'icon' => 'heart'],
            ['name' => 'Government & Public Sector', 'icon' => 'landmark'],
            ['name' => 'Agriculture',             'icon' => 'seedling'],
            ['name' => 'Energy & Utilities',      'icon' => 'bolt'],
            ['name' => 'Telecommunications',      'icon' => 'phone'],
            ['name' => 'Consulting',              'icon' => 'briefcase'],
        ];

        foreach ($industries as $i => $data) {
            Industry::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'       => $data['name'],
                    'icon'       => $data['icon'],
                    'is_active'  => true,
                    'sort_order' => $i + 1,
                ]
            );
        }
    }
}
