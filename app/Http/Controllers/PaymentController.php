<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaystackService;
use App\Support\Currency;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Start a Paystack payment for the given Order.
     */
    public function paystackInit(Request $request, Order $order, PaystackService $paystack)
    {
        $this->authorizeOrder($request, $order);

        if ($order->status === 'completed') {
            return redirect()->route('order.detail', $order->id)
                ->with('error', 'This order is already paid.');
        }

        if (! $paystack->isConfigured()) {
            return back()->with('error', 'Online payment is not currently available. Please use bank transfer or contact support.');
        }

        $reference = 'JCL-' . $order->id . '-' . strtoupper(Str::random(6));

        $data = $paystack->initialize(
            email: $order->user->email,
            amount: (float) $order->total,
            currency: $order->currency ?? 'USD',
            reference: $reference,
            callbackUrl: route('payment.paystack.callback'),
            metadata: [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        );

        if (! $data || empty($data['authorization_url'])) {
            return back()->with('error', 'Could not start payment. Please try again or use bank transfer.');
        }

        // Pre-create a pending Payment row so we can match the reference on callback.
        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'paystack',
            'transaction_id' => $reference,
            'amount' => $order->total,
            'currency' => $order->currency ?? 'USD',
            'status' => 'pending',
            'gateway_response' => ['init' => $data],
        ]);

        return redirect()->away($data['authorization_url']);
    }

    /**
     * Paystack returns the user here after the checkout page (or the webhook hits).
     * Verifies the reference, then marks the order completed if successful.
     */
    public function paystackCallback(Request $request, PaystackService $paystack, EmailDispatcher $dispatcher)
    {
        $reference = $request->query('reference') ?? $request->input('reference');
        if (! $reference) {
            return redirect()->route('home')->with('error', 'Missing payment reference.');
        }

        $payment = Payment::where('gateway', 'paystack')
            ->where('transaction_id', $reference)
            ->latest()
            ->first();

        if (! $payment) {
            return redirect()->route('home')->with('error', 'Unknown payment reference.');
        }

        $order = Order::find($payment->order_id);
        if (! $order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        // Idempotent: already verified.
        if ($payment->status === 'completed' && $order->status === 'completed') {
            return redirect()->route('order.detail', $order->id)
                ->with('success', 'Payment already confirmed.');
        }

        $data = $paystack->verify($reference);

        if (! $data) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['verify_failed_at' => now()->toIso8601String()]),
            ]);
            return redirect()->route('order.detail', $order->id)
                ->with('error', 'We could not verify your payment with Paystack. If you were charged, please contact support.');
        }

        // Verify amount matches (Paystack returns smallest unit — kobo/cents).
        $expectedSubunit = (int) round(((float) $order->total) * 100);
        if ((int) ($data['amount'] ?? 0) !== $expectedSubunit) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['amount_mismatch' => $data]),
            ]);
            return redirect()->route('order.detail', $order->id)
                ->with('error', 'Payment amount mismatch. Please contact support.');
        }

        DB::transaction(function () use ($payment, $order, $data) {
            $payment->update([
                'status' => 'completed',
                'exchange_rate' => Currency::rate($payment->currency ?? 'USD', Currency::default()),
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['verified' => $data]),
            ]);
            $order->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway' => 'paystack',
            ]);
        });

        $this->sendPaymentConfirmedEmail($order->fresh('user'), $dispatcher);

        return redirect()->route('order.detail', $order->id)
            ->with('success', 'Payment confirmed. Thank you!');
    }

    /**
     * Submit a manual bank-transfer claim. Marks the order as 'processing'
     * pending admin verification.
     */
    public function manualSubmit(Request $request, Order $order, EmailDispatcher $dispatcher)
    {
        $this->authorizeOrder($request, $order);

        if ($order->status === 'completed') {
            return back()->with('error', 'This order is already paid.');
        }

        $data = $request->validate([
            'transaction_id' => 'required|string|max:100',
            'paid_at' => 'nullable|date',
            'sender_bank' => 'nullable|string|max:255',
            'sender_account' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store("payments/{$order->id}", 'public');
        }

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'manual',
            'transaction_id' => $data['transaction_id'],
            'amount' => $order->total,
            'currency' => $order->currency ?? 'USD',
            'status' => 'pending',
            'gateway_response' => [
                'paid_at' => $data['paid_at'] ?? null,
                'sender_bank' => $data['sender_bank'] ?? null,
                'sender_account' => $data['sender_account'] ?? null,
                'note' => $data['note'] ?? null,
                'proof_path' => $proofPath,
                'submitted_at' => now()->toIso8601String(),
                'submitted_by_user_id' => Auth::id(),
            ],
        ]);

        $order->update(['status' => 'processing', 'gateway' => 'manual']);

        $this->sendBankTransferReceivedEmails($order->fresh('user'), $data['transaction_id'], $dispatcher);

        return redirect()->route('order.detail', $order->id)
            ->with('success', 'Your transfer details have been submitted. Our team will verify and confirm shortly.');
    }

    protected function authorizeOrder(Request $request, Order $order): void
    {
        if (! $request->user()) {
            abort(403);
        }
        // Owner of the order, OR an admin.
        if ($order->user_id !== $request->user()->id && $request->user()->role?->name !== 'admin') {
            abort(403);
        }
    }

    protected function sendPaymentConfirmedEmail(Order $order, EmailDispatcher $dispatcher): void
    {
        if (! $order->user) {
            return;
        }

        $dispatcher->send('payment.confirmed', $order->user, [
            'order_number' => $order->order_number,
            'amount' => number_format((float) $order->total, 2),
            'currency' => $order->currency ?? 'USD',
            'gateway' => ucfirst($order->gateway ?? 'manual'),
            'paid_at' => optional($order->paid_at)->format('M d, Y \a\t g:i A') ?? now()->format('M d, Y \a\t g:i A'),
            'order_url' => route('order.detail', $order->id),
        ]);
    }

    protected function sendBankTransferReceivedEmails(Order $order, string $transactionId, EmailDispatcher $dispatcher): void
    {
        if ($order->user) {
            $dispatcher->send('payment.received', $order->user, [
                'order_number' => $order->order_number,
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
                'transaction_id' => $transactionId,
                'order_url' => route('order.detail', $order->id),
            ]);
        }

        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            $dispatcher->send('payment.admin_received', $adminEmail, [
                'order_number' => $order->order_number,
                'customer_name' => $order->user?->name ?? '—',
                'customer_email' => $order->user?->email ?? '—',
                'amount' => number_format((float) $order->total, 2),
                'currency' => $order->currency ?? 'USD',
                'transaction_id' => $transactionId,
                'admin_url' => route('admin.orders.show', $order->id),
            ]);
        }
    }
}
