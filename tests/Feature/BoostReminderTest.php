<?php

namespace Tests\Feature;

use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\CandidateBoost;
use App\Models\EmailLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\MonetizationEmailTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BoostReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->seed(MonetizationEmailTemplateSeeder::class);
    }

    private function candidate(string $name = 'Boosted Bea'): Candidate
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
            'starts_at' => now()->subDays(28),
            'ends_at' => now()->addDays(2),
            'status' => CandidateBoost::STATUS_ACTIVE,
            'price' => 29,
            'currency' => 'NGN',
        ], $attrs));
    }

    private function sentCount(string $key): int
    {
        return EmailLog::where('template_key', $key)->count();
    }

    public function test_a_boost_inside_the_lead_window_is_warned(): void
    {
        $boost = $this->boost($this->candidate());

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertNotNull($boost->fresh()->expiring_reminder_sent_at);
        $this->assertSame(1, $this->sentCount('candidate.boost_expiring'));
    }

    public function test_a_boost_outside_the_lead_window_is_not_warned(): void
    {
        $boost = $this->boost($this->candidate(), ['ends_at' => now()->addDays(30)]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertNull($boost->fresh()->expiring_reminder_sent_at);
        $this->assertSame(0, $this->sentCount('candidate.boost_expiring'));
    }

    public function test_the_warning_is_not_sent_twice_for_the_same_boost(): void
    {
        $this->boost($this->candidate());

        $this->artisan('boosts:send-reminders')->assertSuccessful();
        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertSame(1, $this->sentCount('candidate.boost_expiring'));
    }

    /**
     * Dedup is per boost, not per user: a second purchase must be warned about
     * on its own merits.
     */
    public function test_a_second_boost_for_the_same_candidate_is_warned_separately(): void
    {
        $candidate = $this->candidate();
        $this->boost($candidate);

        $this->artisan('boosts:send-reminders')->assertSuccessful();
        $this->assertSame(1, $this->sentCount('candidate.boost_expiring'));

        $this->boost($candidate, ['ends_at' => now()->addDay()]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();
        $this->assertSame(2, $this->sentCount('candidate.boost_expiring'));
    }

    public function test_an_expired_boost_gets_the_expired_notice(): void
    {
        $boost = $this->boost($this->candidate(), [
            'ends_at' => now()->subDay(),
            'status' => CandidateBoost::STATUS_EXPIRED,
        ]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertNotNull($boost->fresh()->expired_notice_sent_at);
        $this->assertSame(1, $this->sentCount('candidate.boost_expired'));
    }

    /**
     * A boost past its end date but not yet swept by boosts:expire is still
     * 'active'. Emailing "your boost has ended" before the sweep would be
     * premature, so the notice waits for the status change.
     */
    public function test_an_unswept_boost_does_not_get_the_expired_notice(): void
    {
        $this->boost($this->candidate(), [
            'ends_at' => now()->subDay(),
            'status' => CandidateBoost::STATUS_ACTIVE,
        ]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertSame(0, $this->sentCount('candidate.boost_expired'));
    }

    public function test_old_backlog_is_not_emailed_about(): void
    {
        $boost = $this->boost($this->candidate(), [
            'ends_at' => now()->subMonths(6),
            'status' => CandidateBoost::STATUS_EXPIRED,
        ]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertNull($boost->fresh()->expired_notice_sent_at);
        $this->assertSame(0, $this->sentCount('candidate.boost_expired'));
    }

    public function test_refunded_boosts_are_not_emailed_about(): void
    {
        $this->boost($this->candidate(), [
            'ends_at' => now()->subDay(),
            'status' => CandidateBoost::STATUS_REFUNDED,
        ]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertSame(0, $this->sentCount('candidate.boost_expired'));
        $this->assertSame(0, $this->sentCount('candidate.boost_expiring'));
    }

    public function test_dry_run_sends_nothing_and_stamps_nothing(): void
    {
        $boost = $this->boost($this->candidate());

        $this->artisan('boosts:send-reminders --dry-run')->assertSuccessful();

        $this->assertNull($boost->fresh()->expiring_reminder_sent_at);
        $this->assertSame(0, $this->sentCount('candidate.boost_expiring'));
    }

    public function test_the_command_can_be_disabled_in_settings(): void
    {
        app(\App\Support\Settings::class)->set('boost.reminders_enabled', '0', 'boost');
        $boost = $this->boost($this->candidate());

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertNull($boost->fresh()->expiring_reminder_sent_at);
    }

    public function test_the_lead_window_is_configurable(): void
    {
        app(\App\Support\Settings::class)->set('boost.reminder_lead_days', '14', 'boost');
        $boost = $this->boost($this->candidate(), ['ends_at' => now()->addDays(10)]);

        $this->artisan('boosts:send-reminders')->assertSuccessful();

        $this->assertNotNull($boost->fresh()->expiring_reminder_sent_at);
    }
}
