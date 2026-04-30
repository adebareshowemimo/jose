@extends('admin.layouts.app')

@section('title', 'Payment #' . $payment->id)
@section('page-title', 'Payment Details')

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="mb-4 flex items-center justify-between gap-4">
        <a href="{{ route('admin.payments') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.payments.edit', $payment) }}" class="px-3 py-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm rounded-md font-semibold">Edit</a>
            <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" onsubmit="return confirm('Soft-delete this payment? It can be recovered later.');">
                @csrf @method('DELETE')
                @if ($payment->status === 'completed')
                    <input type="hidden" name="force" value="1">
                @endif
                <button class="px-3 py-1.5 border border-red-300 text-red-600 hover:bg-red-50 text-sm rounded-md font-semibold">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Left: payment + order --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Payment #{{ $payment->id }}</h2>
                        <p class="text-sm text-gray-500">Created {{ $payment->created_at?->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <span class="text-sm px-3 py-1 rounded-full font-semibold
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($payment->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                        {{ ucfirst($payment->status ?? 'N/A') }}
                    </span>
                </div>

                <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-gray-500">Gateway</dt><dd class="text-gray-900 font-medium">{{ ucfirst($payment->gateway ?? '—') }}</dd></div>
                    <div><dt class="text-gray-500">Transaction ID</dt><dd class="font-mono text-gray-900 break-all">{{ $payment->transaction_id ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Amount</dt><dd class="text-gray-900 font-semibold">{{ money($payment->amount, $payment->currency ?? 'USD') }}</dd></div>
                    <div><dt class="text-gray-500">Type</dt><dd class="text-gray-900">{{ \App\Support\PaymentTypeLabel::description($payment->order) }}</dd></div>
                    <div><dt class="text-gray-500">Order</dt><dd><a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-[#1AAD94] hover:underline">#{{ $payment->order?->order_number }}</a></dd></div>
                    <div><dt class="text-gray-500">Order paid at</dt><dd class="text-gray-900">{{ $payment->order?->paid_at?->format('M d, Y g:i A') ?? '—' }}</dd></div>
                </dl>

                {{-- Gateway-specific details --}}
                @if ($payment->gateway === 'paystack')
                    @include('admin.payments._partials.paystack-details', ['payment' => $payment])
                @elseif ($payment->gateway === 'manual')
                    @include('admin.payments._partials.manual-details', ['payment' => $payment])
                @endif

                {{-- Verify / reject for pending manual payments --}}
                @if ($payment->gateway === 'manual' && $payment->status === 'pending')
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('admin.orders.payments.verify', [$payment->order, $payment]) }}" onsubmit="return confirm('Verify this payment? Order will be marked as completed.');">
                            @csrf
                            <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#1AAD94] text-white rounded-md text-xs font-semibold hover:brightness-110">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Verify &amp; complete
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.payments.reject', [$payment->order, $payment]) }}" onsubmit="return confirm('Reject this payment? Order will return to pending.');" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="reason" placeholder="Reason (optional)" class="text-xs px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-red-400" />
                            <button class="px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 rounded-md text-xs font-semibold">Reject</button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Order summary --}}
            @if ($payment->order)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900">Order #{{ $payment->order->order_number }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $payment->order->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($payment->order->status ?? 'N/A') }}
                        </span>
                    </div>

                    @if ($payment->order->items->isNotEmpty())
                        <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                            <table class="w-full text-sm">
                                <thead><tr class="bg-gray-50"><th class="text-left px-4 py-2 text-gray-500">Item</th><th class="text-right px-4 py-2 text-gray-500">Qty</th><th class="text-right px-4 py-2 text-gray-500">Price</th><th class="text-right px-4 py-2 text-gray-500">Total</th></tr></thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($payment->order->items as $item)
                                        <tr>
                                            <td class="px-4 py-3 text-gray-900">{{ class_basename($item->orderable_type ?? 'Item') }}{{ ! empty($item->meta['program_title']) ? ' — ' . $item->meta['program_title'] : '' }}{{ ! empty($item->meta['days']) ? ' (' . $item->meta['days'] . ' days)' : '' }}{{ ! empty($item->meta['billing_cycle']) ? ' (' . $item->meta['billing_cycle'] . ')' : '' }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600">{{ $item->quantity ?? 1 }}</td>
                                            <td class="px-4 py-3 text-right text-gray-600">{{ money($item->price ?? 0, $payment->order->currency ?? 'USD') }}</td>
                                            <td class="px-4 py-3 text-right font-medium">{{ money($item->subtotal ?? 0, $payment->order->currency ?? 'USD') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="text-gray-900">{{ money($payment->order->subtotal, $payment->order->currency ?? 'USD') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tax</span><span class="text-gray-900">{{ money($payment->order->tax, $payment->order->currency ?? 'USD') }}</span></div>
                        <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200"><span>Total</span><span>{{ money($payment->order->total, $payment->order->currency ?? 'USD') }}</span></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: customer + receipt panel --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Customer</h3>
                @if ($payment->order?->user)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-[#073057] rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($payment->order->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->order->user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $payment->order->user) }}" class="text-sm text-[#1AAD94] hover:underline">View user profile &rarr;</a>
                @else
                    <p class="text-sm text-gray-400">Customer not found.</p>
                @endif
            </div>

            {{-- Receipt panel --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Receipt</h3>
                @if ($payment->receipt)
                    @php $receipt = $payment->receipt; @endphp
                    <dl class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between"><dt class="text-gray-500">Number</dt><dd class="font-mono text-gray-900">{{ $receipt->number }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Issued</dt><dd>{{ $receipt->issued_at?->format('M d, Y g:i A') }}</dd></div>
                        @if ($receipt->issuedBy)
                            <div class="flex justify-between"><dt class="text-gray-500">Issued by</dt><dd>{{ $receipt->issuedBy->name }}</dd></div>
                        @endif
                        @if ($receipt->last_emailed_at)
                            <div class="flex justify-between"><dt class="text-gray-500">Last emailed</dt><dd>{{ $receipt->last_emailed_at->format('M d, Y g:i A') }}</dd></div>
                            <div class="flex justify-between text-xs"><dt class="text-gray-400">to</dt><dd class="text-gray-500">{{ $receipt->last_emailed_to }}</dd></div>
                        @endif
                    </dl>

                    <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.receipts.show', $receipt) }}" target="_blank" class="px-3 py-1.5 bg-[#073057] text-white text-xs font-semibold rounded hover:brightness-110">View PDF</a>
                        <a href="{{ route('admin.receipts.download', $receipt) }}" class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded hover:bg-gray-50">Download</a>
                        <form method="POST" action="{{ route('admin.receipts.email', $receipt) }}" class="inline" onsubmit="return confirm('Email this receipt to {{ $payment->order?->user?->email }}?');">
                            @csrf
                            <button class="px-3 py-1.5 bg-[#1AAD94] text-white text-xs font-semibold rounded hover:brightness-110">Email to customer</button>
                        </form>
                        <form method="POST" action="{{ route('admin.receipts.destroy', $receipt) }}" class="inline" onsubmit="return confirm('Delete this receipt? The customer will lose download access.');">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 border border-red-200 text-red-600 text-xs font-semibold rounded hover:bg-red-50">Delete</button>
                        </form>
                    </div>

                    {{-- Notes editor --}}
                    <form method="POST" action="{{ route('admin.receipts.update', $receipt) }}" class="mt-4 pt-4 border-t border-gray-100">
                        @csrf @method('PUT')
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Notes (printed on PDF)</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ old('notes', $receipt->notes) }}</textarea>
                        <button class="mt-2 px-3 py-1.5 bg-[#073057] text-white text-xs font-semibold rounded hover:brightness-110">Save notes</button>
                    </form>
                @elseif ($payment->status === 'completed')
                    <p class="text-sm text-gray-500 mb-4">No receipt has been issued for this payment.</p>
                    <form method="POST" action="{{ route('admin.receipts.store', $payment) }}">
                        @csrf
                        <button class="w-full px-3 py-2 bg-[#1AAD94] text-white text-sm font-semibold rounded hover:brightness-110">Generate receipt</button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">A receipt can be issued once the payment status is <strong>completed</strong>.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
