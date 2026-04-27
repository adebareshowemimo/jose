<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole     = Role::where('name', 'admin')->first();
        $employerRole  = Role::where('name', 'employer')->first();
        $candidateRole = Role::where('name', 'candidate')->first();

        // Super admin
        User::firstOrCreate(
            ['email' => 'admin@jobportal.com'],
            [
                'name'        => 'Admin User',
                'password'    => Hash::make('password'),
                'role_id'     => $adminRole?->id,
                'is_verified' => true,
                'status'      => 'active',
            ]
        );

        // Sample employer
        User::firstOrCreate(
            ['email' => 'employer@jobportal.com'],
            [
                'name'        => 'Demo Employer',
                'password'    => Hash::make('password'),
                'role_id'     => $employerRole?->id,
                'is_verified' => true,
                'status'      => 'active',
            ]
        );

        // Sample candidate
        User::firstOrCreate(
            ['email' => 'candidate@jobportal.com'],
            [
                'name'        => 'Demo Candidate',
                'password'    => Hash::make('password'),
                'role_id'     => $candidateRole?->id,
                'is_verified' => true,
                'status'      => 'active',
            ]
        );
    }
}
