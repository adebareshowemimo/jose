@extends('layouts.app')

@section('title', $pageTitle ?? 'Order')

@section('content')
@php
    $hasBankDetails = ! empty($bank['bank.account_number'] ?? null) && ! empty($bank['bank.bank_name'] ?? null);
    $statusBadge = match ($order->status) {
        'pending' => 'bg-yellow-100 text-yellow-700',
        'processing' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled', 'refunded' => 'bg-gray-100 text-gray-500',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<section class="py-12 bg-[#F9FAFB] min-h-[80vh]">
    <div class="container mx-auto px-6 max-w-5xl">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-[#073057]">Order #{{ $order->order_number }}</h1>
                <p class="text-sm text-[#6B7280]">Created {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusBadge }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Order Summary --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-[#E5E7EB] p-6">
                    <h2 class="text-lg font-bold text-[#073057] mb-4">Items</h2>
                    @forelse ($order->items as $item)
                        @php
                            $orderable = $item->orderable;
                            $title = $orderable->job_title ?? ($orderable->name ?? 'Item');
                            $sub = $item->meta['service_type'] ?? null;
                        @endphp
                        <div class="flex items-start justify-between gap-4 py-3 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="font-semibold text-[#073057]">{{ $title }}</p>
                                @if ($sub)
                                    <p class="text-xs text-[#6B7280]">{{ \App\Models\RecruitmentRequest::SERVICE_TYPES[$sub] ?? $sub }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">Qty {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold text-[#073057]">{{ $order->currency }} {{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No items.</p>
                    @endforelse

                    <div class="mt-5 pt-4 border-t border-gray-200 space-y-1">
                        <div class="flex justify-between text-sm"><span class="text-[#6B7280]">Subtotal</span><span>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-[#6B7280]">Tax</span><span>{{ $order->currency }} {{ number_format($order->tax, 2) }}</span></div>
                        <div class="flex justify-between text-base font-extrabold pt-2 border-t border-gray-100"><span>Total</span><span class="text-[#073057]">{{ $order->currency }} {{ number_format($order->total, 2) }}</span></div>
                    </div>
                </div>

                {{-- Payment options --}}
                @if ($order->status === 'completed')
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-700 mb-3">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-green-800">Payment confirmed</h3>
                        <p class="text-sm text-green-700 mt-1">Paid on {{ $order->paid_at?->format('M d, Y \a\t g:i A') }}.</p>
                    </div>
                @elseif ($order->status === 'processing')
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                        <h3 class="text-base font-bold text-blue-900 mb-1">Bank transfer received — awaiting verification</h3>
                        <p class="text-sm text-blue-800">We've received your transfer details. Our team will verify the payment with our bank and confirm within 1 business day. You'll receive an email once confirmed.</p>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-[#E5E7EB] p-6" x-data="{ method: '{{ $paystackEnabled ? 'paystack' : 'manual' }}' }">
                        <h2 class="text-lg font-bold text-[#073057] mb-4">Choose how to pay</h2>

                        <div class="grid {{ $paystackEnabled && $hasBankDetails ? 'sm:grid-cols-2' : '' }} gap-3 mb-6">
                            @if ($paystackEnabled)
                                <label class="cursor-pointer block">
                                    <input type="radio" name="method" value="paystack" x-model="method" class="sr-only" />
                                    <div :class="method === 'paystack' ? 'border-[#1AAD94] bg-[#1AAD94]/5 ring-2 ring-[#1AAD94]/30' : 'border-[#E5E7EB]'"
                                         class="p-4 rounded-xl border-2 hover:border-[#1AAD94]/60 transition h-full">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 rounded-lg bg-[#1AAD94]/10 text-[#1AAD94] flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <div class="font-semibold text-[#073057]">Pay with Paystack</div>
                                        </div>
                                        <div class="text-xs text-[#6B7280] leading-relaxed">Card, bank transfer, USSD, or mobile money. Instantly verified.</div>
                                    </div>
                                </label>
                            @endif

                            @if ($hasBankDetails)
                                <label class="cursor-pointer block">
                                    <input type="radio" name="method" value="manual" x-model="method" class="sr-only" />
                                    <div :class="method === 'manual' ? 'border-[#1AAD94] bg-[#1AAD94]/5 ring-2 ring-[#1AAD94]/30' : 'border-[#E5E7EB]'"
                                         class="p-4 rounded-xl border-2 hover:border-[#1AAD94]/60 transition h-full">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 rounded-lg bg-[#073057]/10 text-[#073057] flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <div class="font-semibold text-[#073057]">Bank Transfer</div>
                                        </div>
                                        <div class="text-xs text-[#6B7280] leading-relaxed">Wire to our account, then submit your transaction reference. Verified by our team.</div>
                                    </div>
                                </label>
                            @endif
                        </div>

                        @if (! $paystackEnabled && ! $hasBankDetails)
                            <div class="rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-sm">
                                Online payment is not available right now. Please contact <a href="mailto:info@joseoceanjobs.com" class="font-semibold underline">info@joseoceanjobs.com</a> to settle this invoice.
                            </div>
                        @endif

                        @if ($paystackEnabled)
                            <div x-show="method === 'paystack'" x-cloak>
                                <p class="text-sm text-[#4B5563] mb-4">You'll be securely redirected to Paystack to complete payment. Once paid, you'll be brought back here automatically.</p>
                                <form method="POST" action="{{ route('payment.paystack.init', $order) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3.5 bg-[#1AAD94] hover:brightness-110 text-white rounded-xl font-bold uppercase tracking-widest text-sm transition shadow">
                                        Pay {{ $order->currency }} {{ number_format($order->total, 2) }} with Paystack
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($hasBankDetails)
                            <div x-show="method === 'manual'" x-cloak>
                                <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl p-4 mb-5">
                                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Transfer to:</p>
                                    <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                                        <div><dt class="text-gray-500">Bank</dt><dd class="font-semibold text-[#073057]">{{ $bank['bank.bank_name'] }}</dd></div>
                                        <div><dt class="text-gray-500">Account name</dt><dd class="font-semibold text-[#073057]">{{ $bank['bank.account_name'] }}</dd></div>
                                        <div><dt class="text-gray-500">Account number</dt><dd class="font-semibold text-[#073057] font-mono">{{ $bank['bank.account_number'] }}</dd></div>
                                        @if (! empty($bank['bank.swift_code']))
                                            <div><dt class="text-gray-500">SWIFT / BIC</dt><dd class="font-semibold text-[#073057] font-mono">{{ $bank['bank.swift_code'] }}</dd></div>
                                        @endif
                                        <div class="sm:col-span-2"><dt class="text-gray-500">Reference</dt><dd class="font-semibold text-[#073057] font-mono">{{ $order->order_number }}</dd></div>
                                    </dl>
                                    @if (! empty($bank['bank.instructions']))
                                        <p class="mt-3 pt-3 border-t border-gray-200 text-xs text-[#4B5563] whitespace-pre-line">{{ $bank['bank.instructions'] }}</p>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('payment.manual.submit', $order) }}" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <p class="text-sm text-[#4B5563]">After making the transfer, fill in the details below so our team can verify it.</p>

                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Transaction / Reference ID <span class="text-red-500">*</span></label>
                                            <input type="text" name="transaction_id" required value="{{ old('transaction_id') }}" placeholder="From your bank app/SMS"
                                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Date paid</label>
                                            <input type="date" name="paid_at" value="{{ old('paid_at', now()->toDateString()) }}"
                                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Sender bank</label>
                                            <input type="text" name="sender_bank" value="{{ old('sender_bank') }}" placeholder="GTBank"
                                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Sender account</label>
                                            <input type="text" name="sender_account" value="{{ old('sender_account') }}" placeholder="Last 4 digits or full"
                                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Note (optional)</label>
                                        <textarea name="note" rows="2" placeholder="Anything we should know..."
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ old('note') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Proof of payment (optional)</label>
                                        <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png"
                                               class="block w-full text-sm text-[#4B5563] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#073057] file:text-white file:font-semibold" />
                                        <p class="mt-1 text-xs text-gray-400">PDF or image, max 5 MB.</p>
                                    </div>
                                    <button type="submit" class="w-full px-6 py-3.5 bg-[#073057] hover:brightness-110 text-white rounded-xl font-bold uppercase tracking-widest text-sm transition shadow">
                                        Submit Transfer Details
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Payment activity --}}
                @if ($order->payments->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-[#E5E7EB] p-6">
                        <h2 class="text-lg font-bold text-[#073057] mb-4">Payment activity</h2>
                        <div class="space-y-3">
                            @foreach ($order->payments as $p)
                                <div class="flex items-start justify-between gap-3 p-3 bg-[#F9FAFB] rounded-lg">
                                    <div>
                                        <p class="text-sm font-semibold text-[#073057]">{{ ucfirst($p->gateway) }} &middot; {{ $p->transaction_id ?? '—' }}</p>
                                        <p class="text-xs text-[#6B7280]">{{ $p->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $p->status === 'completed' ? 'bg-green-100 text-green-700' : ($p->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-4">
                <div class="bg-white rounded-2xl border border-[#E5E7EB] p-5">
                    <h3 class="text-sm font-bold text-[#073057] mb-3">Billed to</h3>
                    <p class="text-sm font-semibold text-[#073057]">{{ $order->user->name }}</p>
                    <p class="text-xs text-[#6B7280]">{{ $order->user->email }}</p>
                </div>

                <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-2xl p-5 text-xs text-[#6B7280]">
                    <p class="font-semibold text-[#073057] text-sm mb-2">Need a receipt or have a question?</p>
                    <p>Contact <a href="mailto:info@joseoceanjobs.com" class="text-[#1AAD94] font-semibold">info@joseoceanjobs.com</a> with your order number.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
