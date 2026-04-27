<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Services\PaystackService;
use App\Support\Settings;
use Illuminate\Http\Request;

class TransactionPageController extends BasePageController
{
    public function checkout()
    {
        return $this->renderTransactionPage(
            'pages.transaction.checkout',
            'Checkout',
            'Finalize your purchase securely.',
            ['Order Value' => '$129.00', 'Payment Method' => 'Card', 'Tax' => '$0.00'],
            [
                ['Professional Employer Plan', '1', '$129.00'],
                ['Priority Listing Add-on', '1', '$0.00'],
            ],
            ['Item', 'Qty', 'Amount']
        );
    }

    public function cart()
    {
        return $this->renderTransactionPage(
            'pages.transaction.cart',
            'Cart',
            'Review selected items before checkout.',
            ['Items' => '2', 'Subtotal' => '$129.00', 'Coupon' => 'SPRING26'],
            [
                ['Professional Employer Plan', '1', '$129.00'],
                ['Candidate Spotlight', '1', '$0.00'],
            ],
            ['Product', 'Qty', 'Price']
        );
    }

    public function orderDetail(Request $request, string $id, PaystackService $paystack, Settings $settings)
    {
        $order = Order::with(['items.orderable', 'payments' => fn ($q) => $q->latest(), 'user'])->findOrFail($id);

        // Authorization: order owner or admin only.
        $user = $request->user();
        if (! $user || ($order->user_id !== $user->id && $user->role?->name !== 'admin')) {
            abort(403);
        }

        return view('pages.transaction.order-detail', [
            'order' => $order,
            'paystackEnabled' => $paystack->isConfigured(),
            'paystackPublicKey' => $paystack->publicKey(),
            'bank' => $settings->group('bank'),
            'pageTitle' => 'Order #' . $order->order_number,
            'pageDescription' => 'Order summary and payment options.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Order #' . $order->order_number],
            ],
        ]);
    }

    public function orderHistory()
    {
        return $this->renderTransactionPage(
            'pages.transaction.order-history',
            'Order History',
            'Browse your previous orders.',
            ['Orders This Year' => '12', 'Total Spend' => '$1,428.00', 'Outstanding' => '$0.00'],
            [
                ['#ORD-8932', 'Professional Plan', '$129.00'],
                ['#ORD-8770', 'Candidate Search Credit', '$99.00'],
                ['#ORD-8614', 'Premium Listing', '$49.00'],
            ],
            ['Order', 'Description', 'Amount']
        );
    }

    public function bookingCheckout(string $code)
    {
        return $this->renderTransactionPage(
            'pages.transaction.booking-checkout',
            'Booking Checkout',
            "Complete checkout for booking code {$code}.",
            ['Booking Code' => strtoupper($code), 'Status' => 'Pending Payment', 'Currency' => 'USD'],
            [
                ['Booking Reservation', '1', '$249.00'],
                ['Processing Fee', '1', '$0.00'],
            ],
            ['Item', 'Qty', 'Amount']
        );
    }

    public function invoice(string $code)
    {
        return $this->renderTransactionPage(
            'pages.transaction.invoice',
            'Invoice',
            "Invoice for booking {$code}.",
            ['Invoice Code' => strtoupper($code), 'Status' => 'Paid', 'Issued' => 'Mar 27, 2026'],
            [
                ['Booking Service', 'Completed', '$249.00'],
                ['VAT', 'Exempt', '$0.00'],
            ],
            ['Description', 'Status', 'Amount']
        );
    }

    public function orderComplete()
    {
        return $this->renderTransactionPage(
            'pages.transaction.order-complete',
            'Order Complete',
            'Your order has been completed successfully.',
            ['Confirmation' => 'Sent by Email', 'Activation' => 'Immediate', 'Support' => '24/7'],
            [
                ['Order Confirmation', 'Delivered', 'Inbox + dashboard'],
                ['Subscription Activation', 'Active', 'Ready to use'],
            ],
            ['Milestone', 'Status', 'Details']
        );
    }

    private function renderTransactionPage(
        string $view,
        string $title,
        string $description,
        array $summary,
        array $rows,
        array $headers
    ) {
        return view($view, [
            'pageTitle' => $title,
            'pageDescription' => $description,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Transactions'],
                ['label' => $title],
            ],
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows,
        ]);
    }
}
