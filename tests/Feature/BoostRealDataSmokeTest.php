<?php

namespace Tests\Feature;

use App\Models\BoostPackage;
use App\Models\CandidateBoost;
use App\Models\User;
use Tests\TestCase;

/**
 * Smoke test against the REAL development database, deliberately without
 * RefreshDatabase.
 *
 * The rest of the suite runs on a fresh in-memory schema, which cannot catch a
 * page that breaks only on real-world data - a null relation, a legacy row, a
 * candidate with no user. This walks the boost screens as a genuine admin and
 * fails on any 500 or rendered error.
 *
 * Read-only: it never writes, so it is safe to run against the dev database.
 *
 * @group smoke
 */
class BoostRealDataSmokeTest extends TestCase
{
    private function admin(): User
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->first();

        if (! $admin) {
            $this->markTestSkipped('No admin user in the local database.');
        }

        return $admin;
    }

    public function test_boost_admin_screens_render_against_real_data(): void
    {
        $admin = $this->admin();

        $paths = [
            '/admin/boosts',
            '/admin/boosts?status=active',
            '/admin/boosts?status=expiring',
            '/admin/boosts?status=expired',
            '/admin/boosts?status=refunded',
            '/admin/boosts?q=a',
            '/admin/boosts/packages',
            '/admin/boosts/packages/create',
            '/admin/boosts/settings',
        ];

        foreach ($paths as $path) {
            $response = $this->actingAs($admin)->get($path);

            $this->assertSame(
                200,
                $response->getStatusCode(),
                "{$path} returned {$response->getStatusCode()}"
            );
        }
    }

    public function test_every_package_edit_screen_renders(): void
    {
        $admin = $this->admin();

        foreach (BoostPackage::all() as $package) {
            $this->actingAs($admin)
                ->get("/admin/boosts/packages/{$package->id}/edit")
                ->assertOk();
        }
    }

    public function test_every_boost_detail_screen_renders(): void
    {
        $admin = $this->admin();
        $boosts = CandidateBoost::all();

        if ($boosts->isEmpty()) {
            $this->markTestSkipped('No boosts in the local database.');
        }

        foreach ($boosts as $boost) {
            $this->actingAs($admin)
                ->get("/admin/boosts/subscriber/{$boost->id}")
                ->assertOk();
        }
    }

    public function test_the_csv_export_streams(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/boosts/export');

        $response->assertOk();
        $this->assertStringContainsString('Boost ID', $response->streamedContent());
    }

    public function test_the_candidate_boost_page_renders_for_a_real_candidate(): void
    {
        $user = User::whereHas('candidate')
            ->whereHas('role', fn ($q) => $q->where('name', 'candidate'))
            ->first();

        if (! $user) {
            $this->markTestSkipped('No candidate user in the local database.');
        }

        $this->actingAs($user)->get('/user/boost')->assertOk();
    }

    public function test_the_wider_admin_area_still_renders(): void
    {
        $admin = $this->admin();

        foreach (['/admin', '/admin/orders', '/admin/payments', '/admin/users'] as $path) {
            $response = $this->actingAs($admin)->get($path);

            $this->assertSame(
                200,
                $response->getStatusCode(),
                "{$path} returned {$response->getStatusCode()}"
            );
        }
    }
}
