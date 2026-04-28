<?php

namespace App\Support\OrderFulfillment;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\EmailDispatcher;

interface OrderableHandler
{
    /**
     * Fulfil a paid OrderItem (create the domain record, send the confirmation email, etc.).
     *
     * Implementations must be idempotent: the OrderObserver will skip items already
     * marked OrderItem::STATUS_FULFILLED, but handlers should still re-check their
     * own side-effects (e.g. don't double-create an enrolment row).
     */
    public function handlePaid(Order $order, OrderItem $item, EmailDispatcher $dispatcher): void;
}
