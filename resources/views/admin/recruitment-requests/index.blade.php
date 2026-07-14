@extends('admin.layouts.app')

@section('title', 'Recruitment Requests')
@section('page-title', 'Recruitment Requests')

@section('content')
@php
    $statusBadge = function ($status) {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'quote_sent' => 'bg-blue-100 text-blue-700',
            'paid', 'in_progress' => 'bg-indigo-100 text-indigo-700',
            'candidates_delivered' => 'bg-purple-100 text-purple-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-600',
        };
    };
@endphp

<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteLabel: '' }">
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                <option value="">All</option>
                @foreach (\App\Models\RecruitmentRequest::STATUSES as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Service</label>
            <select name="service_type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                <option value="">All</option>
                @foreach (\App\Models\RecruitmentRequest::SERVICE_TYPES as $val => $label)
                    <option value="{{ $val }}" {{ request('service_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
        @if(request()->hasAny(['status', 'service_type']))
            <a href="{{ route('admin.recruitment-requests.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
        @endif
    </form>
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Company / Role</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Service</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">CVs</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Quote</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Submitted</th>
                    <th class="text-right px-5 py-3 font-medium text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($requests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-[#0A1929]">{{ $req->company?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $req->job_title }} &middot; by {{ $req->requester?->name ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ \App\Models\RecruitmentRequest::SERVICE_TYPES[$req->service_type] }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $req->cv_count }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge($req->status) }}">
                                {{ \App\Models\RecruitmentRequest::STATUSES[$req->status] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-700">
                            @if ($req->quoted_amount)
                                {{ $req->salary_currency }} {{ number_format($req->quoted_amount, 2) }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs">{{ $req->created_at?->format('M d, Y') }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-3">
                                <a href="{{ route('admin.recruitment-requests.show', $req) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#1AAD94] hover:text-[#0F8B75]">View &rarr;</a>
                                <button type="button"
                                    @click="deleteUrl = @js(route('admin.recruitment-requests.destroy', $req)); deleteLabel = @js($req->job_title); deleteModalOpen = true"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No recruitment requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($requests->hasPages())
        <div class="px-5 py-3 border-t border-gray-200">{{ $requests->links() }}</div>
    @endif
</div>

{{-- Delete recruitment request modal --}}
<div x-show="deleteModalOpen" x-cloak x-transition.opacity
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
     @click.self="deleteModalOpen = false" @keydown.escape.window="deleteModalOpen = false">
    <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="delete-request-title">
        <div class="p-6">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
            </div>
            <h3 id="delete-request-title" class="text-lg font-bold text-[#0A1929]">Delete recruitment request?</h3>
            <p class="mt-2 text-sm leading-6 text-gray-600">The request for <span class="font-semibold text-gray-800" x-text="deleteLabel"></span> will be removed from the request lists. The record can still be recovered from the database.</p>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
            <button type="button" @click="deleteModalOpen = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">Cancel</button>
            <form method="POST" :action="deleteUrl">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete request</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
