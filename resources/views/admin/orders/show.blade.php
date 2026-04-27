@extends('admin.layouts.app')

@section('title', 'Order #' . $order->order_number)
@section('page-title', 'Order Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.orders') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Orders
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Order Summary --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500">{{ $order->created_at?->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <span class="text-sm px-3 py-1 rounded-full
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($order->status ?? 'N/A') }}
                    </span>
                </div>

                {{-- Items --}}
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Items</h3>
                @if($order->items->isNotEmpty())
                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50"><th class="text-left px-4 py-2 text-gray-500">Item</th><th class="text-right px-4 py-2 text-gray-500">Qty</th><th class="text-right px-4 py-2 text-gray-500">Price</th><th class="text-right px-4 py-2 text-gray-500">Total</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900">{{ $item->name ?? $item->object_model ?? 'Item' }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">{{ $item->qty ?? 1 }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">${{ number_format($item->price ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-medium">${{ number_format(($item->price ?? 0) * ($item->qty ?? 1), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 mb-6">No items recorded.</p>
                @endif

                {{-- Totals --}}
                <div class="border-t border-gray-200 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tax</span><span class="text-gray-900">${{ number_format($order->tax, 2) }}</span></div>
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>

            {{-- Payments --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Payments</h3>
                @if($order->payments->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($order->payments as $payment)
                            @php $meta = $payment->gateway_response ?? []; @endphp
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($payment->gateway ?? 'N/A') }} <span class="text-xs text-gray-400 font-mono">{{ $payment->transaction_id ?? '—' }}</span></p>
                                        <p class="text-xs text-gray-500">{{ $payment->created_at?->format('M d, Y g:i A') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900">{{ $payment->currency ?? 'USD' }} {{ number_format($payment->amount, 2) }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ ucfirst($payment->status ?? 'N/A') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Manual transfer details --}}
                                @if ($payment->gateway === 'manual')
                                    <dl class="grid sm:grid-cols-2 gap-2 text-xs mt-3 pt-3 border-t border-gray-100">
                                        @if (! empty($meta['paid_at']))<div><dt class="text-gray-500">Date paid</dt><dd class="text-gray-900">{{ $meta['paid_at'] }}</dd></div>@endif
                                        @if (! empty($meta['sender_bank']))<div><dt class="text-gray-500">Sender bank</dt><dd class="text-gray-900">{{ $meta['sender_bank'] }}</dd></div>@endif
                                        @if (! empty($meta['sender_account']))<div><dt class="text-gray-500">Sender account</dt><dd class="text-gray-900">{{ $meta['sender_account'] }}</dd></div>@endif
                                        @if (! empty($meta['note']))<div class="sm:col-span-2"><dt class="text-gray-500">Note</dt><dd class="text-gray-900">{{ $meta['note'] }}</dd></div>@endif
                                        @if (! empty($meta['proof_path']))
                                            <div class="sm:col-span-2"><dt class="text-gray-500">Proof of payment</dt>
                                                <dd><a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($meta['proof_path']) }}" target="_blank" class="text-[#1AAD94] font-semibold">Download &rarr;</a></dd>
                                            </div>
                                        @endif
                                        @if (! empty($meta['rejection_reason']))<div class="sm:col-span-2"><dt class="text-gray-500">Rejection reason</dt><dd class="text-red-600">{{ $meta['rejection_reason'] }}</dd></div>@endif
                                    </dl>

                                    @if ($payment->status === 'pending')
                                        <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
                                            <form method="POST" action="{{ route('admin.orders.payments.verify', [$order, $payment]) }}" onsubmit="return confirm('Verify this payment? Order will be marked as completed.');">
                                                @csrf
                                                <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#1AAD94] text-white rounded-md text-xs font-semibold hover:brightness-110">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                    Verify &amp; complete
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.orders.payments.reject', [$order, $payment]) }}" onsubmit="return confirm('Reject this payment? Order will return to pending.');" class="flex items-center gap-2">
                                                @csrf
                                                <input type="text" name="reason" placeholder="Reason (optional)" class="text-xs px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-red-400" />
                                                <button class="px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 rounded-md text-xs font-semibold">Reject</button>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No payments recorded.</p>
                @endif
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Customer</h3>
                @if($order->user)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-[#073057] rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($order->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $order->user) }}" class="text-sm text-[#1AAD94] hover:underline">View User Profile →</a>
                @else
                    <p class="text-sm text-gray-400">Customer not found.</p>
                @endif
            </div>

            @if($order->billing_info)
                <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Billing Info</h3>
                    <dl class="space-y-2 text-sm">
                        @foreach($order->billing_info as $key => $value)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="text-gray-900">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Details</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Currency</dt><dd>{{ $order->currency ?? 'USD' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Gateway</dt><dd>{{ ucfirst($order->gateway ?? 'N/A') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Paid At</dt><dd>{{ $order->paid_at?->format('M d, Y') ?? '—' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
@endsection
