<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BoostPackage;
use App\Models\Candidate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CandidateBoostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $candidate = $user->candidate ?? null;

        if (! $candidate) {
            return redirect()->route('user.candidate.profile')
                ->with('error', 'Complete your candidate profile before boosting your visibility.');
        }

        return view('pages.candidates.boost', [
            'pageTitle' => 'Boost my profile',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Dashboard', 'url' => route('user.dashboard')],
                ['label' => 'Boost profile'],
            ],
            'candidate' => $candidate,
            'packages' => BoostPackage::active()->ordered()->get(),
            'currency' => Currency::default(),
        ]);
    }

    public function purchase(Request $request)
    {
        $user = $request->user();
        $candidate = $user->candidate ?? null;
        if (! $candidate) {
            return redirect()->route('user.candidate.profile')
                ->with('error', 'Complete your candidate profile before boosting.');
        }

        $data = $request->validate([
            'package_id' => ['required', 'integer', 'exists:boost_packages,id'],
        ]);

        $package = BoostPackage::find($data['package_id']);
        if (! $package || ! $package->isPurchasable()) {
            return back()->with('error', 'This boost package is not available right now.');
        }

        // The price is copied onto the order and never read back through the
        // package relation. A later admin price edit must not rewrite the
        // amount on a sale that has already happened.
        $price = (float) $package->price;
        $days = (int) $package->days;

        $order = DB::transaction(function () use ($candidate, $user, $package, $price, $days) {
            $order = Order::create([
                'order_number' => 'BST-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'subtotal' => $price,
                'tax' => 0,
                'total' => $price,
                'currency' => Currency::default(),
                'gateway' => 'paystack',
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'orderable_type' => Candidate::class,
                'orderable_id' => $candidate->id,
                'price' => $price,
                'quantity' => 1,
                'subtotal' => $price,
                'status' => OrderItem::STATUS_PENDING,
                'meta' => [
                    'days' => $days,
                    'boost_package_id' => $package->id,
                    'package_label' => $package->label,
                ],
            ]);

            return $order;
        });

        return redirect()->route('order.detail', $order->id);
    }
}
