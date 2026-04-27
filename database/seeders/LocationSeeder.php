<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::query()->update(['is_active' => false]);

        $countries = [
            'Angola', 'Australia', 'Belgium', 'Brazil', 'Canada', 'China',
            'Denmark', 'Egypt', 'France', 'Germany', 'Ghana', 'Greece',
            'India', 'Indonesia', 'Italy', 'Japan', 'Kenya', 'Malaysia',
            'Netherlands', 'Nigeria', 'Norway', 'Philippines', 'Qatar',
            'Saudi Arabia', 'Singapore', 'South Africa', 'South Korea',
            'Spain', 'Turkey', 'UAE', 'United Kingdom', 'United States',
        ];

        foreach ($countries as $index => $country) {
            Location::updateOrCreate(
                ['slug' => Str::slug($country)],
                [
                    'name' => $country,
                    'type' => 'country',
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
