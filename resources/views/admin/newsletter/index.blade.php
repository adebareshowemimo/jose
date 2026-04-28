@extends('admin.layouts.app')

@section('title', 'Newsletter Subscribers')
@section('page-title', 'Newsletter')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">Newsletter Subscribers</h1>
            <p class="text-sm text-gray-500 mt-1">Everyone who's signed up to receive editorial and industry updates from JCL.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.newsletter.export') }}{{ request('status') ? '?status='.request('status') : '' }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:border-[#1AAD94] hover:text-[#1AAD94] rounded-lg text-sm font-semibold text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Total subscribers</p>
            <p class="text-3xl font-extrabold text-[#073057]">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Active</p>
            <p class="text-3xl font-extrabold text-green-600">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Unsubscribed</p>
            <p class="text-3xl font-extrabold text-gray-500">{{ number_format($stats['unsubscribed']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Last 30 days</p>
            <p class="text-3xl font-extrabold text-[#1AAD94]">{{ number_format($stats['last_30_days']) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Search</label>
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Email or name..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.newsletter.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="text-left px-5 py-3 font-semibold">Subscriber</th>
                        <th class="text-left px-5 py-3 font-semibold">Source</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="text-left px-5 py-3 font-semibold">Subscribed</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($subscribers as $sub)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#1AAD94] to-[#073057] text-white font-bold flex items-center justify-center text-sm shrink-0">
                                        {{ strtoupper(mb_substr($sub->name ?: $sub->email, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-[#0A1929] truncate">{{ $sub->email }}</p>
                                        @if ($sub->name)
                                            <p class="text-xs text-gray-500 truncate">{{ $sub->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 text-xs">{{ $sub->source ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if ($sub->isActive())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">
                                        Unsubscribed
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-600 text-xs">
                                {{ $sub->subscribed_at?->format('M d, Y') ?? $sub->created_at->format('M d, Y') }}
                                @if ($sub->unsubscribed_at)
                                    <p class="text-gray-400">Out: {{ $sub->unsubscribed_at->format('M d, Y') }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($sub->isActive())
                                        <form method="POST" action="{{ route('admin.newsletter.unsubscribe', $sub) }}" onsubmit="return confirm('Unsubscribe {{ $sub->email }}?');">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-semibold text-amber-700 hover:bg-amber-50">Unsubscribe</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.newsletter.reactivate', $sub) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-semibold text-green-700 hover:bg-green-50">Reactivate</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('Permanently delete {{ $sub->email }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-semibold text-red-600 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="text-sm text-gray-500">No subscribers yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscribers->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $subscribers->links() }}</div>
        @endif
    </div>
@endsection
