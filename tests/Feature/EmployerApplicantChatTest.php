<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\ChatConversation;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerApplicantChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_granted_applicant_is_added_to_employer_and_candidate_chat(): void
    {
        $employerRole = Role::firstOrCreate(['name' => 'employer']);
        $candidateRole = Role::firstOrCreate(['name' => 'candidate']);
        $admin = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'admin'])->id,
        ]);
        $employer = User::factory()->create(['role_id' => $employerRole->id]);
        $candidateUser = User::factory()->create(['role_id' => $candidateRole->id, 'name' => 'Approved Applicant']);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Approved Jobs Ltd',
            'slug' => 'approved-jobs-ltd',
        ]);
        $candidate = Candidate::create([
            'user_id' => $candidateUser->id,
            'slug' => 'approved-applicant',
        ]);
        $job = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Approved Deck Officer',
            'slug' => 'approved-deck-officer',
            'description' => 'Approved vacancy.',
            'is_approved' => true,
        ]);
        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'applied',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.applications.chat-access.grant', $application))
            ->assertRedirect();
        $this->assertNotNull($application->fresh()->employer_chat_access_granted_at);

        $this->actingAs($employer)->get(route('employer.chat'))
            ->assertOk()
            ->assertSee('Approved Applicant')
            ->assertSee('Approved Deck Officer');

        $conversation = ChatConversation::where('job_application_id', $application->id)->firstOrFail();
        $this->assertSame($company->id, $conversation->company_id);
        $this->assertSame($candidate->id, $conversation->candidate_id);

        $this->actingAs($candidateUser)->get(route('user.chat'))
            ->assertOk()
            ->assertSee('Approved Jobs Ltd')
            ->assertSee('Approved Deck Officer');

        $this->actingAs($admin)
            ->delete(route('admin.applications.chat-access.revoke', $application))
            ->assertRedirect();

        $this->actingAs($employer)->get(route('employer.chat'))
            ->assertOk()
            ->assertDontSee('Approved Applicant');
        $this->actingAs($employer)
            ->post(route('employer.chat.messages.store', $conversation), ['body' => 'Should be blocked'])
            ->assertForbidden();
    }

    public function test_applicant_without_admin_grant_is_not_added_to_chat(): void
    {
        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $candidateUser = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'candidate'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Pending Jobs Ltd',
            'slug' => 'pending-jobs-ltd',
        ]);
        $candidate = Candidate::create([
            'user_id' => $candidateUser->id,
            'slug' => 'pending-job-applicant',
        ]);
        $job = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Approved But Restricted Job',
            'slug' => 'approved-but-restricted-job',
            'description' => 'Approved without applicant chat access.',
            'is_approved' => true,
        ]);
        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'applied',
        ]);

        $this->actingAs($employer)->get(route('employer.chat'))->assertOk();

        $this->assertFalse(ChatConversation::where('job_application_id', $application->id)->exists());
    }
}
