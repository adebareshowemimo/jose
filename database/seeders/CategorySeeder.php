<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::query()->update(['is_active' => false]);

        $categories = [
            [
                'name' => 'Deck & Navigation',
                'icon' => 'compass',
                'children' => [
                    'Master / Captain', 'Chief Officer', 'Second Officer',
                    'Third Officer', 'Able Seafarer Deck', 'Dynamic Positioning Officer',
                ],
            ],
            [
                'name' => 'Marine Engineering',
                'icon' => 'wrench',
                'children' => [
                    'Chief Engineer', 'Second Engineer', 'Third Engineer',
                    'Fourth Engineer', 'ETO / Electrical Officer', 'Motorman',
                ],
            ],
            [
                'name' => 'Ratings & Crew',
                'icon' => 'users',
                'children' => [
                    'Bosun', 'Ordinary Seaman', 'Able Seaman', 'Oiler',
                    'Cook / Steward', 'Cadet / Trainee',
                ],
            ],
            [
                'name' => 'Port & Terminal Operations',
                'icon' => 'anchor',
                'children' => [
                    'Port Operations', 'Terminal Operations', 'Cargo Handling',
                    'Stevedoring', 'Harbour Master', 'Marine Pilotage',
                ],
            ],
            [
                'name' => 'Logistics & Supply Chain',
                'icon' => 'truck',
                'children' => [
                    'Freight Forwarding', 'Customs Clearing', 'Warehouse Operations',
                    'Fleet Management', 'Procurement', 'Supply Chain Coordination',
                ],
            ],
            [
                'name' => 'Offshore & Energy',
                'icon' => 'globe',
                'children' => [
                    'Offshore Support Vessel', 'Rig Operations', 'Subsea Operations',
                    'Marine Energy', 'Oil & Gas Logistics', 'Renewable Energy Marine',
                ],
            ],
            [
                'name' => 'Marine Safety & Compliance',
                'icon' => 'shield-check',
                'children' => [
                    'HSE / QHSE', 'Marine Surveying', 'ISM / ISPS Compliance',
                    'Vetting Inspection', 'Classification Society', 'Flag State Compliance',
                ],
            ],
            [
                'name' => 'Maritime Administration',
                'icon' => 'clipboard-list',
                'children' => [
                    'Crewing', 'Manning Agency', 'Shipping Documentation',
                    'Chartering', 'Claims & Insurance', 'Marine HR',
                ],
            ],
            [
                'name' => 'Marine Procurement & Chandelling',
                'icon' => 'package',
                'children' => [
                    'Ship Chandelling', 'Marine Procurement', 'Spare Parts',
                    'Bunker Supply', 'Vessel Stores', 'Technical Purchasing',
                ],
            ],
            [
                'name' => 'Training & Certification',
                'icon' => 'book-open',
                'children' => [
                    'STCW Training', 'Maritime Instructor', 'Simulator Training',
                    'Safety Training', 'Technical Training', 'Cadet Development',
                ],
            ],
        ];

        foreach ($categories as $i => $catData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name'       => $catData['name'],
                    'icon'       => $catData['icon'],
                    'is_active'  => true,
                    'sort_order' => $i + 1,
                ]
            );

            foreach (($catData['children'] ?? []) as $j => $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($catData['name'] . '-' . $childName)],
                    [
                        'name'       => $childName,
                        'parent_id'  => $parent->id,
                        'is_active'  => true,
                        'sort_order' => $j + 1,
                        'icon'       => null,
                    ]
                );
            }
        }
    }
}
