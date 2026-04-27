<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\RecruitmentRequest;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        if ($order->status !== 'completed') {
            return;
        }

        // When an Order tied to a RecruitmentRequest is marked completed,
        // auto-advance the request from quote_sent → in_progress.
        $request = RecruitmentRequest::where('order_id', $order->id)
            ->whereIn('status', ['quote_sent', 'paid'])
            ->first();

        if ($request) {
            $request->update(['status' => 'in_progress']);
        }
    }
}
