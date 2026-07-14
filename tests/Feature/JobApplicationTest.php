<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function candidateUser(array $candidateAttributes): array
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $candidate = Candidate::create(array_merge([
            'user_id' => $user->id,
            'slug' => 'cand-' . $user->id,
        ], $candidateAttributes));

        return [$user, $candidate];
    }

    private function completeProfileAttributes(): array
    {
        return [
            'title' => 'Second Officer',
            'bio' => 'Experienced deck officer.',
            'experience' => [[
                'position' => 'Third Officer',
                'company' => 'Blue Line',
                'start_date' => '2019-01-01',
                'is_current' => true,
            ]],
            'skills_list' => ['Navigation', 'ECDIS'],
        ];
    }

    private function job(): JobListing
    {
        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Acme Shipping',
            'slug' => 'acme-shipping',
        ]);

        return JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Engine Cadet',
            'slug' => 'engine-cadet-1',
            'description' => 'A great role.',
        ]);
    }

    public function test_candidate_with_complete_profile_can_apply_without_a_cv(): void
    {
        [$user, $candidate] = $this->candidateUser($this->completeProfileAttributes());
        $job = $this->job();

        $this->assertTrue($candidate->hasApplyReadyProfile());

        $res = $this->actingAs($user)->post(route('job.apply', $job), [
            'cover_letter' => 'Keen to join your crew.',
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');
        $this->assertDatabaseHas('job_applications', [
            'candidate_id' => $candidate->id,
            'job_listing_id' => $job->id,
            'resume_id' => null,
            'status' => 'applied',
        ]);
    }

    public function test_candidate_with_incomplete_profile_cannot_apply(): void
    {
        // Missing bio / experience / skills => not apply-ready.
        [$user, $candidate] = $this->candidateUser(['title' => 'Deck Hand']);
        $job = $this->job();

        $this->assertFalse($candidate->hasApplyReadyProfile());

        $res = $this->actingAs($user)->post(route('job.apply', $job), [
            'cover_letter' => 'Hello',
        ]);

        $res->assertSessionHas('error');
        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_job_page_renders_a_clean_apply_with_profile_banner(): void
    {
        [$user] = $this->candidateUser($this->completeProfileAttributes());

        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Acme Shipping',
            'slug' => 'acme-banner',
        ]);
        JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Engine Cadet',
            'slug' => 'engine-cadet-banner',
            'description' => 'A great role.',
            'status' => 'active',
            'is_approved' => true,
        ]);

        $res = $this->actingAs($user)->get(route('job.detail', 'engine-cadet-banner'));

        $res->assertOk();
        $res->assertSee('Apply with your profile');
        $res->assertSee('(experience, education, skills)'); // clean, interpolated list
        $res->assertDontSee('@if');    // no leaked Blade directive
        $res->assertDontSee('@endif');
    }

    public function test_candidate_with_incomplete_profile_cannot_apply_even_with_an_uploaded_cv(): void
    {
        [$user] = $this->candidateUser(['title' => 'Deck Hand']);
        $job = $this->job();
        Storage::fake('public');

        $res = $this->actingAs($user)->post(route('job.apply', $job), [
            'resume' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $res->assertSessionHas('error');
        $this->assertNull(JobApplication::first());
        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_candidate_with_complete_profile_may_still_attach_a_cv(): void
    {
        [$user] = $this->candidateUser($this->completeProfileAttributes());
        $job = $this->job();
        Storage::fake('public');

        $res = $this->actingAs($user)->post(route('job.apply', $job), [
            'resume' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $res->assertSessionHas('success');
        $this->assertNotNull(JobApplication::first()?->resume_id);
    }

    public function test_job_page_hides_the_apply_form_until_the_profile_is_complete(): void
    {
        [$user] = $this->candidateUser(['title' => 'Deck Hand']);

        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Acme Shipping',
            'slug' => 'acme-gate',
        ]);
        JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Deck Cadet',
            'slug' => 'deck-cadet-gate',
            'description' => 'A great role.',
            'status' => 'active',
            'is_approved' => true,
        ]);

        $res = $this->actingAs($user)->get(route('job.detail', 'deck-cadet-gate'));

        $res->assertOk();
        $res->assertSee('Complete your profile to apply.');
        $res->assertDontSee('Upload your CV');
        $res->assertDontSee('Submit application');
        $res->assertDontSee('to apply without uploading a CV');
    }
}
