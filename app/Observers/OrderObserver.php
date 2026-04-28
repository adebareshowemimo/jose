<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\HandlerRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $registry = app(HandlerRegistry::class);
        $dispatcher = app(EmailDispatcher::class);

        $order->load('items.orderable');

        foreach ($order->items as $item) {
            // Idempotency: skip already-fulfilled items.
            if ($item->status === OrderItem::STATUS_FULFILLED) {
                continue;
            }

            $type = $item->orderable_type;
            if (! $type || ! $registry->has($type)) {
                continue;
            }

            try {
                DB::transaction(function () use ($registry, $dispatcher, $order, $item, $type) {
                    $registry->resolve($type)->handlePaid($order, $item, $dispatcher);
                    $item->update(['status' => OrderItem::STATUS_FULFILLED]);
                });
            } catch (\Throwable $e) {
                Log::error("Order fulfilment failed for order {$order->id} item {$item->id} ({$type}): {$e->getMessage()}");
                $item->update(['status' => OrderItem::STATUS_FAILED]);
            }
        }
    }
}
