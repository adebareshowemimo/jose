<?php

namespace App\Support\OrderFulfillment\Handlers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\OrderableHandler;
use Illuminate\Support\Carbon;

class PlanHandler implements OrderableHandler
{
    public function handlePaid(Order $order, OrderItem $item, EmailDispatcher $dispatcher): void
    {
        $plan = $item->orderable;
        if (! $plan) {
            return;
        }

        $cycle = $item->meta['billing_cycle'] ?? 'monthly';
        $cycle = in_array($cycle, ['monthly', 'annual'], true) ? $cycle : 'monthly';

        // Check for existing active subscription on the same plan -> extend
        $existing = Subscription::where('user_id', $order->user_id)
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->first();

        $startsAt = $existing && $existing->ends_at && $existing->ends_at->isFuture()
            ? $existing->ends_at
            : Carbon::now();

        $endsAt = $cycle === 'annual'
            ? $startsAt->copy()->addYear()
            : $startsAt->copy()->addMonth();

        $isRenewal = $existing !== null;

        Subscription::updateOrCreate(
            [
                'user_id' => $order->user_id,
                'plan_id' => $plan->id,
            ],
            [
                'order_id' => $order->id,
                'billing_cycle' => $cycle,
                'starts_at' => $existing?->starts_at ?? $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
            ]
        );

        if ($order->user) {
            $key = $isRenewal ? 'subscription.renewed' : 'subscription.started';
            $dispatcher->send($key, $order->user, [
                'plan_name' => $plan->name,
                'billing_cycle' => $cycle,
                'ends_at' => $endsAt->format('M d, Y'),
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
            ]);
        }
    }
}
