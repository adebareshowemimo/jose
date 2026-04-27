@extends('layouts.dashboard')

@section('title', 'Hiring Services')
@section('page-title', 'Hiring Services')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

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

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#073057]">Hiring Services</h2>
        <p class="text-[#6B7280]">Request candidates from our recruitment team — CV sourcing, screening, or full hire.</p>
    </div>
    <a href="{{ route('employer.recruitment-requests.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        New Request
    </a>
</div>

@if (session('success'))
    <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
@endif

@if ($requests->isEmpty())
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-[#1AAD94]/10 text-[#1AAD94] rounded-full mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-[#073057] mb-2">No requests yet</h3>
        <p class="text-[#6B7280] mb-5">Tell us what you're hiring for and we'll source the right candidates.</p>
        <a href="{{ route('employer.recruitment-requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#073057] text-white font-semibold rounded-xl hover:brightness-110 transition">Submit your first request</a>
    </div>
@else
    <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium">Request</th>
                        <th class="text-left px-5 py-3 font-medium">Service</th>
                        <th class="text-left px-5 py-3 font-medium">CVs</th>
                        <th class="text-left px-5 py-3 font-medium">Status</th>
                        <th class="text-left px-5 py-3 font-medium">Quote</th>
                        <th class="text-left px-5 py-3 font-medium">Submitted</th>
                        <th class="text-right px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#073057]">{{ $req->job_title }}</p>
                                <p class="text-xs text-[#6B7280]">{{ $req->category?->name ?? 'No category' }} &middot; {{ $req->location?->name ?? 'Any location' }}</p>
                            </td>
                            <td class="px-5 py-4 text-[#4B5563]">{{ \App\Models\RecruitmentRequest::SERVICE_TYPES[$req->service_type] ?? $req->service_type }}</td>
                            <td class="px-5 py-4 text-[#4B5563]">{{ $req->cv_count }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge($req->status) }}">
                                    {{ \App\Models\RecruitmentRequest::STATUSES[$req->status] ?? $req->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-[#4B5563]">
                                @if ($req->quoted_amount)
                                    {{ $req->salary_currency }} {{ number_format($req->quoted_amount, 2) }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-[#6B7280] text-xs">{{ $req->created_at?->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('employer.recruitment-requests.show', $req) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
                                    View &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $requests->links() }}</div>
        @endif
    </div>
@endif
@endsection
