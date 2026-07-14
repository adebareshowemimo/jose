<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\RecruitmentRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'admin'])->id,
        ]);
    }

    private function employerWithCompany(): array
    {
        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Ocean Staffing',
            'slug' => 'ocean-staffing-'.$employer->id,
        ]);

        return [$employer, $company];
    }

    public function test_admin_can_soft_delete_an_application(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $candidateUser = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'candidate'])->id,
        ]);
        $candidate = Candidate::create([
            'user_id' => $candidateUser->id,
            'slug' => 'delete-applicant-'.$candidateUser->id,
        ]);
        $job = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Deck Officer',
            'slug' => 'delete-deck-officer-'.$employer->id,
            'description' => 'Deck officer vacancy.',
        ]);
        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'applied',
        ]);

        $response = $this->actingAs($this->admin())
            ->delete(route('admin.applications.destroy', $application));

        $response->assertRedirect(route('admin.applications'));
        $this->assertSoftDeleted($application);
        $this->assertNull(JobApplication::find($application->id));
    }

    public function test_admin_can_soft_delete_a_recruitment_request(): void
    {
        [$employer, $company] = $this->employerWithCompany();
        $recruitment = RecruitmentRequest::create([
            'company_id' => $company->id,
            'requested_by_user_id' => $employer->id,
            'service_type' => 'cv_sourcing',
            'cv_count' => 2,
            'job_title' => 'Marine Engineer',
            'description' => 'Source qualified marine engineers.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin())
            ->delete(route('admin.recruitment-requests.destroy', $recruitment));

        $response->assertRedirect(route('admin.recruitment-requests.index'));
        $this->assertSoftDeleted($recruitment);
        $this->assertNull(RecruitmentRequest::find($recruitment->id));
    }
}
