<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoostPageSmokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression: the boost page 500'd because the currency fallback read a
     * settings array key that does not exist when candidate_boost has never
     * been configured. The page must render on the bare defaults.
     */
    public function test_boost_page_renders_when_boost_settings_are_unconfigured(): void
    {
        $role = Role::firstOrCreate(['name' => 'candidate']);

        $user = User::factory()->create(['role_id' => $role->id]);
        Candidate::create([
            'user_id' => $user->id,
            'slug' => 'test-candidate-'.$user->id,
        ]);

        $this->actingAs($user)->get('/user/boost')->assertOk();
    }
}
