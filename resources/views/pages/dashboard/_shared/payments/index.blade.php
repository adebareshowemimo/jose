@php
    $isEmployer = str_starts_with((string) request()->route()?->getName(), 'employer.');
    $showRoute = $isEmployer ? 'employer.payments.show' : 'user.payments.show';
    $receiptRoute = $isEmployer ? 'employer.payments.receipt' : 'user.payments.receipt';
    $indexRoute = $isEmployer ? 'employer.payments' : 'user.payments';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#073057]">{{ $isEmployer ? 'Payments & Receipts' : 'My Payments' }}</h2>
    <p class="text-[#6B7280]">View your payment history, download receipts, and check the status of any pending bank transfers.</p>
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
@endif

{{-- Status filters --}}
<div class="flex flex-wrap gap-2 mb-6">
    @php
        $statusCounts = [
            'all' => $payments->total(),
        ];
    @endphp
    <a href="{{ route($indexRoute) }}" class="px-4 py-2 text-sm font-medium rounded-lg border {{ ! request('status') ? 'bg-[#1AAD94] text-white border-[#1AAD94]' : 'bg-white border-[#E5E7EB] text-[#4B5563] hover:bg-[#F9FAFB]' }}">All</a>
    @foreach (['completed' => 'Completed', 'pending' => 'Pending', 'processing' => 'Processing', 'failed' => 'Failed', 'refunded' => 'Refunded'] as $key => $label)
        <a href="{{ route($indexRoute, ['status' => $key]) }}" class="px-4 py-2 text-sm font-medium rounded-lg border {{ request('status') === $key ? 'bg-[#1AAD94] text-white border-[#1AAD94]' : 'bg-white border-[#E5E7EB] text-[#4B5563] hover:bg-[#F9FAFB]' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
    @if ($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-[#073057]">Order</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#073057]">Description</th>
                        <th class="text-right px-5 py-3 font-semibold text-[#073057]">Amount</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#073057]">Method</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#073057]">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#073057]">Date</th>
                        <th class="text-right px-5 py-3 font-semibold text-[#073057]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3F4F6]">
                    @foreach ($payments as $payment)
                        <tr class="hover:bg-[#F9FAFB]">
                            <td class="px-5 py-3 font-mono text-xs text-[#073057]">{{ $payment->order?->order_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-[#374151]">{{ \App\Support\PaymentTypeLabel::description($payment->order) }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-[#073057]">{{ money($payment->amount, $payment->currency ?? 'USD') }}</td>
                            <td class="px-5 py-3 text-[#6B7280] capitalize">{{ $payment->gateway ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($payment->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($payment->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-[#6B7280]">{{ $payment->created_at?->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route($showRoute, $payment) }}" class="px-3 py-1.5 text-xs font-semibold text-[#073057] hover:bg-[#073057] hover:text-white border border-[#073057]/20 rounded transition">Details</a>
                                @if ($payment->status === 'completed' && $payment->receipt)
                                    <a href="{{ route($receiptRoute, $payment) }}" class="px-3 py-1.5 text-xs font-semibold bg-[#1AAD94] text-white rounded hover:brightness-110">Receipt</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="px-5 py-3 border-t border-[#E5E7EB]">{{ $payments->links() }}</div>
        @endif
    @else
        <div class="px-5 py-16 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-[#F3F4F6] mb-4">
                <svg class="w-7 h-7 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m4 0h4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
            </div>
            <p class="text-[#374151] font-semibold">No payments yet</p>
            <p class="text-[#6B7280] text-sm mt-1">When you make a payment, it will appear here with a downloadable receipt.</p>
        </div>
    @endif
</div>
