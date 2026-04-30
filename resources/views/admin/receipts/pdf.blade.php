<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $receipt->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
        .page { padding: 32px 40px; }
        .header { width: 100%; margin-bottom: 28px; }
        .header td { vertical-align: top; padding: 0; }
        .business-name { font-size: 18px; font-weight: bold; color: #073057; }
        .business-meta { font-size: 10px; color: #6b7280; line-height: 1.5; }
        .doc-title { font-size: 22px; font-weight: bold; color: #073057; text-align: right; letter-spacing: 1px; }
        .doc-meta { font-size: 10px; color: #6b7280; text-align: right; line-height: 1.7; margin-top: 6px; }
        .doc-meta .label { color: #9ca3af; display: inline-block; min-width: 90px; }
        .doc-meta .value { color: #111827; font-weight: 600; }
        .header-note { background: #f9fafb; border-left: 3px solid #1AAD94; padding: 10px 14px; margin-bottom: 20px; font-size: 10px; color: #4b5563; }
        .blocks { width: 100%; margin-bottom: 22px; }
        .blocks td { vertical-align: top; padding: 0; width: 50%; }
        .block-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; font-weight: bold; margin-bottom: 6px; }
        .block-content { font-size: 11px; color: #111827; line-height: 1.6; }
        .block-content strong { color: #073057; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .items th { background: #073057; color: #fff; text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        .items th.right, .items td.right { text-align: right; }
        .items td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        .totals { width: 50%; margin-left: 50%; font-size: 11px; }
        .totals td { padding: 6px 12px; }
        .totals .label { color: #6b7280; text-align: right; }
        .totals .value { text-align: right; font-weight: 600; color: #111827; }
        .totals tr.grand td { border-top: 2px solid #073057; padding-top: 10px; font-size: 13px; color: #073057; font-weight: bold; }
        .stamp { margin-top: 30px; padding: 12px 16px; background: #ecfdf5; border: 1px dashed #10b981; color: #065f46; font-size: 11px; font-weight: bold; text-align: center; letter-spacing: 1px; text-transform: uppercase; }
        .notes { margin-top: 24px; padding: 12px 14px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 10px; color: #78350f; line-height: 1.5; }
        .footer { margin-top: 36px; border-top: 1px solid #e5e7eb; padding-top: 16px; font-size: 10px; color: #6b7280; line-height: 1.6; }
        .signature { margin-top: 50px; width: 220px; border-top: 1px solid #9ca3af; padding-top: 6px; font-size: 10px; color: #6b7280; text-align: center; }
        .pill { display: inline-block; padding: 3px 10px; background: #ecfdf5; color: #065f46; border-radius: 999px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>
<div class="page">
    {{-- Header --}}
    <table class="header">
        <tr>
            <td style="width: 60%;">
                @if (! empty($template['receipt.logo_path']))
                    <img src="{{ public_path('storage/' . $template['receipt.logo_path']) }}" alt="Logo" style="max-height: 56px; margin-bottom: 8px;">
                @endif
                <div class="business-name">{{ $template['receipt.business_name'] ?? config('app.name') }}</div>
                <div class="business-meta">
                    @if (! empty($template['receipt.business_address']))<div>{!! nl2br(e($template['receipt.business_address'])) !!}</div>@endif
                    @if (! empty($template['receipt.business_phone']))<div>Tel: {{ $template['receipt.business_phone'] }}</div>@endif
                    @if (! empty($template['receipt.business_email']))<div>Email: {{ $template['receipt.business_email'] }}</div>@endif
                    @if (! empty($template['receipt.tax_id']))<div>Tax ID: {{ $template['receipt.tax_id'] }}</div>@endif
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="doc-title">RECEIPT</div>
                <div class="doc-meta">
                    <div><span class="label">Receipt No:</span> <span class="value">{{ $receipt->number }}</span></div>
                    <div><span class="label">Issued:</span> <span class="value">{{ $receipt->issued_at?->format('M d, Y') }}</span></div>
                    @if ($order)
                        <div><span class="label">Order No:</span> <span class="value">{{ $order->order_number }}</span></div>
                    @endif
                    @if ($order && $order->paid_at)
                        <div><span class="label">Paid On:</span> <span class="value">{{ $order->paid_at->format('M d, Y') }}</span></div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @if (! empty($template['receipt.header_note']))
        <div class="header-note">{!! nl2br(e($template['receipt.header_note'])) !!}</div>
    @endif

    {{-- Customer + Payment blocks --}}
    <table class="blocks">
        <tr>
            <td style="padding-right: 16px;">
                <div class="block-label">Billed to</div>
                <div class="block-content">
                    @if ($customer)
                        <strong>{{ $customer->name }}</strong><br>
                        {{ $customer->email }}
                        @if (! empty($order?->billing_info))
                            @foreach ($order->billing_info as $key => $value)
                                <br><span style="color: #6b7280;">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}:</span> {{ $value }}
                            @endforeach
                        @endif
                    @endif
                </div>
            </td>
            <td style="padding-left: 16px;">
                <div class="block-label">Payment</div>
                <div class="block-content">
                    <strong>{{ ucfirst($payment?->gateway ?? 'Manual') }}</strong>
                    @if ($payment?->transaction_id)
                        <br><span style="color: #6b7280;">Transaction:</span> {{ $payment->transaction_id }}
                    @endif
                    <br><span class="pill">Paid</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if ($order && $order->items->isNotEmpty())
                @foreach ($order->items as $item)
                    @php
                        $orderableType = class_basename($item->orderable_type ?? '');
                        $itemDescription = match ($orderableType) {
                            'TrainingProgram' => 'Training: ' . ($item->meta['program_title'] ?? 'Program'),
                            'Event' => 'Event Registration' . (! empty($item->meta['buyer_name']) ? ' — ' . $item->meta['buyer_name'] : ''),
                            'Plan' => 'Premium Membership' . (! empty($item->meta['billing_cycle']) ? ' (' . ucfirst($item->meta['billing_cycle']) . ')' : ''),
                            'Candidate' => 'Profile Boost' . (! empty($item->meta['days']) ? ' (' . $item->meta['days'] . ' days)' : ''),
                            default => 'Service',
                        };
                    @endphp
                    <tr>
                        <td>{{ $itemDescription }}</td>
                        <td class="right">{{ $item->quantity ?? 1 }}</td>
                        <td class="right">{{ money($item->price ?? 0, $order->currency ?? 'USD') }}</td>
                        <td class="right">{{ money($item->subtotal ?? (($item->price ?? 0) * ($item->quantity ?? 1)), $order->currency ?? 'USD') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ \App\Support\PaymentTypeLabel::for($order) }}</td>
                    <td class="right">1</td>
                    <td class="right">{{ money($payment->amount, $payment->currency ?? 'USD') }}</td>
                    <td class="right">{{ money($payment->amount, $payment->currency ?? 'USD') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals">
        @if ($order)
            <tr><td class="label">Subtotal</td><td class="value">{{ money($order->subtotal, $order->currency ?? 'USD') }}</td></tr>
            @if ((float) $order->tax > 0)
                <tr><td class="label">Tax</td><td class="value">{{ money($order->tax, $order->currency ?? 'USD') }}</td></tr>
            @endif
            <tr class="grand"><td class="label">Total Paid</td><td class="value">{{ money($order->total, $order->currency ?? 'USD') }}</td></tr>
        @else
            <tr class="grand"><td class="label">Total Paid</td><td class="value">{{ money($receipt->amount, $receipt->currency ?? 'USD') }}</td></tr>
        @endif
    </table>

    <div class="stamp">Payment received — Thank you</div>

    @if (! empty($receipt->notes))
        <div class="notes"><strong>Notes:</strong> {!! nl2br(e($receipt->notes)) !!}</div>
    @endif

    {{-- Signature --}}
    @if (! empty($template['receipt.signature_label']))
        <div class="signature">{{ $template['receipt.signature_label'] }}</div>
    @endif

    {{-- Footer --}}
    @if (! empty($template['receipt.footer_text']))
        <div class="footer">{!! nl2br(e($template['receipt.footer_text'])) !!}</div>
    @else
        <div class="footer">This is a computer-generated receipt and does not require a signature. Thank you for your business.</div>
    @endif
</div>
</body>
</html>
