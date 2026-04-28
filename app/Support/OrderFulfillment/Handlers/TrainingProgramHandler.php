<?php

namespace App\Support\OrderFulfillment\Handlers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\EmailDispatcher;
use App\Support\OrderFulfillment\OrderableHandler;

class TrainingProgramHandler implements OrderableHandler
{
    public function handlePaid(Order $order, OrderItem $item, EmailDispatcher $dispatcher): void
    {
        $program = $item->orderable;
        if (! $program) {
            return;
        }

        $enrolment = \App\Models\TrainingEnrolment::updateOrCreate(
            [
                'training_program_id' => $program->id,
                'user_id' => $order->user_id,
            ],
            [
                'order_id' => $order->id,
                'status' => 'paid',
                'enrolled_at' => now(),
            ]
        );

        if ($order->user) {
            $dispatcher->send('training.enrolment_confirmed', $order->user, [
                'program_title' => $program->title,
                'program_type' => ucfirst($program->type ?? 'training'),
                'starts_at' => optional($program->starts_at)->format('M d, Y') ?? 'Flexible',
                'duration' => $program->duration ?? 'See programme details',
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
                'order_url' => route('order.detail', $order->id),
            ]);
        }
    }
}
