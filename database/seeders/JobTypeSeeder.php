<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobTypeSeeder extends Seeder
{
    public function run(): void
    {
        JobType::query()->update(['is_active' => false]);

        $types = [
            'Permanent',
            'Contract',
            'Temporary',
            'Rotational',
            'Offshore Rotation',
            'Voyage Contract',
            'Cadetship',
            'Internship',
            'Consulting',
            'Full Time',
            'Part Time',
        ];

        foreach ($types as $index => $name) {
            JobType::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
