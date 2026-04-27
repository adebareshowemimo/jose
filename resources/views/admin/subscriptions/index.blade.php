@extends('admin.layouts.app')

@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')

@section('content')
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['active', 'expired', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request('status'))
                <a href="{{ route('admin.subscriptions') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">User</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Plan</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Cycle</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Period</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Jobs Used</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $sub->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $sub->user?->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-700 font-medium">{{ $sub->plan?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ ucfirst($sub->billing_cycle ?? '—') }}</td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $sub->starts_at?->format('M d, Y') ?? '—' }} – {{ $sub->ends_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $sub->jobs_used ?? 0 }} / {{ $sub->plan?->max_job_posts ?? '∞' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $sub->status === 'active' ? 'bg-green-100 text-green-700' : ($sub->status === 'expired' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst($sub->status ?? 'N/A') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No subscriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $subscriptions->links() }}</div>
        @endif
    </div>
@endsection
