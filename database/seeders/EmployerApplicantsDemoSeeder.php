<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Location;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployerApplicantsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::whereHas('owner', fn ($owner) => $owner
                ->where('email', 'employer@jobportal.com'))
            ->first();

        if (! $company) {
            $this->command?->warn('Demo Employer company was not found; no demo applicants were added.');
            return;
        }

        $job = JobListing::firstOrCreate(
            ['company_id' => $company->id, 'slug' => 'demo-marine-operations-officer'],
            [
                'posted_by' => $company->owner_id,
                'title' => 'Marine Operations Officer',
                'description' => 'Coordinate vessel operations, safety compliance, and offshore personnel.',
                'apply_method' => 'internal',
                'status' => 'active',
                'is_approved' => true,
                'vacancies' => 2,
            ]
        );

        $candidateRole = Role::firstOrCreate(['name' => 'candidate']);
        $location = Location::where('is_active', true)->first();

        $applicants = [
            ['name' => 'Amara Okafor', 'title' => 'Marine Engineer', 'years' => 7, 'status' => 'applied', 'skills' => ['Marine Engineering', 'Safety Management']],
            ['name' => 'Kwame Mensah', 'title' => 'Deck Officer', 'years' => 5, 'status' => 'reviewed', 'skills' => ['Navigation', 'Team Leadership']],
            ['name' => 'Fatima Bello', 'title' => 'HSE Officer', 'years' => 6, 'status' => 'shortlisted', 'skills' => ['Risk Assessment', 'Safety Management']],
            ['name' => 'Daniel Boateng', 'title' => 'Electrical Technical Officer', 'years' => 4, 'status' => 'interviewed', 'skills' => ['Electrical Systems', 'Troubleshooting']],
            ['name' => 'Grace Ekanem', 'title' => 'Offshore Operations Coordinator', 'years' => 8, 'status' => 'rejected', 'skills' => ['Offshore Operations', 'Project Management']],
            ['name' => 'Samuel Adeyemi', 'title' => 'Able Seaman', 'years' => 3, 'status' => 'applied', 'skills' => ['Deck Operations', 'First Aid']],
        ];

        foreach ($applicants as $index => $data) {
            $email = 'demo.applicant.'.($index + 1).'@joseoceanjobs.test';
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'],
                    'role_id' => $candidateRole->id,
                    'password' => Hash::make('DemoApplicant123!'),
                    'email_verified_at' => now(),
                    'is_verified' => true,
                    'status' => 'active',
                ]
            );

            $candidate = Candidate::withTrashed()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => Str::slug($data['name']).'-demo',
                    'title' => $data['title'],
                    'bio' => "Experienced {$data['title']} available for maritime and offshore opportunities.",
                    'experience_years' => $data['years'],
                    'location_id' => $location?->id,
                    'allow_search' => true,
                    'is_available' => true,
                    'deleted_at' => null,
                ]
            );

            $skillIds = collect($data['skills'])->map(function (string $name) {
                return Skill::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'is_active' => true]
                )->id;
            });
            $candidate->skills()->sync($skillIds);

            $application = JobApplication::withTrashed()->updateOrCreate(
                ['job_listing_id' => $job->id, 'candidate_id' => $candidate->id],
                [
                    'status' => $data['status'],
                    'cover_letter' => "I am interested in the {$job->title} opportunity and believe my {$data['years']} years of experience would add value to your team.",
                    'employer_viewed_at' => null,
                    'deleted_at' => null,
                ]
            );
            $application->timestamps = false;
            $application->created_at = now()->subDays($index + 1);
            $application->save();
        }

        $this->command?->info(count($applicants).' demo applicants added for '.$job->company->name.'.');
    }
}
