<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Services\ReceiptPdfService;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function store(Payment $payment)
    {
        if (! $payment->isReceiptable()) {
            return back()->with('error', 'A receipt can only be issued for a completed payment.');
        }
        $payment->load('order.user');
        if ($payment->receipt) {
            return back()->with('error', 'A receipt for this payment already exists.');
        }

        $order = $payment->order;
        if (! $order || ! $order->user_id) {
            return back()->with('error', 'Cannot issue a receipt — order or customer is missing.');
        }

        $receipt = Receipt::create([
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'number' => Receipt::nextNumberFor($payment),
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'USD',
            'issued_at' => now(),
            'issued_by_admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', "Receipt {$receipt->number} issued.");
    }

    public function show(Receipt $receipt, ReceiptPdfService $pdfService)
    {
        return $pdfService->render($receipt)->stream($pdfService->filename($receipt));
    }

    public function download(Receipt $receipt, ReceiptPdfService $pdfService)
    {
        return $pdfService->render($receipt)->download($pdfService->filename($receipt));
    }

    public function update(Request $request, Receipt $receipt)
    {
        $data = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $receipt->update($data);

        return back()->with('success', 'Receipt notes updated.');
    }

    public function destroy(Receipt $receipt)
    {
        $paymentId = $receipt->payment_id;
        $receipt->delete();

        return redirect()->route('admin.payments.show', $paymentId)
            ->with('success', 'Receipt deleted.');
    }

    public function email(Receipt $receipt, ReceiptPdfService $pdfService, EmailDispatcher $dispatcher)
    {
        $receipt->loadMissing('payment.order.user');
        $user = $receipt->payment?->order?->user;
        if (! $user || ! $user->email) {
            return back()->with('error', 'Customer email not found for this receipt.');
        }

        $pdfBinary = $pdfService->render($receipt)->output();
        $filename = $pdfService->filename($receipt);

        $sent = $dispatcher->send(
            'receipt.sent',
            $user,
            [
                'order_number' => $receipt->payment->order->order_number,
                'receipt_number' => $receipt->number,
                'amount' => number_format((float) $receipt->amount, 2),
                'currency' => $receipt->currency,
                'issued_at' => optional($receipt->issued_at)->format('M d, Y \a\t g:i A') ?? '',
                'download_url' => $this->customerReceiptUrl($user, $receipt->payment_id),
            ],
            null,
            [['name' => $filename, 'data' => $pdfBinary, 'mime' => 'application/pdf']],
        );

        if (! $sent) {
            return back()->with('error', 'Could not send the receipt email. Check email logs for details.');
        }

        $receipt->update([
            'last_emailed_at' => now(),
            'last_emailed_to' => $user->email,
        ]);

        return back()->with('success', "Receipt {$receipt->number} emailed to {$user->email}.");
    }

    protected function customerReceiptUrl(User $user, int $paymentId): string
    {
        $user->loadMissing(['company', 'candidate']);
        if ($user->company) {
            return route('employer.payments.receipt', $paymentId);
        }
        return route('user.payments.receipt', $paymentId);
    }
}
