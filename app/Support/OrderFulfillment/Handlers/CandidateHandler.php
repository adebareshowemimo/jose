<?php

namespace App\Support\OrderFulfillment\Handlers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\OrderableHandler;
use Illuminate\Support\Carbon;

class CandidateHandler implements OrderableHandler
{
    public function handlePaid(Order $order, OrderItem $item, EmailDispatcher $dispatcher): void
    {
        $candidate = $item->orderable;
        if (! $candidate) {
            return;
        }

        $days = max(1, (int) ($item->meta['days'] ?? 30));

        $startsAt = ($candidate->featured_until && $candidate->featured_until->isFuture())
            ? $candidate->featured_until
            : Carbon::now();
        $endsAt = $startsAt->copy()->addDays($days);

        $candidate->update(['featured_until' => $endsAt]);

        \App\Models\CandidateBoost::create([
            'candidate_id' => $candidate->id,
            'order_id' => $order->id,
            'days' => $days,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'price' => $order->total,
            'currency' => $order->currency ?? 'USD',
        ]);

        if ($order->user) {
            $dispatcher->send('candidate.boost_activated', $order->user, [
                'days' => $days,
                'ends_at' => $endsAt->format('M d, Y'),
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
                'profile_url' => $candidate->slug
                    ? route('candidate.detail', $candidate->slug)
                    : url('/'),
            ]);
        }
    }
}
