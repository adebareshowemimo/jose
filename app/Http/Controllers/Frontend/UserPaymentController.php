<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Receipt;
use App\Services\ReceiptPdfService;
use Illuminate\Http\Request;

class UserPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()
            ->with(['order.items', 'receipt'])
            ->whereHas('order', fn ($o) => $o->where('user_id', $request->user()->id));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view($this->viewPath('index'), compact('payments'));
    }

    public function show(Request $request, Payment $payment)
    {
        $this->authorize($request, $payment);
        $payment->load(['order.items', 'receipt']);

        // Opening the payment clears the sidebar "payments & receipts" badge for
        // this payment and its receipt.
        $this->markPaymentViewed($payment);
        if ($payment->receipt) {
            $this->markReceiptViewed($payment->receipt);
        }

        return view($this->viewPath('show'), compact('payment'));
    }

    public function downloadReceipt(Request $request, Payment $payment, ReceiptPdfService $pdfService)
    {
        $this->authorize($request, $payment);
        $payment->loadMissing('receipt');

        if (! $payment->receipt) {
            return redirect()->route($this->routeName('show'), $payment)
                ->with('error', 'Receipt not yet available. Please contact support if you need a copy urgently.');
        }

        // Downloading the receipt counts as viewing it.
        $this->markReceiptViewed($payment->receipt);

        return $pdfService->render($payment->receipt)
            ->download($pdfService->filename($payment->receipt));
    }

    protected function markPaymentViewed(Payment $payment): void
    {
        $unseen = is_null($payment->employer_viewed_at)
            || $payment->employer_viewed_at->lt($payment->updated_at);
        if ($unseen) {
            Payment::whereKey($payment->getKey())
                ->toBase()->update(['employer_viewed_at' => now()]);
            $payment->employer_viewed_at = now();
        }
    }

    protected function markReceiptViewed(Receipt $receipt): void
    {
        if (is_null($receipt->employer_viewed_at)) {
            Receipt::whereKey($receipt->getKey())
                ->toBase()->update(['employer_viewed_at' => now()]);
            $receipt->employer_viewed_at = now();
        }
    }

    protected function authorize(Request $request, Payment $payment): void
    {
        $payment->loadMissing('order');
        if (! $payment->order || $payment->order->user_id !== $request->user()->id) {
            abort(404);
        }
    }

    protected function isEmployerContext(): bool
    {
        return str_starts_with((string) request()->route()?->getName(), 'employer.');
    }

    protected function viewPath(string $name): string
    {
        $base = $this->isEmployerContext()
            ? 'pages.dashboard.employer.payments'
            : 'pages.dashboard.candidate.payments';
        return "{$base}.{$name}";
    }

    protected function routeName(string $action): string
    {
        $prefix = $this->isEmployerContext() ? 'employer.payments' : 'user.payments';
        return $action === 'index' ? $prefix : "{$prefix}.{$action}";
    }
}
