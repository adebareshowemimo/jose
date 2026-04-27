@extends('admin.layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Order #</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order number..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['pending', 'processing', 'completed', 'cancelled', 'refunded'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Order #</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Customer</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Gateway</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Total</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Date</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">
                                <p class="text-gray-700">{{ $order->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user?->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ ucfirst($order->gateway ?? '—') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">${{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($order->status === 'cancelled' || $order->status === 'refunded' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                    {{ ucfirst($order->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-[#1AAD94] hover:underline text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
