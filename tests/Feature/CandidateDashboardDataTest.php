<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateProfileView;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateDashboardDataTest extends TestCase
{
    use RefreshDatabase;

    private function candidateUser(): User
    {
        $role = Role::create(['name' => 'candidate']);

        $user = User::factory()->create([
            'name' => 'Ada Lovelace',
            'role_id' => $role->id,
        ]);

        Candidate::create([
            'user_id' => $user->id,
            'slug' => 'ada-lovelace',
        ]);

        return $user;
    }

    public function test_profile_view_count_reflects_recorded_views(): void
    {
        $user = $this->candidateUser();
        $candidate = $user->candidate;

        // Three views this month from three different employers.
        foreach (range(1, 3) as $i) {
            $viewer = User::factory()->create();
            CandidateProfileView::create([
                'candidate_id' => $candidate->id,
                'viewer_user_id' => $viewer->id,
                'source' => 'employer',
            ]);
        }

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertSeeText('3 views in the last 6 months');
    }

    public function test_record_ignores_self_views_and_dedupes_per_day(): void
    {
        $user = $this->candidateUser();
        $candidate = $user->candidate;
        $employer = User::factory()->create();

        // Self view should not count.
        CandidateProfileView::record($candidate, $user, 'public');
        $this->assertSame(0, $candidate->profileViews()->count());

        // First employer view counts; a same-day repeat does not.
        CandidateProfileView::record($candidate, $employer, 'employer');
        CandidateProfileView::record($candidate, $employer, 'employer');
        $this->assertSame(1, $candidate->profileViews()->count());
    }
}
