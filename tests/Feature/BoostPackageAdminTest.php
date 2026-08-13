<?php

namespace Tests\Feature;

use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\CandidateBoost;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostPackageAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function candidateUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);
        $user = User::factory()->create(['role_id' => $role->id]);
        Candidate::create(['user_id' => $user->id, 'slug' => 'cand-'.$user->id]);

        return $user;
    }

    public function test_admin_can_list_packages(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.boosts.packages.index'))
            ->assertOk()
            ->assertSee('Standard');
    }

    public function test_admin_can_create_a_package(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.boosts.packages.store'), [
                'label' => 'Fortnight',
                'tagline' => 'Two weeks up top',
                'days' => 14,
                'price' => 15.50,
                'sort_order' => 5,
                'is_active' => 1,
                'perks' => ['top_of_search'],
            ])
            ->assertRedirect(route('admin.boosts.packages.index'));

        $package = BoostPackage::where('label', 'Fortnight')->first();

        $this->assertNotNull($package);
        $this->assertSame(14, $package->days);
        $this->assertSame('15.50', $package->price);
        $this->assertTrue($package->hasPerk('top_of_search'));
        $this->assertFalse($package->hasPerk('most_popular'));
    }

    public function test_admin_can_edit_a_price(): void
    {
        $package = BoostPackage::first();

        $this->actingAs($this->admin())
            ->put(route('admin.boosts.packages.update', $package), [
                'label' => $package->label,
                'days' => $package->days,
                'price' => 49.99,
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertSame('49.99', $package->fresh()->price);
    }

    public function test_a_package_that_has_been_sold_is_deactivated_not_deleted(): void
    {
        $package = BoostPackage::first();
        $user = $this->candidateUser();

        CandidateBoost::create([
            'candidate_id' => $user->candidate->id,
            'boost_package_id' => $package->id,
            'days' => $package->days,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
            'status' => CandidateBoost::STATUS_ACTIVE,
            'price' => $package->price,
            'currency' => 'NGN',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.boosts.packages.destroy', $package))
            ->assertRedirect();

        // Still present, so the historical boost keeps its tier - just hidden.
        $this->assertNotNull($package->fresh());
        $this->assertFalse($package->fresh()->is_active);
    }

    public function test_an_unsold_package_is_deleted_outright(): void
    {
        $package = BoostPackage::create([
            'label' => 'Unsold', 'days' => 5, 'price' => 5, 'is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.boosts.packages.destroy', $package))
            ->assertRedirect();

        $this->assertNull(BoostPackage::find($package->id));
    }

    public function test_the_public_page_only_shows_active_packages(): void
    {
        $hidden = BoostPackage::first();
        $hidden->update(['is_active' => false]);

        $this->actingAs($this->candidateUser())
            ->get('/user/boost')
            ->assertOk()
            ->assertDontSee($hidden->label)
            ->assertSee('Quarter');
    }

    public function test_a_candidate_can_buy_an_active_package(): void
    {
        $user = $this->candidateUser();
        $package = BoostPackage::where('label', 'Standard')->first();

        $this->actingAs($user)
            ->post(route('candidate.boost.purchase'), ['package_id' => $package->id])
            ->assertRedirect();

        $order = Order::where('user_id', $user->id)->latest()->first();

        $this->assertNotNull($order);
        $this->assertSame('29.00', $order->total);
        $this->assertSame('NGN', $order->currency);
        $this->assertSame($package->id, $order->items->first()->meta['boost_package_id']);
        $this->assertSame(30, $order->items->first()->meta['days']);
    }

    public function test_buying_an_inactive_package_is_rejected(): void
    {
        $user = $this->candidateUser();
        $package = BoostPackage::first();
        $package->update(['is_active' => false]);

        $this->actingAs($user)
            ->post(route('candidate.boost.purchase'), ['package_id' => $package->id])
            ->assertSessionHas('error');

        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    public function test_buying_a_free_package_is_rejected(): void
    {
        $user = $this->candidateUser();
        $package = BoostPackage::create([
            'label' => 'Freebie', 'days' => 7, 'price' => 0, 'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('candidate.boost.purchase'), ['package_id' => $package->id])
            ->assertSessionHas('error');

        $this->assertSame(0, Order::where('user_id', $user->id)->count());
    }

    /**
     * The price is copied onto the order at purchase. Editing the package
     * afterwards must not rewrite what the customer was charged.
     */
    public function test_editing_a_price_does_not_change_completed_orders(): void
    {
        $user = $this->candidateUser();
        $package = BoostPackage::where('label', 'Standard')->first();

        $this->actingAs($user)
            ->post(route('candidate.boost.purchase'), ['package_id' => $package->id]);

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertSame('29.00', $order->total);

        $package->update(['price' => 999]);

        $this->assertSame('29.00', $order->fresh()->total);
        $this->assertSame('29.00', $order->fresh()->items->first()->price);
    }
}
