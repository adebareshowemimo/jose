<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\Handlers\CandidateHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Pins the boost stacking rule before the packages refactor touches it.
 *
 * Buying a boost while one is already running must EXTEND from the existing
 * end date, not restart from today - otherwise a candidate who renews early
 * silently loses the time they already paid for.
 */
class BoostStackingTest extends TestCase
{
    use RefreshDatabase;

    private function candidate(): Candidate
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(['role_id' => $role->id]);

        return Candidate::create([
            'user_id' => $user->id,
            'slug' => 'stack-test-'.$user->id,
        ]);
    }

    private function payFor(Candidate $candidate, int $days): void
    {
        $order = Order::create([
            'order_number' => 'BST-'.strtoupper(uniqid()),
            'user_id' => $candidate->user_id,
            'subtotal' => 10,
            'tax' => 0,
            'total' => 10,
            'currency' => 'NGN',
            'gateway' => 'paystack',
            'status' => 'completed',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'orderable_type' => Candidate::class,
            'orderable_id' => $candidate->id,
            'price' => 10,
            'quantity' => 1,
            'subtotal' => 10,
            'status' => OrderItem::STATUS_PENDING,
            'meta' => ['days' => $days],
        ]);

        app(CandidateHandler::class)->handlePaid(
            $order->fresh(),
            $item->fresh(),
            app(EmailDispatcher::class)
        );
    }

    public function test_a_first_boost_runs_from_today(): void
    {
        Carbon::setTestNow('2026-01-01 12:00:00');

        $candidate = $this->candidate();
        $this->payFor($candidate, 30);

        $this->assertSame(
            '2026-01-31',
            $candidate->fresh()->featured_until->toDateString()
        );

        Carbon::setTestNow();
    }

    public function test_buying_again_while_active_extends_rather_than_restarts(): void
    {
        Carbon::setTestNow('2026-01-01 12:00:00');

        $candidate = $this->candidate();
        $this->payFor($candidate, 30);

        // Renew a week in, with 23 days still unused.
        Carbon::setTestNow('2026-01-08 12:00:00');
        $this->payFor($candidate, 30);

        // 31 Jan + 30 days. Restarting from today would give 7 Feb and
        // would burn the 23 days already paid for.
        $this->assertSame(
            '2026-03-02',
            $candidate->fresh()->featured_until->toDateString()
        );

        Carbon::setTestNow();
    }

    public function test_buying_after_expiry_runs_from_today(): void
    {
        Carbon::setTestNow('2026-01-01 12:00:00');

        $candidate = $this->candidate();
        $this->payFor($candidate, 7);

        // Well after the first boost lapsed.
        Carbon::setTestNow('2026-02-01 12:00:00');
        $this->payFor($candidate, 30);

        $this->assertSame(
            '2026-03-03',
            $candidate->fresh()->featured_until->toDateString()
        );

        Carbon::setTestNow();
    }
}
