<?php

namespace Tests\Feature;

use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\CandidateBoost;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostExpiryTest extends TestCase
{
    use RefreshDatabase;

    private function candidate(): Candidate
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(['role_id' => $role->id]);

        return Candidate::create([
            'user_id' => $user->id,
            'slug' => 'expiry-'.$user->id,
        ]);
    }

    private function boost(Candidate $candidate, string $status, $endsAt): CandidateBoost
    {
        return CandidateBoost::create([
            'candidate_id' => $candidate->id,
            'days' => 30,
            'starts_at' => now()->subDays(31),
            'ends_at' => $endsAt,
            'status' => $status,
            'price' => 29,
            'currency' => 'NGN',
        ]);
    }

    public function test_lapsed_boosts_are_marked_expired(): void
    {
        $candidate = $this->candidate();
        $lapsed = $this->boost($candidate, CandidateBoost::STATUS_ACTIVE, now()->subDay());

        $this->artisan('boosts:expire')->assertSuccessful();

        $this->assertSame(CandidateBoost::STATUS_EXPIRED, $lapsed->fresh()->status);
    }

    public function test_running_boosts_are_left_alone(): void
    {
        $candidate = $this->candidate();
        $running = $this->boost($candidate, CandidateBoost::STATUS_ACTIVE, now()->addDays(10));

        $this->artisan('boosts:expire')->assertSuccessful();

        $this->assertSame(CandidateBoost::STATUS_ACTIVE, $running->fresh()->status);
    }

    public function test_dry_run_writes_nothing(): void
    {
        $candidate = $this->candidate();
        $lapsed = $this->boost($candidate, CandidateBoost::STATUS_ACTIVE, now()->subDay());

        $this->artisan('boosts:expire --dry-run')->assertSuccessful();

        $this->assertSame(CandidateBoost::STATUS_ACTIVE, $lapsed->fresh()->status);
    }

    public function test_refunded_boosts_are_not_reopened_or_touched(): void
    {
        $candidate = $this->candidate();
        $refunded = $this->boost($candidate, CandidateBoost::STATUS_REFUNDED, now()->subDay());

        $this->artisan('boosts:expire')->assertSuccessful();

        $this->assertSame(CandidateBoost::STATUS_REFUNDED, $refunded->fresh()->status);
    }

    public function test_running_it_twice_is_a_no_op(): void
    {
        $candidate = $this->candidate();
        $lapsed = $this->boost($candidate, CandidateBoost::STATUS_ACTIVE, now()->subDay());

        $this->artisan('boosts:expire')->assertSuccessful();
        $updatedAt = $lapsed->fresh()->updated_at;

        $this->artisan('boosts:expire')->assertSuccessful();

        $this->assertEquals($updatedAt, $lapsed->fresh()->updated_at);
    }

    public function test_expiry_does_not_touch_featured_until(): void
    {
        $candidate = $this->candidate();
        $candidate->update(['featured_until' => now()->addDays(5)]);
        $this->boost($candidate, CandidateBoost::STATUS_ACTIVE, now()->subDay());

        $before = $candidate->fresh()->featured_until;
        $this->artisan('boosts:expire')->assertSuccessful();

        $this->assertEquals($before, $candidate->fresh()->featured_until);
    }

    public function test_seeded_packages_match_the_previous_hardcoded_tiers(): void
    {
        $packages = BoostPackage::active()->ordered()->get();

        $this->assertCount(3, $packages);
        $this->assertSame([7, 30, 90], $packages->pluck('days')->all());
        $this->assertSame(['9.00', '29.00', '69.00'], $packages->pluck('price')->all());
    }
}
