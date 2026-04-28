<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CandidateUpgradeController extends Controller
{
    public function show(Request $request)
    {
        $candidateRoleId = optional(Role::where('name', 'candidate')->first())->id;

        $plans = Plan::where('is_active', true)
            ->when($candidateRoleId, fn ($q) => $q->where('role_id', $candidateRoleId))
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        $current = $request->user()
            ? Subscription::active()
                ->where('user_id', $request->user()->id)
                ->whereIn('plan_id', $plans->pluck('id'))
                ->latest()
                ->first()
            : null;

        return view('pages.candidates.upgrade', [
            'pageTitle' => 'Candidate Premium',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Dashboard', 'url' => route('user.dashboard')],
                ['label' => 'Premium'],
            ],
            'plans' => $plans,
            'currentSubscription' => $current,
        ]);
    }

    public function subscribe(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'billing_cycle' => ['required', 'in:monthly,annual'],
        ]);

        $price = $data['billing_cycle'] === 'annual' ? $plan->annual_price : $plan->monthly_price;
        if ($price <= 0) {
            return back()->with('error', 'This plan is not available for purchase right now.');
        }

        $user = $request->user();

        $order = DB::transaction(function () use ($plan, $user, $price, $data) {
            $order = Order::create([
                'order_number' => 'SUB-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'subtotal' => $price,
                'tax' => 0,
                'total' => $price,
                'currency' => 'USD',
                'gateway' => 'paystack',
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'orderable_type' => Plan::class,
                'orderable_id' => $plan->id,
                'price' => $price,
                'quantity' => 1,
                'subtotal' => $price,
                'status' => OrderItem::STATUS_PENDING,
                'meta' => ['billing_cycle' => $data['billing_cycle']],
            ]);

            return $order;
        });

        return redirect()->route('order.detail', $order->id);
    }
}
