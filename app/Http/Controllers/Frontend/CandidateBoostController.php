<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CandidateBoostController extends Controller
{
    public function index(Request $request, Settings $settings)
    {
        $user = $request->user();
        $candidate = $user->candidate ?? null;

        if (! $candidate) {
            return redirect()->route('user.candidate.profile')
                ->with('error', 'Complete your candidate profile before boosting your visibility.');
        }

        $group = $settings->group('candidate_boost');
        $packages = $this->packages($group);

        return view('pages.candidates.boost', [
            'pageTitle' => 'Boost my profile',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Dashboard', 'url' => route('user.dashboard')],
                ['label' => 'Boost profile'],
            ],
            'candidate' => $candidate,
            'packages' => $packages,
            'currency' => $group['candidate_boost.currency'] ?? 'USD',
        ]);
    }

    public function purchase(Request $request, Settings $settings)
    {
        $user = $request->user();
        $candidate = $user->candidate ?? null;
        if (! $candidate) {
            return redirect()->route('user.candidate.profile')
                ->with('error', 'Complete your candidate profile before boosting.');
        }

        $data = $request->validate([
            'days' => ['required', 'integer', 'in:7,30,90'],
        ]);

        $group = $settings->group('candidate_boost');
        $packages = $this->packages($group);
        $package = collect($packages)->firstWhere('days', $data['days']);
        if (! $package || $package['price'] <= 0) {
            return back()->with('error', 'This boost package is not available right now.');
        }

        $order = DB::transaction(function () use ($candidate, $user, $package, $group) {
            $order = Order::create([
                'order_number' => 'BST-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'subtotal' => $package['price'],
                'tax' => 0,
                'total' => $package['price'],
                'currency' => $group['candidate_boost.currency'] ?? 'USD',
                'gateway' => 'paystack',
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'orderable_type' => Candidate::class,
                'orderable_id' => $candidate->id,
                'price' => $package['price'],
                'quantity' => 1,
                'subtotal' => $package['price'],
                'status' => OrderItem::STATUS_PENDING,
                'meta' => ['days' => (int) $package['days']],
            ]);

            return $order;
        });

        return redirect()->route('order.detail', $order->id);
    }

    private function packages(array $group): array
    {
        return [
            ['days' => 7,  'price' => (float) ($group['candidate_boost.price_7d']  ?? 9),  'label' => 'Quick boost', 'tagline' => 'Try it for a week'],
            ['days' => 30, 'price' => (float) ($group['candidate_boost.price_30d'] ?? 29), 'label' => 'Standard',     'tagline' => 'Best value · 30 days top placement'],
            ['days' => 90, 'price' => (float) ($group['candidate_boost.price_90d'] ?? 69), 'label' => 'Quarter',      'tagline' => 'Stay featured for 3 months'],
        ];
    }
}
