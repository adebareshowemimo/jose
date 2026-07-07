<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobListing;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobListingSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_listing_slug_generation_reserves_soft_deleted_slugs(): void
    {
        $role = Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $owner = User::create([
            'name' => 'Employer Owner',
            'email' => 'slug-owner@example.com',
            'password' => 'password',
            'role_id' => $role->id,
        ]);

        $company = Company::create([
            'owner_id' => $owner->id,
            'name' => 'Slug Test Company',
            'slug' => 'slug-test-company',
        ]);

        $jobListing = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $owner->id,
            'title' => 'Engine Cadet',
            'slug' => 'engine-cadet',
            'description' => 'Training role.',
            'apply_method' => 'internal',
            'status' => 'pending',
        ]);

        $jobListing->delete();

        $this->assertSame('engine-cadet-1', JobListing::uniqueSlugForTitle('ENGINE CADET'));
    }
}
