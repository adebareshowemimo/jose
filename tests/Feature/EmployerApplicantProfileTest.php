<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerApplicantProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_view_full_profile_for_an_applicant_to_their_job(): void
    {
        $employerRole = Role::firstOrCreate(['name' => 'employer']);
        $candidateRole = Role::firstOrCreate(['name' => 'candidate']);
        $employer = User::factory()->create(['role_id' => $employerRole->id]);
        $otherEmployer = User::factory()->create(['role_id' => $employerRole->id]);
        $candidateUser = User::factory()->create([
            'name' => 'Applicant Profile',
            'role_id' => $candidateRole->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Applicant Company',
            'slug' => 'applicant-company',
        ]);
        Company::create([
            'owner_id' => $otherEmployer->id,
            'name' => 'Other Company',
            'slug' => 'other-applicant-company',
        ]);
        $candidate = Candidate::create([
            'user_id' => $candidateUser->id,
            'slug' => 'applicant-profile',
            'title' => 'Marine Engineer',
            'bio' => 'Experienced marine engineering professional.',
            'experience' => [['position' => 'Second Engineer', 'company' => 'Ocean Line']],
        ]);
        $job = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Chief Engineer',
            'slug' => 'chief-engineer-applicant',
            'description' => 'Engineering vacancy.',
        ]);
        JobApplication::create([
            'job_listing_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'shortlisted',
            'cover_letter' => 'My application cover letter.',
        ]);

        $response = $this->actingAs($employer)->get(route('employer.candidates.show', $candidate));

        $response->assertOk();
        $response->assertSee('Applicant Profile');
        $response->assertSee('Chief Engineer');
        $response->assertSee('My application cover letter.');
        $response->assertSee('Second Engineer');

        $this->actingAs($otherEmployer)
            ->get(route('employer.candidates.show', $candidate))
            ->assertRedirect(route('employer.applicants'));
    }
}
