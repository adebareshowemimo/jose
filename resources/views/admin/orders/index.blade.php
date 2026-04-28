@extends('admin.layouts.app')

@section('title', 'Orders')
@section('page-title', 'Orders')

@php
    $typeLabel = function (?string $fqcn) {
        if (! $fqcn) return '—';
        $short = class_basename($fqcn);
        return [
            'RecruitmentRequest' => 'Recruitment',
            'TrainingProgram' => 'Training',
            'Event' => 'Event',
            'Candidate' => 'Boost',
            'Plan' => 'Subscription',
        ][$short] ?? $short;
    };
    $typeBadgeColor = function (?string $fqcn) {
        $short = class_basename($fqcn ?? '');
        return [
            'RecruitmentRequest' => 'bg-purple-100 text-purple-700',
            'TrainingProgram' => 'bg-amber-100 text-amber-700',
            'Event' => 'bg-blue-100 text-blue-700',
            'Candidate' => 'bg-pink-100 text-pink-700',
            'Plan' => 'bg-emerald-100 text-emerald-700',
        ][$short] ?? 'bg-gray-100 text-gray-600';
    };
@endphp

@section('content')
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Order #</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order number..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['pending', 'processing', 'completed', 'cancelled', 'refunded'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Type</label>
                <select name="orderable_type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach($orderableTypes ?? [] as $t)
                        <option value="{{ $t }}" {{ request('orderable_type') === $t ? 'selected' : '' }}>{{ $typeLabel($t) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Filter</button>
            @if(request()->hasAny(['search', 'status', 'orderable_type']))
                <a href="{{ route('admin.orders') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="text-left px-5 py-3 font-semibold">Order #</th>
                        <th class="text-left px-5 py-3 font-semibold">Type</th>
                        <th class="text-left px-5 py-3 font-semibold">Customer</th>
                        <th class="text-left px-5 py-3 font-semibold">Gateway</th>
                        <th class="text-right px-5 py-3 font-semibold">Total</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="text-left px-5 py-3 font-semibold">Date</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        @php $primaryItem = $order->items->first(); @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $typeBadgeColor($primaryItem?->orderable_type) }}">
                                    {{ $typeLabel($primaryItem?->orderable_type) }}
                                </span>
                                @if ($order->items->count() > 1)
                                    <span class="ml-1 text-[10px] text-gray-400">+{{ $order->items->count() - 1 }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-gray-700">{{ $order->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user?->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ ucfirst($order->gateway ?? '—') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ $order->currency ?? 'USD' }} {{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($order->status === 'cancelled' || $order->status === 'refunded' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                    {{ ucfirst($order->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-[#1AAD94] hover:underline text-sm font-semibold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
