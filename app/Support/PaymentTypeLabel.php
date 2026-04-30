<?php

namespace App\Support;

use App\Models\Order;

class PaymentTypeLabel
{
    public static function for(?Order $order): string
    {
        if (! $order) {
            return 'Order';
        }

        $prefix = strtoupper(substr((string) $order->order_number, 0, 3));

        return match ($prefix) {
            'BST' => 'Profile Boost',
            'SUB' => 'Premium Membership',
            'EVT' => 'Event Registration',
            'TRN' => 'Training Enrolment',
            default => 'Order',
        };
    }

    public static function description(?Order $order): string
    {
        if (! $order || $order->items->isEmpty()) {
            return self::for($order);
        }

        $first = $order->items->first();
        $type = self::for($order);
        $orderableType = class_basename($first->orderable_type ?? '');

        $detail = match ($orderableType) {
            'TrainingProgram' => $first->meta['program_title'] ?? null,
            'Event' => $first->meta['buyer_name'] ?? null,
            'Plan' => ucfirst($first->meta['billing_cycle'] ?? '') ?: null,
            'Candidate' => isset($first->meta['days']) ? $first->meta['days'] . '-day' : null,
            default => null,
        };

        return $detail ? "{$type}: {$detail}" : $type;
    }
}
