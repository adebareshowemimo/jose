@extends('admin.layouts.app')

@section('title', 'Boost Subscribers')
@section('page-title', 'Boost Subscribers')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">Boost Subscribers</h1>
            <p class="text-sm text-gray-500 mt-1">Candidates with a featured-profile boost, and when each one ends.</p>
        </div>
        <a href="{{ route('admin.boosts.subscribers.export', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 bg-white text-sm font-bold uppercase tracking-widest text-gray-700 rounded-lg hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Active now</p>
            <p class="text-2xl font-bold text-[#0A1929] mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Ending in {{ $expiringWindow }}d</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['expiring'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Total sold</p>
            <p class="text-2xl font-bold text-[#0A1929] mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Boost revenue</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ money($stats['revenue']) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4 mb-5 grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or email"
               class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">

        <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="expiring" @selected(request('status') === 'expiring')>Expiring soon</option>
            <option value="expired" @selected(request('status') === 'expired')>Expired</option>
            <option value="refunded" @selected(request('status') === 'refunded')>Refunded</option>
        </select>

        <select name="package" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
            <option value="">All packages</option>
            @foreach ($packages as $pkg)
                <option value="{{ $pkg->id }}" @selected((string) request('package') === (string) $pkg->id)>{{ $pkg->label }}</option>
            @endforeach
        </select>

        <input type="date" name="from" value="{{ request('from') }}" title="Started on or after"
               class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-[#073057] text-white text-sm font-bold uppercase tracking-widest rounded-lg hover:brightness-125 transition">Filter</button>
            <a href="{{ route('admin.boosts.subscribers.index') }}" class="px-4 py-2 border border-gray-200 text-sm font-semibold text-gray-600 rounded-lg hover:bg-gray-50 transition">Reset</a>
        </div>
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-5 py-3 font-semibold">Candidate</th>
                        <th class="px-5 py-3 font-semibold">Package</th>
                        <th class="px-5 py-3 font-semibold">Started</th>
                        <th class="px-5 py-3 font-semibold">Ends</th>
                        <th class="px-5 py-3 font-semibold">Remaining</th>
                        <th class="px-5 py-3 font-semibold">Paid</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold text-right">&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($boosts as $boost)
                        @php
                            $isRunning = $boost->isCurrentlyActive();
                            $daysLeft = $isRunning ? (int) ceil(now()->floatDiffInDays($boost->ends_at)) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#0A1929]">{{ $boost->candidate?->user?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $boost->candidate?->user?->email ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-700">
                                {{ $boost->package?->label ?? '—' }}
                                <span class="block text-xs text-gray-400">{{ $boost->days }} days</span>
                            </td>
                            <td class="px-5 py-4 text-gray-700">{{ $boost->starts_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-4 text-gray-700">{{ $boost->ends_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if ($isRunning)
                                    <span class="font-semibold {{ $daysLeft <= $expiringWindow ? 'text-amber-600' : 'text-gray-700' }}">
                                        {{ $daysLeft }}d
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-semibold text-[#0A1929]">{{ money($boost->price) }}</td>
                            <td class="px-5 py-4">
                                @if ($boost->status === \App\Models\CandidateBoost::STATUS_REFUNDED)
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold">Refunded</span>
                                @elseif ($isRunning && $daysLeft <= $expiringWindow)
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">Expiring</span>
                                @elseif ($isRunning)
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">Active</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">Expired</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.boosts.subscribers.show', $boost) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">No boosts match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($boosts->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $boosts->links() }}</div>
        @endif
    </div>
@endsection
