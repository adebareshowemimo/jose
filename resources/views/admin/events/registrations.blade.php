@extends('admin.layouts.app')

@section('title', 'Attendees · ' . $event->title)
@section('page-title', 'Event Attendees')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-gray-600 mb-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to events
        </a>
        <h1 class="text-2xl font-bold text-[#0A1929]">{{ $event->title }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $event->display_date }} · {{ $event->location }}</p>
    </div>
    <a href="{{ route('admin.events.registrations.export', $event) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:border-[#1AAD94] hover:text-[#1AAD94] rounded-lg text-sm font-semibold text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Total registrations</p>
        <p class="text-3xl font-extrabold text-[#073057]">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Paid</p>
        <p class="text-3xl font-extrabold text-green-600">{{ number_format($stats['paid']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Tickets sold</p>
        <p class="text-3xl font-extrabold text-[#1AAD94]">{{ number_format($stats['tickets']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-1">Capacity</p>
        <p class="text-3xl font-extrabold text-gray-700">{{ $stats['capacity'] !== null ? number_format($stats['capacity']) : '∞' }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Email or name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
        </div>
        <div>
            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                <option value="">All</option>
                @foreach (['pending', 'paid', 'cancelled', 'attended'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Filter</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.events.registrations', $event) }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                <th class="text-left px-5 py-3 font-semibold">Buyer</th>
                <th class="text-left px-5 py-3 font-semibold">Tickets</th>
                <th class="text-left px-5 py-3 font-semibold">Status</th>
                <th class="text-left px-5 py-3 font-semibold">Registered</th>
                <th class="text-left px-5 py-3 font-semibold">Answers</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($registrations as $reg)
                <tr class="hover:bg-gray-50 align-top">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-[#0A1929]">{{ $reg->buyer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $reg->buyer_email }} @if ($reg->buyer_phone) · {{ $reg->buyer_phone }} @endif</p>
                    </td>
                    <td class="px-5 py-4 font-bold text-[#073057]">{{ $reg->ticket_count }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $reg->status === 'paid' ? 'bg-green-100 text-green-700' : ($reg->status === 'attended' ? 'bg-blue-100 text-blue-700' : ($reg->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ ucfirst($reg->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-600">{{ $reg->registered_at?->format('M d, Y H:i') ?? $reg->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-5 py-4">
                        @if (! empty($reg->answers))
                            <div class="space-y-1 text-xs text-gray-600">
                                @foreach ($reg->answers as $key => $val)
                                    <p><strong class="text-gray-700">{{ $key }}:</strong> {{ \Illuminate\Support\Str::limit((string) $val, 60) }}</p>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-16 text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <p class="text-sm text-gray-500">No attendees yet.</p>
                </td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($registrations->hasPages())
        <div class="px-5 py-3 border-t border-gray-200">{{ $registrations->links() }}</div>
    @endif
</div>
@endsection
