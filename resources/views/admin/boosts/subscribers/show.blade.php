@extends('admin.layouts.app')

@section('title', 'Boost detail')
@section('page-title', 'Boost Subscribers')

@section('content')
    @php
        $isRunning = $boost->isCurrentlyActive();
        $daysLeft = $isRunning ? (int) ceil(now()->floatDiffInDays($boost->ends_at)) : 0;
        $user = $boost->candidate?->user;
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.boosts.subscribers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to subscribers</a>
        <h1 class="text-2xl font-bold text-[#0A1929] mt-2">{{ $user?->name ?? 'Unknown candidate' }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $user?->email ?? '—' }}</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Summary --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Package</p>
                        <p class="text-lg font-bold text-[#0A1929] mt-0.5">{{ $boost->package?->label ?? '—' }}</p>
                    </div>
                    @if ($boost->status === \App\Models\CandidateBoost::STATUS_REFUNDED)
                        <span class="inline-flex px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold">Refunded</span>
                    @elseif ($isRunning)
                        <span class="inline-flex px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">Active · {{ $daysLeft }}d left</span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">Expired</span>
                    @endif
                </div>

                <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Duration purchased</dt>
                        <dd class="font-semibold text-[#0A1929] mt-0.5">{{ $boost->days }} days</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Amount paid</dt>
                        <dd class="font-semibold text-[#0A1929] mt-0.5">{{ money($boost->price) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Started</dt>
                        <dd class="font-semibold text-[#0A1929] mt-0.5">{{ $boost->starts_at?->format('M d, Y · H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Ends</dt>
                        <dd class="font-semibold text-[#0A1929] mt-0.5">{{ $boost->ends_at?->format('M d, Y · H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Order</dt>
                        <dd class="font-semibold mt-0.5">
                            @if ($boost->order)
                                <a href="{{ route('admin.orders.show', $boost->order->id) }}" class="text-[#1AAD94] hover:underline">{{ $boost->order->order_number }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Profile featured until</dt>
                        <dd class="font-semibold text-[#0A1929] mt-0.5">
                            {{ $boost->candidate?->featured_until?->format('M d, Y') ?? 'Not featured' }}
                        </dd>
                    </div>
                </dl>

                @if ($boost->candidate?->featured_until && $boost->ends_at && $boost->candidate->featured_until->gt($boost->ends_at))
                    <p class="mt-4 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
                        This candidate's featured placement runs past this boost, because another boost is stacked on top of it.
                    </p>
                @endif
            </div>

            {{-- Payments --}}
            @if ($boost->order && $boost->order->payments->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-[#0A1929]">Payments</h2>
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($boost->order->payments as $payment)
                                <tr>
                                    <td class="px-5 py-3 text-gray-700">{{ $payment->created_at?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-gray-500">{{ ucfirst($payment->gateway) }}</td>
                                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $payment->transaction_id }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#0A1929]">{{ money($payment->amount) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold {{ $payment->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Other boosts by this candidate --}}
            @if ($history->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-[#0A1929]">Other boosts by this candidate</h2>
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($history as $other)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-5 py-3 text-gray-700">{{ $other->package?->label ?? '—' }}</td>
                                    <td class="px-5 py-3 text-gray-500">{{ $other->starts_at?->format('M d, Y') }} → {{ $other->ends_at?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#0A1929]">{{ money($other->price) }}</td>
                                    <td class="px-5 py-3 text-gray-500">{{ ucfirst($other->status) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.boosts.subscribers.show', $other) }}" class="text-xs font-semibold text-[#1AAD94] hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-bold text-[#0A1929] mb-1">Extend boost</h2>
                <p class="text-xs text-gray-500 mb-4">Adds days to the end date. Use for goodwill or to compensate for downtime.</p>

                <form method="POST" action="{{ route('admin.boosts.subscribers.extend', $boost) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="days" class="block text-xs font-semibold text-gray-700 mb-1">Days to add</label>
                        <input id="days" type="number" name="days" min="1" max="365" value="7" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg transition">
                        Extend
                    </button>
                </form>
            </div>

            @if ($boost->status !== \App\Models\CandidateBoost::STATUS_REFUNDED)
                <div class="bg-white border border-red-100 rounded-xl p-5">
                    <h2 class="font-bold text-[#0A1929] mb-1">End boost</h2>
                    <p class="text-xs text-gray-500 mb-4">
                        By default the candidate keeps the placement they already paid for. Tick the box only if the boost should stop immediately, such as a refund.
                    </p>

                    <form method="POST" action="{{ route('admin.boosts.subscribers.cancel', $boost) }}" class="space-y-3"
                          onsubmit="return confirm('End this boost now?');">
                        @csrf
                        <div>
                            <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">Mark as</label>
                            <select id="status" name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                                <option value="expired">Expired</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>

                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="revoke_placement" value="1" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-xs text-gray-700">Remove featured placement immediately</span>
                        </label>

                        <button type="submit" class="w-full px-4 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 text-sm font-bold uppercase tracking-widest rounded-lg transition">
                            End boost
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
