<?php

namespace Tests\Feature;

use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\CandidateBoost;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostSubscriberAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function candidate(string $name = 'Ada Boost'): Candidate
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(['role_id' => $role->id, 'name' => $name]);

        return Candidate::create(['user_id' => $user->id, 'slug' => 'cand-'.$user->id]);
    }

    private function boost(Candidate $candidate, array $attrs = []): CandidateBoost
    {
        return CandidateBoost::create(array_merge([
            'candidate_id' => $candidate->id,
            'boost_package_id' => BoostPackage::first()->id,
            'days' => 30,
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(25),
            'status' => CandidateBoost::STATUS_ACTIVE,
            'price' => 29,
            'currency' => 'NGN',
        ], $attrs));
    }

    public function test_the_list_renders(): void
    {
        $candidate = $this->candidate();
        $this->boost($candidate);

        $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.index'))
            ->assertOk()
            ->assertSee('Ada Boost');
    }

    public function test_the_detail_page_renders(): void
    {
        $boost = $this->boost($this->candidate());

        $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.show', $boost))
            ->assertOk()
            ->assertSee('Ada Boost');
    }

    /**
     * The active filter is date-derived, so a lapsed row the hourly sweep has
     * not yet reached must not be listed as active.
     */
    public function test_active_filter_excludes_a_lapsed_row_not_yet_swept(): void
    {
        $stale = $this->boost($this->candidate('Stale Sam'), [
            'ends_at' => now()->subDay(),
            'status' => CandidateBoost::STATUS_ACTIVE,
        ]);
        $this->boost($this->candidate('Running Rita'));

        $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee('Running Rita')
            ->assertDontSee('Stale Sam');
    }

    public function test_expiring_filter_finds_boosts_inside_the_window(): void
    {
        $this->boost($this->candidate('Soon Sue'), ['ends_at' => now()->addDays(3)]);
        $this->boost($this->candidate('Later Lee'), ['ends_at' => now()->addDays(60)]);

        $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.index', ['status' => 'expiring']))
            ->assertOk()
            ->assertSee('Soon Sue')
            ->assertDontSee('Later Lee');
    }

    public function test_search_matches_email(): void
    {
        $candidate = $this->candidate('Findable Fay');
        $this->boost($candidate);
        $this->boost($this->candidate('Hidden Hal'));

        $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.index', ['q' => $candidate->user->email]))
            ->assertOk()
            ->assertSee('Findable Fay')
            ->assertDontSee('Hidden Hal');
    }

    public function test_admin_can_extend_a_running_boost(): void
    {
        $candidate = $this->candidate();
        $candidate->update(['featured_until' => now()->addDays(25)]);
        $boost = $this->boost($candidate);

        $originalEnd = $boost->ends_at;

        $this->actingAs($this->admin())
            ->post(route('admin.boosts.subscribers.extend', $boost), ['days' => 10])
            ->assertRedirect();

        $this->assertSame(
            $originalEnd->copy()->addDays(10)->toDateString(),
            $boost->fresh()->ends_at->toDateString()
        );

        // Placement must follow the extension.
        $this->assertSame(
            $originalEnd->copy()->addDays(10)->toDateString(),
            $candidate->fresh()->featured_until->toDateString()
        );
    }

    public function test_extending_a_lapsed_boost_runs_from_today_not_the_past(): void
    {
        $boost = $this->boost($this->candidate(), [
            'ends_at' => now()->subDays(30),
            'status' => CandidateBoost::STATUS_EXPIRED,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.boosts.subscribers.extend', $boost), ['days' => 5]);

        $this->assertSame(
            now()->addDays(5)->toDateString(),
            $boost->fresh()->ends_at->toDateString()
        );
        $this->assertSame(CandidateBoost::STATUS_ACTIVE, $boost->fresh()->status);
    }

    public function test_cancelling_leaves_paid_placement_by_default(): void
    {
        $candidate = $this->candidate();
        $candidate->update(['featured_until' => now()->addDays(25)]);
        $boost = $this->boost($candidate);

        $this->actingAs($this->admin())
            ->post(route('admin.boosts.subscribers.cancel', $boost), ['status' => 'refunded']);

        $this->assertSame(CandidateBoost::STATUS_REFUNDED, $boost->fresh()->status);
        $this->assertNotNull($candidate->fresh()->featured_until);
    }

    public function test_revoking_placement_clears_featured_until(): void
    {
        $candidate = $this->candidate();
        $candidate->update(['featured_until' => now()->addDays(25)]);
        $boost = $this->boost($candidate);

        $this->actingAs($this->admin())
            ->post(route('admin.boosts.subscribers.cancel', $boost), [
                'status' => 'refunded',
                'revoke_placement' => 1,
            ]);

        $this->assertNull($candidate->fresh()->featured_until);
    }

    /**
     * Revoking one boost must not strip placement that a DIFFERENT active
     * boost has already been paid for.
     */
    public function test_revoking_one_of_two_boosts_keeps_the_others_placement(): void
    {
        $candidate = $this->candidate();
        $candidate->update(['featured_until' => now()->addDays(60)]);

        $first = $this->boost($candidate, ['ends_at' => now()->addDays(10)]);
        $second = $this->boost($candidate, ['ends_at' => now()->addDays(60)]);

        $this->actingAs($this->admin())
            ->post(route('admin.boosts.subscribers.cancel', $first), [
                'status' => 'refunded',
                'revoke_placement' => 1,
            ]);

        $this->assertSame(
            $second->ends_at->toDateString(),
            $candidate->fresh()->featured_until->toDateString()
        );
    }

    public function test_csv_export_streams_the_filtered_rows(): void
    {
        $this->boost($this->candidate('Export Eve'));

        $response = $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Export Eve', $csv);
        $this->assertStringContainsString('Boost ID', $csv);
    }

    public function test_revenue_excludes_refunded_boosts(): void
    {
        $this->boost($this->candidate('Paid Pat'), ['price' => 100]);
        $this->boost($this->candidate('Refunded Ray'), [
            'price' => 500,
            'status' => CandidateBoost::STATUS_REFUNDED,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.boosts.subscribers.index'))
            ->assertOk()
            ->assertSee(money(100))
            ->assertDontSee(money(600));
    }
}
