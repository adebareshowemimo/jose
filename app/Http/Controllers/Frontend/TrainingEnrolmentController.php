<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TrainingEnrolment;
use App\Models\TrainingProgram;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrainingEnrolmentController extends Controller
{
    public function enrol(Request $request, TrainingProgram $program, EmailDispatcher $dispatcher)
    {
        if (! $program->is_active) {
            return back()->with('error', 'This programme is not currently open for enrolment.');
        }

        $user = $request->user();
        if (! $user) {
            return redirect()->route('auth.login')->with('error', 'Please sign in to enrol.');
        }

        // Already enrolled?
        $existing = TrainingEnrolment::where('training_program_id', $program->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['paid', 'active', 'completed'])
            ->first();
        if ($existing) {
            return redirect()->route('training.show', $program->slug)
                ->with('info', "You're already enrolled in this programme.");
        }

        // Free programme — short-circuit, no Order needed.
        if ($program->isFree()) {
            $enrolment = TrainingEnrolment::updateOrCreate(
                ['training_program_id' => $program->id, 'user_id' => $user->id],
                ['status' => 'paid', 'enrolled_at' => now()]
            );

            $dispatcher->send('training.enrolment_confirmed', $user, [
                'program_title' => $program->title,
                'program_type' => ucfirst($program->type),
                'starts_at' => optional($program->starts_at)->format('M d, Y') ?? 'Flexible',
                'duration' => $program->duration ?? 'See programme details',
                'amount' => '0.00',
                'currency' => $program->currency ?? 'USD',
                'order_url' => route('training.show', $program->slug),
            ]);

            return redirect()->route('training.show', $program->slug)
                ->with('success', "You're enrolled. Check your inbox for the confirmation.");
        }

        // Paid programme — create Order + OrderItem and route to checkout.
        $order = DB::transaction(function () use ($program, $user) {
            $order = Order::create([
                'order_number' => 'TRN-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'subtotal' => $program->price,
                'tax' => 0,
                'total' => $program->price,
                'currency' => $program->currency ?? 'USD',
                'gateway' => 'paystack',
                'status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'orderable_type' => TrainingProgram::class,
                'orderable_id' => $program->id,
                'price' => $program->price,
                'quantity' => 1,
                'subtotal' => $program->price,
                'status' => OrderItem::STATUS_PENDING,
                'meta' => [
                    'program_slug' => $program->slug,
                    'program_title' => $program->title,
                ],
            ]);

            return $order;
        });

        return redirect()->route('order.detail', $order->id);
    }
}
