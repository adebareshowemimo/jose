<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Category;
use App\Models\Company;
use App\Models\Industry;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\JobType;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\PayoutAccount;
use App\Models\Plan;
use App\Models\Resume;
use App\Models\Review;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_portal_core_relationships_work_end_to_end(): void
    {
        $role = Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $owner = User::create([
            'name' => 'Employer Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
            'role_id' => $role->id,
        ]);

        $candidateUser = User::create([
            'name' => 'Candidate User',
            'email' => 'candidate@example.com',
            'password' => 'password',
            'role_id' => $role->id,
        ]);

        $industry = Industry::create(['name' => 'Technology', 'slug' => 'technology']);
        $location = Location::create(['name' => 'Lagos', 'slug' => 'lagos']);
        $category = Category::create(['name' => 'Engineering', 'slug' => 'engineering']);
        $skill = Skill::create(['name' => 'PHP', 'slug' => 'php']);
        $jobType = JobType::create(['name' => 'Full Time', 'slug' => 'full-time']);

        $company = Company::create([
            'owner_id' => $owner->id,
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
            'location_id' => $location->id,
        ]);
        $company->industries()->attach($industry->id);

        $candidate = Candidate::create([
            'user_id' => $candidateUser->id,
            'slug' => 'candidate-user',
            'location_id' => $location->id,
        ]);
        $candidate->skills()->attach($skill->id);
        $candidate->categories()->attach($category->id);

        $resume = Resume::create([
            'candidate_id' => $candidate->id,
            'title' => 'Main Resume',
            'file_path' => 'resumes/main.pdf',
            'is_default' => true,
        ]);

        $jobListing = JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $owner->id,
            'title' => 'Senior Laravel Developer',
            'slug' => 'senior-laravel-developer',
            'description' => 'Build and maintain Laravel applications.',
            'category_id' => $category->id,
            'job_type_id' => $jobType->id,
            'location_id' => $location->id,
            'apply_method' => 'internal',
            'status' => 'active',
        ]);

        $application = JobApplication::create([
            'job_listing_id' => $jobListing->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'cover_letter' => 'I am a great fit for this role.',
            'status' => 'applied',
        ]);

        $this->assertTrue($company->owner->is($owner));
        $this->assertTrue($company->industries->contains($industry));
        $this->assertTrue($candidate->user->is($candidateUser));
        $this->assertTrue($candidate->skills->contains($skill));
        $this->assertTrue($candidate->categories->contains($category));
        $this->assertTrue($jobListing->company->is($company));
        $this->assertTrue($jobListing->applications->contains($application));
        $this->assertTrue($application->resume->is($resume));
    }

    public function test_commerce_and_polymorphic_relationships_work(): void
    {
        $role = Role::create(['name' => 'vendor', 'guard_name' => 'web']);

        $user = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@example.com',
            'password' => 'password',
            'role_id' => $role->id,
        ]);

        $plan = Plan::create([
            'name' => 'Pro',
            'monthly_price' => 49.99,
            'annual_price' => 499.99,
            'max_job_posts' => 20,
            'max_featured_jobs' => 5,
            'resume_access' => true,
            'is_active' => true,
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addMonth()->toDateString(),
            'status' => 'active',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-10001',
            'user_id' => $user->id,
            'subtotal' => 49.99,
            'tax' => 0,
            'total' => 49.99,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'orderable_type' => Plan::class,
            'orderable_id' => $plan->id,
            'price' => 49.99,
            'quantity' => 1,
            'subtotal' => 49.99,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'amount' => 49.99,
            'currency' => 'USD',
            'exchange_rate' => 1,
            'status' => 'completed',
        ]);

        $payoutAccount = PayoutAccount::create([
            'user_id' => $user->id,
            'method' => 'bank',
            'account_details' => ['bank' => 'Demo Bank', 'account_number' => '1234567890'],
            'is_primary' => true,
        ]);

        $payout = Payout::create([
            'user_id' => $user->id,
            'payout_account_id' => $payoutAccount->id,
            'amount' => 30,
            'currency' => 'USD',
            'status' => 'pending',
            'month' => (int) now()->format('m'),
            'year' => (int) now()->format('Y'),
        ]);

        $review = Review::create([
            'reviewable_type' => Plan::class,
            'reviewable_id' => $plan->id,
            'reviewer_id' => $user->id,
            'rating' => 5,
            'status' => 'approved',
        ]);

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'wishlistable_type' => Plan::class,
            'wishlistable_id' => $plan->id,
        ]);

        $this->assertTrue($subscription->user->is($user));
        $this->assertTrue($subscription->plan->is($plan));
        $this->assertTrue($order->user->is($user));
        $this->assertTrue($order->items->contains($item));
        $this->assertTrue($order->payments->contains($payment));
        $this->assertTrue($item->orderable->is($plan));
        $this->assertTrue($payment->order->is($order));
        $this->assertTrue($payout->payoutAccount->is($payoutAccount));
        $this->assertTrue($review->reviewer->is($user));
        $this->assertTrue($wishlist->wishlistable->is($plan));
    }
}
