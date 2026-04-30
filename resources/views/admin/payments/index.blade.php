@extends('admin.layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Revenue (Completed)</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ money($totalRevenue, \App\Support\Currency::default()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Pending Payments</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ money($pendingPayments, \App\Support\Currency::default()) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Txn ID, order #, customer..."
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-56 focus:ring-2 focus:ring-[#1AAD94]" />
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['pending', 'processing', 'completed', 'failed', 'refunded'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Gateway</label>
                <select name="gateway" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach($gateways as $g)
                        <option value="{{ $g }}" {{ request('gateway') === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]" />
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]" />
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request()->hasAny(['q','status','gateway','from','to','trashed']))
                <a href="{{ route('admin.payments') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
            <label class="ml-auto inline-flex items-center gap-2 text-xs text-gray-500">
                <input type="checkbox" name="trashed" value="1" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300">
                Show deleted
            </label>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Transaction</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Customer</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Type</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Gateway</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Amount</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Receipt</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Date</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-gray-900 text-xs">{{ $payment->transaction_id ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <p class="text-gray-700">{{ $payment->order?->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">Order #{{ $payment->order?->order_number ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ \App\Support\PaymentTypeLabel::for($payment->order) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ ucfirst($payment->gateway ?? '—') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ money($payment->amount, $payment->currency ?? 'USD') }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($payment->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($payment->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs">
                                @if ($payment->receipt)
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-mono">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        {{ $payment->receipt->number }}
                                    </span>
                                @elseif ($payment->status === 'completed')
                                    <span class="text-amber-600">Not issued</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $payment->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="px-2.5 py-1 text-xs font-semibold text-[#073057] hover:bg-[#073057] hover:text-white rounded border border-[#073057]/20 transition">View</a>
                                    @if ($payment->status === 'completed' && ! $payment->receipt)
                                        <form method="POST" action="{{ route('admin.receipts.store', $payment) }}" class="inline">
                                            @csrf
                                            <button class="px-2.5 py-1 text-xs font-semibold bg-[#1AAD94] text-white rounded hover:brightness-110">Issue receipt</button>
                                        </form>
                                    @elseif ($payment->receipt)
                                        <form method="POST" action="{{ route('admin.receipts.email', $payment->receipt) }}" class="inline" onsubmit="return confirm('Email this receipt to {{ $payment->order?->user?->email }}?');">
                                            @csrf
                                            <button class="px-2.5 py-1 text-xs font-semibold border border-[#1AAD94] text-[#1AAD94] rounded hover:bg-[#1AAD94] hover:text-white">Send</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.payments.edit', $payment) }}" class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded border border-gray-200">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-10 text-center text-gray-400">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $payments->links() }}</div>
        @endif
    </div>
@endsection
