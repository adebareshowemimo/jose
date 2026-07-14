<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateCertificateUploadTest extends TestCase
{
    use RefreshDatabase;

    private function candidateUser(): User
    {
        $user = User::factory()->create([
            'role_id' => Role::firstOrCreate(['name' => 'candidate'])->id,
        ]);

        Candidate::create([
            'user_id' => $user->id,
            'slug' => 'certificate-upload-'.$user->id,
            'awards' => [],
        ]);

        return $user;
    }

    public function test_certificate_attachment_is_required_when_adding_a_certification(): void
    {
        $user = $this->candidateUser();

        $response = $this->actingAs($user)
            ->from(route('user.candidate.profile'))
            ->post(route('user.profile.certification.add'), [
                'name' => 'STCW Basic Safety',
                'issuer' => 'Maritime Authority',
            ]);

        $response->assertRedirect(route('user.candidate.profile'));
        $response->assertSessionHasErrors('certificate');
        $this->assertSame([], $user->candidate->fresh()->awards);
    }

    public function test_candidate_can_add_a_certification_with_a_pdf_attachment(): void
    {
        Storage::fake('public');
        $user = $this->candidateUser();

        $response = $this->actingAs($user)->post(route('user.profile.certification.add'), [
            'name' => 'STCW Basic Safety',
            'issuer' => 'Maritime Authority',
            'certificate' => UploadedFile::fake()->create('stcw.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasNoErrors();
        $award = $user->candidate->fresh()->awards[0];
        $this->assertSame('stcw.pdf', $award['certificate_name']);
        Storage::disk('public')->assertExists($award['certificate_path']);
    }
}
