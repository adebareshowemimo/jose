<?php

namespace App\Support\OrderFulfillment\Handlers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\OrderableHandler;
use Illuminate\Support\Facades\DB;

class EventHandler implements OrderableHandler
{
    public function handlePaid(Order $order, OrderItem $item, EmailDispatcher $dispatcher): void
    {
        $event = $item->orderable;
        if (! $event) {
            return;
        }

        $meta = $item->meta ?? [];
        $ticketCount = max(1, (int) ($meta['ticket_count'] ?? 1));
        $buyerName = $meta['buyer_name'] ?? ($order->user?->name ?? 'Attendee');
        $buyerEmail = $meta['buyer_email'] ?? ($order->user?->email);
        $buyerPhone = $meta['buyer_phone'] ?? null;
        $answers = $meta['answers'] ?? [];

        DB::transaction(function () use ($event, $order, $ticketCount, $buyerName, $buyerEmail, $buyerPhone, $answers) {
            // Capacity re-check inside transaction (race condition guard)
            $event->refresh();
            if ($event->capacity !== null
                && ($event->seats_sold + $ticketCount) > $event->capacity) {
                \Log::warning("EventHandler: capacity exceeded for event {$event->id}; order {$order->id} flagged for admin");
                return;
            }

            \App\Models\EventRegistration::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'event_id' => $event->id,
                    'user_id' => $order->user_id,
                    'status' => 'paid',
                    'ticket_count' => $ticketCount,
                    'buyer_name' => $buyerName,
                    'buyer_email' => $buyerEmail,
                    'buyer_phone' => $buyerPhone,
                    'answers' => $answers,
                    'registered_at' => now(),
                ]
            );

            $event->increment('seats_sold', $ticketCount);
        });

        if ($buyerEmail) {
            $dispatcher->send('event.ticket_issued', [$buyerEmail, $order->user_id, $buyerName], [
                'event_title' => $event->title,
                'event_date' => $event->display_date ?? '',
                'event_location' => $event->location ?? '',
                'ticket_count' => $ticketCount,
                'total_amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
                'registration_id' => $order->id,
            ]);
        }
    }
}
