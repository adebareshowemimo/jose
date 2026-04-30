<?php

namespace App\Support\OrderFulfillment\Handlers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecruitmentRequest;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\OrderableHandler;

class RecruitmentRequestHandler implements OrderableHandler
{
    public function handlePaid(Order $order, OrderItem $item, EmailDispatcher $dispatcher): void
    {
        $request = $item->orderable;

        if (! $request instanceof RecruitmentRequest) {
            return;
        }

        if (in_array($request->status, ['quote_sent', 'paid'], true)) {
            $request->update(['status' => 'in_progress']);
        }

        if ($order->user) {
            $dispatcher->send('recruitment.payment_confirmed', $order->user, [
                'job_title' => $request->job_title,
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? ($request->salary_currency ?? 'USD'),
                'paid_at' => optional($order->paid_at)->format('M d, Y \a\t g:i A') ?? now()->format('M d, Y \a\t g:i A'),
                'request_url' => route('employer.recruitment-requests.show', $request),
            ]);
        }
    }
}
