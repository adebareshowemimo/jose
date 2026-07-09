<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestCandidate;
use App\Models\Resume;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function candidateWithCertificate(): User
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(['name' => 'Ada Cert', 'role_id' => $role->id]);

        $candidate = Candidate::create([
            'user_id' => $user->id,
            'slug' => 'ada-cert',
            'bio' => 'Seasoned deck officer with global experience.',
            'experience' => [[
                'position' => 'Second Officer',
                'company' => 'Blue Line Shipping',
                'start_date' => '2019-01-01',
                'is_current' => true,
            ]],
            'awards' => [[
                'id' => 'award-1',
                'name' => 'STCW Basic Safety',
                'issuer' => 'Maritime & Coastguard Agency',
                'issue_date' => '2022-01-01',
                'expiry_date' => null,
                'no_expiry' => true,
                'credential_id' => 'CID-9',
                'certificate_path' => 'certificates/99/proof.pdf',
                'certificate_name' => 'proof.pdf',
            ]],
        ]);

        Resume::create([
            'candidate_id' => $candidate->id,
            'title' => 'Master Mariner CV',
            'file_path' => 'resumes/99/master-cv.pdf',
            'is_default' => true,
        ]);

        return $user;
    }

    public function test_admin_user_page_shows_certificate_file_link(): void
    {
        $candidateUser = $this->candidateWithCertificate();
        $admin = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'admin'])->id,
        ]);

        $res = $this->actingAs($admin)->get(route('admin.users.show', $candidateUser));

        $res->assertOk();
        // Full candidate profile
        $res->assertSee('Candidate Profile');
        $res->assertSee('Seasoned deck officer with global experience.'); // bio
        $res->assertSee('Second Officer'); // work experience
        // CV
        $res->assertSee('Master Mariner CV');
        $res->assertSee('resumes/99/master-cv.pdf'); // the CV download url
        // Certifications + certificate file
        $res->assertSee('Certifications');
        $res->assertSee('STCW Basic Safety');
        $res->assertSee('No expiry');
        $res->assertSee('View file');
        $res->assertSee('certificates/99/proof.pdf'); // the certificate href
    }

    public function test_employer_delivered_candidate_page_shows_certificate_link(): void
    {
        $candidateUser = $this->candidateWithCertificate();
        $candidate = $candidateUser->candidate;

        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Acme Shipping',
            'slug' => 'acme-shipping',
        ]);

        $request = RecruitmentRequest::create([
            'company_id' => $company->id,
            'requested_by_user_id' => $employer->id,
            'service_type' => 'cv_sourcing',
            'job_title' => 'Engine Cadet',
            'description' => 'Need an engine cadet.',
            'cv_count' => 1,
            'status' => 'candidates_delivered',
        ]);
        RecruitmentRequestCandidate::create([
            'recruitment_request_id' => $request->id,
            'candidate_id' => $candidate->id,
            'delivered_at' => now(),
        ]);

        $res = $this->actingAs($employer)->get(route('employer.candidates.show', $candidate));

        $res->assertOk();
        $res->assertSee('Certifications');
        $res->assertSee('STCW Basic Safety');
        $res->assertSee('View certificate');
        $res->assertSee('certificates/99/proof.pdf'); // the asset() href
    }

    public function test_admin_application_page_shows_view_button_cv_and_details(): void
    {
        $candidateUser = $this->candidateWithCertificate();
        $candidate = $candidateUser->candidate;
        $resume = $candidate->resumes->first();

        $admin = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'admin'])->id,
        ]);

        $employer = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'employer'])->id,
        ]);
        $company = Company::create([
            'owner_id' => $employer->id,
            'name' => 'Acme Shipping',
            'slug' => 'acme-shipping-app',
        ]);
        $job = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $employer->id,
            'title' => 'Engine Cadet',
            'slug' => 'engine-cadet-app',
            'description' => 'A great role.',
        ]);
        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'cover_letter' => 'I am very keen to join your crew.',
            'status' => 'applied',
        ]);

        // The list page has a View button linking to the detail page.
        $index = $this->actingAs($admin)->get(route('admin.applications'));
        $index->assertOk();
        $index->assertSee(route('admin.applications.show', $application), false);

        // The detail page shows the application, the submitted CV, and full profile.
        $res = $this->actingAs($admin)->get(route('admin.applications.show', $application));
        $res->assertOk();
        $res->assertSee('Ada Cert');                          // candidate
        $res->assertSee('Engine Cadet');                      // job title
        $res->assertSee('I am very keen to join your crew.');  // cover letter
        $res->assertSee('Master Mariner CV');                 // submitted CV
        $res->assertSee('resumes/99/master-cv.pdf');          // CV download url
        $res->assertSee('Candidate Profile');                 // profile partial
        $res->assertSee('STCW Basic Safety');                 // certification
        $res->assertSee('View file');                         // certificate file link
    }
}
