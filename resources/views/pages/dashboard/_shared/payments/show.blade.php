@php
    $isEmployer = str_starts_with((string) request()->route()?->getName(), 'employer.');
    $indexRoute = $isEmployer ? 'employer.payments' : 'user.payments';
    $receiptRoute = $isEmployer ? 'employer.payments.receipt' : 'user.payments.receipt';
    $meta = $payment->gateway_response ?? [];
@endphp

<div class="mb-4">
    <a href="{{ route($indexRoute) }}" class="text-sm text-[#6B7280] hover:text-[#073057] inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to {{ $isEmployer ? 'Payments' : 'My Payments' }}
    </a>
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Left: payment + order --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-widest text-[#9CA3AF]">Payment</p>
                    <h2 class="text-xl font-bold text-[#073057]">{{ money($payment->amount, $payment->currency ?? 'USD') }}</h2>
                    <p class="text-sm text-[#6B7280]">{{ \App\Support\PaymentTypeLabel::description($payment->order) }}</p>
                </div>
                <span class="text-sm px-3 py-1 rounded-full font-semibold
                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($payment->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                    {{ ucfirst($payment->status ?? 'N/A') }}
                </span>
            </div>

            <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                <div><dt class="text-[#9CA3AF]">Order number</dt><dd class="text-[#073057] font-semibold">{{ $payment->order?->order_number ?? '—' }}</dd></div>
                <div><dt class="text-[#9CA3AF]">Method</dt><dd class="text-[#374151] capitalize">{{ $payment->gateway ?? '—' }}</dd></div>
                <div><dt class="text-[#9CA3AF]">Transaction ID</dt><dd class="font-mono text-[#374151] break-all">{{ $payment->transaction_id ?? '—' }}</dd></div>
                <div><dt class="text-[#9CA3AF]">Date</dt><dd>{{ $payment->created_at?->format('M d, Y g:i A') }}</dd></div>
                @if ($payment->order?->paid_at)
                    <div><dt class="text-[#9CA3AF]">Paid at</dt><dd class="text-emerald-700 font-semibold">{{ $payment->order->paid_at->format('M d, Y g:i A') }}</dd></div>
                @endif
            </dl>

            {{-- Customer-safe gateway details --}}
            @if ($payment->gateway === 'paystack')
                @php
                    $verified = $meta['verified'] ?? null;
                    $auth = $verified['authorization'] ?? null;
                @endphp
                @if ($verified)
                    <div class="mt-4 pt-4 border-t border-[#F3F4F6]">
                        <p class="text-xs uppercase tracking-widest text-[#9CA3AF] mb-3">Card / channel</p>
                        <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                            @if (! empty($verified['channel']))<div><dt class="text-[#9CA3AF]">Channel</dt><dd class="text-[#374151] capitalize">{{ $verified['channel'] }}</dd></div>@endif
                            @if (! empty($auth['bank']))<div><dt class="text-[#9CA3AF]">Bank</dt><dd class="text-[#374151]">{{ $auth['bank'] }}</dd></div>@endif
                            @if (! empty($auth['card_type']))<div><dt class="text-[#9CA3AF]">Card type</dt><dd class="text-[#374151] capitalize">{{ $auth['card_type'] }}</dd></div>@endif
                            @if (! empty($auth['last4']))<div><dt class="text-[#9CA3AF]">Card</dt><dd class="font-mono text-[#374151]">**** {{ $auth['last4'] }}</dd></div>@endif
                        </dl>
                    </div>
                @endif
            @elseif ($payment->gateway === 'manual')
                <div class="mt-4 pt-4 border-t border-[#F3F4F6]">
                    <p class="text-xs uppercase tracking-widest text-[#9CA3AF] mb-3">Bank transfer</p>
                    <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                        @if (! empty($meta['paid_at']))<div><dt class="text-[#9CA3AF]">Date paid</dt><dd>{{ $meta['paid_at'] }}</dd></div>@endif
                        @if (! empty($meta['sender_bank']))<div><dt class="text-[#9CA3AF]">Sender bank</dt><dd>{{ $meta['sender_bank'] }}</dd></div>@endif
                        @if (! empty($meta['sender_account']))<div><dt class="text-[#9CA3AF]">Sender account</dt><dd>{{ $meta['sender_account'] }}</dd></div>@endif
                        @if (! empty($meta['proof_path']))
                            <div class="sm:col-span-2"><dt class="text-[#9CA3AF]">Proof of payment</dt><dd><a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($meta['proof_path']) }}" target="_blank" class="text-[#1AAD94] font-semibold">Download &rarr;</a></dd></div>
                        @endif
                        @if (! empty($meta['note']))<div class="sm:col-span-2"><dt class="text-[#9CA3AF]">Your note</dt><dd class="whitespace-pre-line">{{ $meta['note'] }}</dd></div>@endif
                        @if ($payment->status === 'failed' && ! empty($meta['rejection_reason']))
                            <div class="sm:col-span-2"><dt class="text-[#9CA3AF]">Reason rejected</dt><dd class="text-red-600">{{ $meta['rejection_reason'] }}</dd></div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>

        {{-- Order summary --}}
        @if ($payment->order && $payment->order->items->isNotEmpty())
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                <h3 class="text-base font-semibold text-[#073057] mb-4">Items</h3>
                <div class="border border-[#F3F4F6] rounded-lg overflow-hidden mb-4">
                    <table class="w-full text-sm">
                        <thead class="bg-[#F9FAFB]"><tr><th class="text-left px-4 py-2 text-[#9CA3AF]">Item</th><th class="text-right px-4 py-2 text-[#9CA3AF]">Qty</th><th class="text-right px-4 py-2 text-[#9CA3AF]">Price</th><th class="text-right px-4 py-2 text-[#9CA3AF]">Subtotal</th></tr></thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @foreach ($payment->order->items as $item)
                                @php
                                    $orderableType = class_basename($item->orderable_type ?? '');
                                    $itemDescription = match ($orderableType) {
                                        'TrainingProgram' => 'Training: ' . ($item->meta['program_title'] ?? 'Program'),
                                        'Event' => 'Event registration' . (! empty($item->meta['buyer_name']) ? ' — ' . $item->meta['buyer_name'] : ''),
                                        'Plan' => 'Premium membership' . (! empty($item->meta['billing_cycle']) ? ' (' . ucfirst($item->meta['billing_cycle']) . ')' : ''),
                                        'Candidate' => 'Profile boost' . (! empty($item->meta['days']) ? ' (' . $item->meta['days'] . ' days)' : ''),
                                        default => 'Service',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-[#374151]">{{ $itemDescription }}</td>
                                    <td class="px-4 py-3 text-right text-[#6B7280]">{{ $item->quantity ?? 1 }}</td>
                                    <td class="px-4 py-3 text-right text-[#6B7280]">{{ money($item->price ?? 0, $payment->order->currency ?? 'USD') }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ money($item->subtotal ?? 0, $payment->order->currency ?? 'USD') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-[#9CA3AF]">Subtotal</span><span>{{ money($payment->order->subtotal, $payment->order->currency ?? 'USD') }}</span></div>
                    <div class="flex justify-between"><span class="text-[#9CA3AF]">Tax</span><span>{{ money($payment->order->tax, $payment->order->currency ?? 'USD') }}</span></div>
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-[#F3F4F6] text-[#073057]"><span>Total</span><span>{{ money($payment->order->total, $payment->order->currency ?? 'USD') }}</span></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Right: receipt --}}
    <div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <h3 class="text-sm font-semibold text-[#073057] mb-4">Receipt</h3>
            @if ($payment->receipt)
                <dl class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between"><dt class="text-[#9CA3AF]">Number</dt><dd class="font-mono text-[#073057]">{{ $payment->receipt->number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#9CA3AF]">Issued</dt><dd>{{ $payment->receipt->issued_at?->format('M d, Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#9CA3AF]">Amount</dt><dd>{{ money($payment->receipt->amount, $payment->receipt->currency) }}</dd></div>
                </dl>
                <a href="{{ route($receiptRoute, $payment) }}" class="block w-full text-center px-4 py-2.5 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:brightness-110">
                    Download receipt PDF
                </a>
            @elseif ($payment->status === 'completed')
                <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-700 px-4 py-3 text-sm">
                    Your payment is confirmed. Your receipt is being prepared and will be available here shortly. If you need it urgently, please contact support.
                </div>
            @elseif ($payment->status === 'pending' || $payment->status === 'processing')
                <div class="rounded-lg border border-blue-200 bg-blue-50 text-blue-700 px-4 py-3 text-sm">
                    Once your payment is verified, your receipt will appear here for download.
                </div>
            @else
                <p class="text-sm text-[#9CA3AF]">No receipt available for this payment.</p>
            @endif
        </div>
    </div>
</div>
