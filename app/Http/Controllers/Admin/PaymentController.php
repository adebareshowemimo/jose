<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Currency;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.user', 'order.items', 'receipt']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where(function ($q) use ($term) {
                $q->where('transaction_id', 'like', $term)
                  ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', $term))
                  ->orWhereHas('order.user', fn ($u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term));
            });
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        $totalRevenue    = $this->sumPaymentsInDefault(Payment::where('status', 'completed'));
        $pendingPayments = $this->sumPaymentsInDefault(Payment::where('status', 'pending'));
        $gateways = Payment::query()->select('gateway')->distinct()->orderBy('gateway')->pluck('gateway')->all();

        return view('admin.payments.index', compact('payments', 'totalRevenue', 'pendingPayments', 'gateways'));
    }

    /**
     * Sum a Payment query in the site's default currency.
     * Prefers the per-row stamped exchange_rate; otherwise falls back to the current rate.
     */
    protected function sumPaymentsInDefault($query): float
    {
        $default = Currency::default();
        $total = 0.0;

        $query->select('amount', 'currency', 'exchange_rate')
            ->cursor()
            ->each(function ($row) use (&$total, $default) {
                $amount = (float) $row->amount;
                $currency = strtoupper((string) ($row->currency ?? $default));
                if ($currency === $default) {
                    $total += $amount;
                    return;
                }
                $stamped = (float) ($row->exchange_rate ?? 0);
                $rate = $stamped > 0 && $stamped !== 1.0
                    ? $stamped
                    : Currency::rate($currency, $default);
                $total += $amount * $rate;
            });

        return round($total, 2);
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.items', 'receipt.issuedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load('order.user');
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment, EmailDispatcher $dispatcher)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,refunded',
            'transaction_id' => 'nullable|string|max:100',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $becomingCompleted = $data['status'] === 'completed' && $payment->status !== 'completed';

        $gatewayResponse = $payment->gateway_response ?? [];
        if (! empty($data['admin_note'])) {
            $gatewayResponse['admin_notes'] = array_merge(
                $gatewayResponse['admin_notes'] ?? [],
                [[
                    'note' => $data['admin_note'],
                    'by' => auth()->id(),
                    'at' => now()->toIso8601String(),
                ]]
            );
        }
        if ($payment->status !== $data['status']) {
            $gatewayResponse['status_history'] = array_merge(
                $gatewayResponse['status_history'] ?? [],
                [[
                    'from' => $payment->status,
                    'to' => $data['status'],
                    'by' => auth()->id(),
                    'at' => now()->toIso8601String(),
                ]]
            );
        }

        $updates = [
            'status' => $data['status'],
            'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
            'gateway_response' => $gatewayResponse,
        ];
        if ($becomingCompleted) {
            $updates['exchange_rate'] = Currency::rate($payment->currency ?? 'USD', Currency::default());
        }
        $payment->update($updates);

        if ($becomingCompleted) {
            $order = $payment->order;
            if ($order) {
                $order->update([
                    'status' => 'completed',
                    'paid_at' => $order->paid_at ?? now(),
                ]);
                $this->notifyPaymentConfirmed($order, $dispatcher);
            }
        }

        return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment updated.');
    }

    public function destroy(Request $request, Payment $payment)
    {
        if ($payment->status === 'completed' && ! $request->boolean('force')) {
            return back()->with('error', 'Cannot delete a completed payment without confirmation.');
        }

        $payment->delete();

        return redirect()->route('admin.payments')->with('success', 'Payment deleted.');
    }

    public function verify(Request $request, Order $order, Payment $payment, EmailDispatcher $dispatcher)
    {
        if ($payment->order_id !== $order->id) {
            abort(404);
        }
        if ($payment->status === 'completed') {
            return back()->with('error', 'This payment is already verified.');
        }

        $payment->update([
            'status' => 'completed',
            'exchange_rate' => Currency::rate($payment->currency ?? 'USD', Currency::default()),
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'verified_by_admin_id' => auth()->id(),
                'verified_at' => now()->toIso8601String(),
            ]),
        ]);
        $order->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->notifyPaymentConfirmed($order, $dispatcher);

        return back()->with('success', 'Payment verified. Order marked as completed. Customer notified by email.');
    }

    protected function notifyPaymentConfirmed(Order $order, EmailDispatcher $dispatcher): void
    {
        $order->loadMissing('user');
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

    public function reject(Request $request, Order $order, Payment $payment)
    {
        if ($payment->order_id !== $order->id) {
            abort(404);
        }
        $reason = $request->validate(['reason' => 'nullable|string|max:500'])['reason'] ?? null;

        $payment->update([
            'status' => 'failed',
            'gateway_response' => array_merge($payment->gateway_response ?? [], [
                'rejected_by_admin_id' => auth()->id(),
                'rejected_at' => now()->toIso8601String(),
                'rejection_reason' => $reason,
            ]),
        ]);
        $order->update(['status' => 'pending']);

        return back()->with('success', 'Payment rejected. Order returned to pending so the customer can retry.');
    }
}
