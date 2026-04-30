<?php

namespace App\Services;

use App\Models\Receipt;
use App\Support\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;

class ReceiptPdfService
{
    public function render(Receipt $receipt): DomPDF
    {
        $receipt->loadMissing(['payment.order.user', 'payment.order.items', 'issuedBy']);

        $template = app(Settings::class)->group('receipt');

        return Pdf::loadView('admin.receipts.pdf', [
            'receipt' => $receipt,
            'payment' => $receipt->payment,
            'order' => $receipt->payment?->order,
            'customer' => $receipt->payment?->order?->user,
            'template' => $template,
        ])->setPaper('a4');
    }

    public function filename(Receipt $receipt): string
    {
        return 'receipt-' . preg_replace('/[^A-Za-z0-9_-]/', '-', $receipt->number) . '.pdf';
    }
}
