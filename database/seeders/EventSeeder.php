<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Support\JclProfileContent;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $profile = JclProfileContent::company();

        foreach ($profile['events'] as $index => $event) {
            Event::updateOrCreate(
                ['title' => $event['title'], 'category' => 'hosted'],
                [
                    'type' => $event['type'],
                    'display_date' => $event['date'],
                    'location' => $event['location'],
                    'description' => $event['description'],
                    'status' => $event['status'] ?? 'upcoming',
                    'sort_order' => $index + 1,
                    'is_featured' => $index === 0,
                ]
            );
        }

        foreach ($profile['industry_events'] as $index => $event) {
            Event::updateOrCreate(
                ['title' => $event['title'], 'category' => 'industry'],
                [
                    'type' => 'Industry Event',
                    'display_date' => $event['date'],
                    'location' => $event['location'],
                    'description' => $event['description'],
                    'status' => 'upcoming',
                    'sort_order' => $index + 1,
                    'is_featured' => false,
                ]
            );
        }
    }
}
