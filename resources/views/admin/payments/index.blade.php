@extends('admin.layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
    {{-- Summary Cards --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Revenue (Completed)</p>
            <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Pending Payments</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">${{ number_format($pendingPayments, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['pending', 'completed', 'failed', 'refunded'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request('status'))
                <a href="{{ route('admin.payments') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Transaction ID</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Customer</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Gateway</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Amount</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-gray-900">{{ $payment->transaction_id ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <p class="text-gray-700">{{ $payment->order?->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">Order #{{ $payment->order?->order_number ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ ucfirst($payment->gateway ?? '—') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($payment->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $payment->created_at?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $payments->links() }}</div>
        @endif
    </div>
@endsection
