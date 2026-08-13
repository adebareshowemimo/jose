<?php

namespace Tests\Feature;

use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\CandidateBoost;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostGatingTest extends TestCase
{
    use RefreshDatabase;

    private function set(string $key, string $value): void
    {
        app(Settings::class)->set($key, $value, 'boost');
    }

    private function candidateUser(array $userAttrs = []): User
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(array_merge(['role_id' => $role->id], $userAttrs));
        Candidate::create(['user_id' => $user->id, 'slug' => 'cand-'.$user->id]);

        return $user->fresh();
    }

    private function package(): BoostPackage
    {
        return BoostPackage::where('label', 'Standard')->first();
    }

    private function buy(User $user, ?BoostPackage $package = null)
    {
        return $this->actingAs($user)->post(route('candidate.boost.purchase'), [
            'package_id' => ($package ?? $this->package())->id,
        ]);
    }

    public function test_the_feature_switch_hides_the_page(): void
    {
        $this->set('boost.enabled', '0');

        $this->actingAs($this->candidateUser())
            ->get(route('candidate.boost.index'))
            ->assertNotFound();
    }

    public function test_the_feature_switch_blocks_purchasing(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.enabled', '0');

        $this->buy($user)->assertNotFound();
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_by_default_a_plain_candidate_can_buy(): void
    {
        $user = $this->candidateUser();

        $this->buy($user)->assertRedirect();
        $this->assertSame(1, Order::where('user_id', $user->id)->count());
    }

    public function test_unverified_email_blocks_when_required(): void
    {
        $user = $this->candidateUser(['email_verified_at' => null]);
        $this->set('boost.require_verified_email', '1');

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_missing_cv_blocks_when_required(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.require_cv', '1');

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_low_profile_completion_blocks(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.min_profile_completion', '95');

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_the_page_explains_why_buying_is_blocked(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.require_cv', '1');

        $this->actingAs($user)
            ->get(route('candidate.boost.index'))
            ->assertOk()
            ->assertSee('Upload a CV before boosting your profile.')
            ->assertSee('Unavailable');
    }

    public function test_block_when_active_prevents_stacking(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.block_when_active', '1');

        CandidateBoost::create([
            'candidate_id' => $user->candidate->id,
            'days' => 30,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(20),
            'status' => CandidateBoost::STATUS_ACTIVE,
            'price' => 29,
            'currency' => 'NGN',
        ]);

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_stacking_is_allowed_by_default(): void
    {
        $user = $this->candidateUser();

        CandidateBoost::create([
            'candidate_id' => $user->candidate->id,
            'days' => 30,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(20),
            'status' => CandidateBoost::STATUS_ACTIVE,
            'price' => 29,
            'currency' => 'NGN',
        ]);

        $this->buy($user)->assertRedirect();
        $this->assertSame(1, Order::where('user_id', $user->id)->count());
    }

    public function test_the_stacked_days_cap_rejects_a_package_that_would_exceed_it(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.max_stacked_days', '40');

        // 25 days already queued; the 30-day package would reach 55.
        $user->candidate->update(['featured_until' => now()->addDays(25)]);

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_a_shorter_package_still_fits_under_the_cap(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.max_stacked_days', '40');
        $user->candidate->update(['featured_until' => now()->addDays(25)]);

        $short = BoostPackage::where('days', 7)->first();

        $this->buy($user, $short)->assertRedirect();
        $this->assertSame(1, Order::where('user_id', $user->id)->count());
    }

    public function test_the_cooldown_blocks_a_repeat_purchase(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.cooldown_days', '30');

        CandidateBoost::create([
            'candidate_id' => $user->candidate->id,
            'days' => 7,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDays(3),
            'status' => CandidateBoost::STATUS_EXPIRED,
            'price' => 9,
            'currency' => 'NGN',
        ]);

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_the_yearly_cap_blocks_once_reached(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.max_per_year', '2');

        foreach ([1, 2] as $i) {
            CandidateBoost::create([
                'candidate_id' => $user->candidate->id,
                'days' => 7,
                'starts_at' => now()->subMonths($i + 1),
                'ends_at' => now()->subMonths($i),
                'status' => CandidateBoost::STATUS_EXPIRED,
                'price' => 9,
                'currency' => 'NGN',
            ]);
        }

        $this->buy($user)->assertSessionHas('error');
        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_refunded_boosts_do_not_count_toward_the_yearly_cap(): void
    {
        $user = $this->candidateUser();
        $this->set('boost.max_per_year', '1');

        CandidateBoost::create([
            'candidate_id' => $user->candidate->id,
            'days' => 7,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
            'status' => CandidateBoost::STATUS_REFUNDED,
            'price' => 9,
            'currency' => 'NGN',
        ]);

        $this->buy($user)->assertRedirect();
        $this->assertSame(1, Order::where('user_id', $user->id)->count());
    }

    public function test_admin_can_save_boost_settings(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $this->actingAs($admin)
            ->put(route('admin.boosts.settings.update'), [
                'boost_enabled' => 1,
                'reminders_enabled' => 1,
                'reminder_lead_days' => 5,
                'require_cv' => 1,
                'min_profile_completion' => 60,
                'max_stacked_days' => 90,
                'cooldown_days' => 0,
                'max_per_year' => 6,
            ])
            ->assertRedirect(route('admin.boosts.settings.index'));

        $settings = app(Settings::class);
        $this->assertSame('5', (string) $settings->get('boost.reminder_lead_days', null));
        $this->assertSame('1', (string) $settings->get('boost.require_cv', null));
        $this->assertSame('60', (string) $settings->get('boost.min_profile_completion', null));
        // Unticked boxes must persist as off, not be left at their old value.
        $this->assertSame('0', (string) $settings->get('boost.require_verified_email', null));
    }
}
