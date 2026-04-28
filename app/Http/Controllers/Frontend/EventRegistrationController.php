<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    public function showForm(Request $request, Event $event)
    {
        if ($event->hasExternalUrl()) {
            // External events don't use our form — redirect to the listing.
            return redirect()->route('events.index');
        }

        if ($event->isSoldOut()) {
            return redirect()->route('events.index')
                ->with('error', "Sorry, '{$event->title}' is sold out.");
        }

        $user = $request->user();

        return view('pages.events.register', [
            'pageTitle' => 'Register · ' . $event->title,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Events', 'url' => route('events.index')],
                ['label' => 'Register'],
            ],
            'event' => $event,
            'prefill' => [
                'buyer_name' => old('buyer_name', $user?->name ?? ''),
                'buyer_email' => old('buyer_email', $user?->email ?? ''),
                'buyer_phone' => old('buyer_phone', ''),
            ],
        ]);
    }

    public function submit(Request $request, Event $event, EmailDispatcher $dispatcher)
    {
        if ($event->hasExternalUrl()) {
            return redirect()->route('events.index');
        }
        if ($event->isSoldOut()) {
            return back()->with('error', 'Sorry, this event is sold out.');
        }

        // Paid events require a logged-in user (so we can attach the Order).
        if ($event->isPaid() && ! $request->user()) {
            return redirect()->route('auth.login', ['redirect' => route('events.register.show', $event)])
                ->with('error', 'Please sign in to purchase tickets.');
        }

        // Build dynamic answer rules from event questions.
        $rules = [
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['nullable', 'string', 'max:50'],
            'ticket_count' => ['required', 'integer', 'min:1', 'max:10'],
            'answers' => ['nullable', 'array'],
        ];
        foreach (($event->questions ?? []) as $q) {
            $key = "answers.{$q['id']}";
            $rules[$key] = [
                ($q['required'] ?? false) ? 'required' : 'nullable',
                'string',
                'max:1000',
            ];
            if (($q['type'] ?? 'text') === 'select' && ! empty($q['options'])) {
                $rules[$key][] = \Illuminate\Validation\Rule::in($q['options']);
            }
        }
        $data = $request->validate($rules);

        // Capacity guard.
        if ($event->capacity !== null
            && ($event->seats_sold + $data['ticket_count']) > $event->capacity) {
            return back()
                ->withInput()
                ->with('error', 'Not enough seats available — please reduce the ticket count or check back later.');
        }

        // Free internal event: short-circuit, no Order.
        if ($event->isFreeInternal()) {
            $registration = DB::transaction(function () use ($event, $data, $request) {
                $reg = EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => $request->user()?->id,
                    'order_id' => null,
                    'status' => EventRegistration::STATUS_PAID,
                    'ticket_count' => $data['ticket_count'],
                    'buyer_name' => $data['buyer_name'],
                    'buyer_email' => $data['buyer_email'],
                    'buyer_phone' => $data['buyer_phone'] ?? null,
                    'answers' => $data['answers'] ?? null,
                    'registered_at' => now(),
                ]);
                $event->increment('seats_sold', $data['ticket_count']);
                return $reg;
            });

            $dispatcher->send('event.ticket_issued', [$data['buyer_email'], $request->user()?->id, $data['buyer_name']], [
                'event_title' => $event->title,
                'event_date' => $event->display_date ?? '',
                'event_location' => $event->location ?? '',
                'ticket_count' => $data['ticket_count'],
                'total_amount' => '0.00',
                'currency' => $event->currency ?? '',
                'registration_id' => $registration->id,
            ]);

            return redirect()->route('events.register.thanks', $event)
                ->with('success', "You're registered for {$event->title}.");
        }

        // Paid: create Order + OrderItem and route to checkout.
        $unitPrice = (float) $event->price;
        $total = $unitPrice * $data['ticket_count'];

        $order = DB::transaction(function () use ($event, $data, $request, $unitPrice, $total) {
            $order = Order::create([
                'order_number' => 'EVT-' . strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'subtotal' => $total,
                'tax' => 0,
                'total' => $total,
                'currency' => $event->currency ?? 'USD',
                'gateway' => 'paystack',
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'orderable_type' => Event::class,
                'orderable_id' => $event->id,
                'price' => $unitPrice,
                'quantity' => $data['ticket_count'],
                'subtotal' => $total,
                'status' => OrderItem::STATUS_PENDING,
                'meta' => [
                    'ticket_count' => $data['ticket_count'],
                    'buyer_name' => $data['buyer_name'],
                    'buyer_email' => $data['buyer_email'],
                    'buyer_phone' => $data['buyer_phone'] ?? null,
                    'answers' => $data['answers'] ?? null,
                ],
            ]);

            return $order;
        });

        return redirect()->route('order.detail', $order->id);
    }

    public function thanks(Event $event)
    {
        return view('pages.events.registered', [
            'pageTitle' => 'Registered · ' . $event->title,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Events', 'url' => route('events.index')],
                ['label' => 'Registered'],
            ],
            'event' => $event,
        ]);
    }
}
