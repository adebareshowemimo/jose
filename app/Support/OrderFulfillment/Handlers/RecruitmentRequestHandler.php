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
    }
}
