<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use App\Support\Currency;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ─── Dashboard ───────────────────────────────────────────────

    public function dashboard()
    {
        $totalUsers      = User::count();
        $totalCompanies  = Company::count();
        $totalJobs       = JobListing::count();
        $totalCandidates = Candidate::count();
        $totalOrders     = Order::count();
        $totalRevenue    = $this->sumPaymentsInDefault(
            Payment::where('status', 'completed')
        );

        $activeJobs      = JobListing::where('status', 'active')->count();
        $pendingJobs     = JobListing::where('status', 'pending')->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $recentUsers = User::with('role')->latest()->take(10)->get();
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Revenue chart — last 6 months (converted to default currency per row)
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueChart[] = [
                'month' => $date->format('M Y'),
                'total' => $this->sumPaymentsInDefault(
                    Payment::where('status', 'completed')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                ),
            ];
        }

        // User registrations — last 6 months
        $userChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $userChart[] = [
                'month' => $date->format('M Y'),
                'count' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalCompanies', 'totalJobs', 'totalCandidates',
            'totalOrders', 'totalRevenue', 'activeJobs', 'pendingJobs',
            'activeSubscriptions', 'recentUsers', 'recentOrders',
            'revenueChart', 'userChart'
        ));
    }

    // ─── Users ───────────────────────────────────────────────────

    public function users(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function showUser(User $user)
    {
        $user->load(['role', 'company', 'candidate', 'subscriptions.plan', 'orders.payments']);
        return view('admin.users.show', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'status'  => 'required|in:active,inactive,banned',
            'is_verified' => 'boolean',
        ]);

        $validated['is_verified'] = $request->boolean('is_verified');
        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|string|min:8|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password reset successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->role?->name === 'admin') {
            return back()->with('error', 'Cannot delete admin users.');
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }

    // ─── Companies ───────────────────────────────────────────────

    public function companies(Request $request)
    {
        $query = Company::with(['owner', 'location']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $companies = $query->latest()->paginate(20)->withQueryString();
        return view('admin.companies.index', compact('companies'));
    }

    public function showCompany(Company $company)
    {
        $company->load(['owner', 'location', 'industries', 'jobListings', 'reviews']);
        return view('admin.companies.show', compact('company'));
    }

    public function updateCompany(Request $request, Company $company)
    {
        $validated = $request->validate([
            'status'      => 'required|in:active,inactive,pending',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_verified'] = $request->boolean('is_verified');
        $company->update($validated);

        return back()->with('success', 'Company updated.');
    }

    public function deleteCompany(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.companies')->with('success', 'Company deleted.');
    }

    // ─── Job Listings ────────────────────────────────────────────

    public function jobs(Request $request)
    {
        $query = JobListing::with(['company']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(20)->withQueryString();
        return view('admin.jobs.index', compact('jobs'));
    }

    public function updateJob(Request $request, JobListing $job)
    {
        $request->validate(['status' => 'required|in:draft,pending,active,paused,closed,expired']);
        $job->update([
            'status' => $request->status,
            'is_approved' => $request->status === 'active',
        ]);

        return back()->with('success', 'Job status updated.');
    }

    public function deleteJob(JobListing $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs')->with('success', 'Job deleted.');
    }

    // ─── Plans ───────────────────────────────────────────────────

    public function plans()
    {
        $plans = Plan::withCount('subscriptions')->orderBy('sort_order')->get();
        $roles = Role::all();
        return view('admin.plans.index', compact('plans', 'roles'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'monthly_price'     => 'required|numeric|min:0',
            'annual_price'      => 'required|numeric|min:0',
            'max_job_posts'     => 'required|integer|min:0',
            'max_featured_jobs' => 'required|integer|min:0',
            'resume_access'     => 'boolean',
            'role_id'           => 'required|exists:roles,id',
            'is_recommended'    => 'boolean',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer|min:0',
        ]);

        $validated['resume_access']  = $request->boolean('resume_access');
        $validated['is_recommended'] = $request->boolean('is_recommended');
        $validated['is_active']      = $request->boolean('is_active');
        $validated['sort_order']     = $request->input('sort_order', 0);

        Plan::create($validated);
        return back()->with('success', 'Plan created.');
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'monthly_price'     => 'required|numeric|min:0',
            'annual_price'      => 'required|numeric|min:0',
            'max_job_posts'     => 'required|integer|min:0',
            'max_featured_jobs' => 'required|integer|min:0',
            'resume_access'     => 'boolean',
            'role_id'           => 'required|exists:roles,id',
            'is_recommended'    => 'boolean',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer|min:0',
        ]);

        $validated['resume_access']  = $request->boolean('resume_access');
        $validated['is_recommended'] = $request->boolean('is_recommended');
        $validated['is_active']      = $request->boolean('is_active');

        $plan->update($validated);
        return back()->with('success', 'Plan updated.');
    }

    public function deletePlan(Plan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Cannot delete plan with active subscriptions.');
        }
        $plan->delete();
        return redirect()->route('admin.plans')->with('success', 'Plan deleted.');
    }

    // ─── Subscriptions ──────────────────────────────────────────

    public function subscriptions(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(20)->withQueryString();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    // ─── Orders & Payments ──────────────────────────────────────

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'payments', 'items']);

        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('orderable_type')) {
            $type = (string) $request->input('orderable_type');
            $query->whereHas('items', fn ($q) => $q->where('orderable_type', $type));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        // Distinct orderable types currently in use, for the filter dropdown.
        $orderableTypes = \App\Models\OrderItem::query()
            ->select('orderable_type')
            ->whereNotNull('orderable_type')
            ->distinct()
            ->orderBy('orderable_type')
            ->pluck('orderable_type')
            ->all();

        return view('admin.orders.index', compact('orders', 'orderableTypes'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'items', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function verifyPayment(Request $request, Order $order, Payment $payment, EmailDispatcher $dispatcher)
    {
        if ($payment->order_id !== $order->id) {
            abort(404);
        }
        if ($payment->status === 'completed') {
            return back()->with('error', 'This payment is already verified.');
        }

        $payment->update([
            'status' => 'completed',
            'exchange_rate' => Currency::rate($payment->currency ?? 'USD', Currency::default()),
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'verified_by_admin_id' => auth()->id(),
                'verified_at' => now()->toIso8601String(),
            ]),
        ]);
        $order->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $order->load('user');
        if ($order->user) {
            $dispatcher->send('payment.confirmed', $order->user, [
                'order_number' => $order->order_number,
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
                'gateway' => ucfirst($order->gateway ?? 'manual'),
                'paid_at' => optional($order->paid_at)->format('M d, Y \a\t g:i A') ?? now()->format('M d, Y \a\t g:i A'),
                'order_url' => route('order.detail', $order->id),
            ]);
        }

        return back()->with('success', 'Payment verified. Order marked as completed. Customer notified by email.');
    }

    public function rejectPayment(Request $request, Order $order, Payment $payment)
    {
        if ($payment->order_id !== $order->id) {
            abort(404);
        }
        $reason = $request->validate(['reason' => 'nullable|string|max:500'])['reason'] ?? null;

        $payment->update([
            'status' => 'failed',
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'rejected_by_admin_id' => auth()->id(),
                'rejected_at' => now()->toIso8601String(),
                'rejection_reason' => $reason,
            ]),
        ]);
        // Revert order to pending so the employer can retry.
        $order->update(['status' => 'pending']);

        return back()->with('success', 'Payment rejected. Order returned to pending so the customer can retry.');
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['order.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();
        $totalRevenue    = $this->sumPaymentsInDefault(Payment::where('status', 'completed'));
        $pendingPayments = $this->sumPaymentsInDefault(Payment::where('status', 'pending'));

        return view('admin.payments.index', compact('payments', 'totalRevenue', 'pendingPayments'));
    }

    /**
     * Sum a Payment query in the site's default currency.
     * Prefers the per-row stamped exchange_rate (when present and not the default 1.0
     * placeholder for cross-currency rows); otherwise falls back to the current rate.
     */
    protected function sumPaymentsInDefault($query): float
    {
        $default = Currency::default();
        $total = 0.0;

        $query->select('amount', 'currency', 'exchange_rate')
            ->cursor()
            ->each(function ($row) use (&$total, $default) {
                $amount = (float) $row->amount;
                $currency = strtoupper((string) ($row->currency ?? $default));
                if ($currency === $default) {
                    $total += $amount;
                    return;
                }
                $stamped = (float) ($row->exchange_rate ?? 0);
                $rate = $stamped > 0 && $stamped !== 1.0
                    ? $stamped
                    : Currency::rate($currency, $default);
                $total += $amount * $rate;
            });

        return round($total, 2);
    }

    // ─── Applications ────────────────────────────────────────────

    public function applications(Request $request)
    {
        $query = JobApplication::with(['candidate.user', 'jobListing.company']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(20)->withQueryString();
        return view('admin.applications.index', compact('applications'));
    }
}
