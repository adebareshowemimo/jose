<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestCandidate;
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

        Candidate::create([
            'user_id' => $user->id,
            'slug' => 'ada-cert',
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
        $res->assertSee('Certifications');
        $res->assertSee('STCW Basic Safety');
        $res->assertSee('No expiry');
        $res->assertSee('View file');
        $res->assertSee('certificates/99/proof.pdf'); // the asset() href
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
}
