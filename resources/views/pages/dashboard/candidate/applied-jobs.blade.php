@extends('layouts.dashboard')

@section('title', 'Applied Jobs')
@section('page-title', 'Applied Jobs')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#073057]">Applied Jobs</h2>
        <p class="text-[#6B7280]">Track your job applications and their status</p>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-2">
            <button class="px-4 py-2 bg-[#1AAD94] text-white text-sm font-medium rounded-lg cursor-pointer">All ({{ $totalApplications ?? 0 }})</button>
            <button class="px-4 py-2 bg-white border border-[#E5E7EB] text-[#4B5563] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] cursor-pointer">Pending ({{ $pendingCount ?? 0 }})</button>
            <button class="px-4 py-2 bg-white border border-[#E5E7EB] text-[#4B5563] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] cursor-pointer">Interview ({{ $interviewCount ?? 0 }})</button>
            <button class="px-4 py-2 bg-white border border-[#E5E7EB] text-[#4B5563] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] cursor-pointer">Accepted ({{ $acceptedCount ?? 0 }})</button>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <input type="text" placeholder="Search applications..." class="pl-10 pr-4 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none w-64" />
                <svg class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Applications Table --}}
    <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
        @if(isset($applications) && $applications->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Job</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Applied Date</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Status</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-[#073057]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @foreach($applications as $application)
                    @php
                        $job = $application->jobListing;
                        $company = $job?->company;
                        $initials = $company ? strtoupper(substr($company->name, 0, 2)) : 'JB';
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-700',
                            'reviewing' => 'bg-blue-100 text-blue-700',
                            'shortlisted' => 'bg-indigo-100 text-indigo-700',
                            'interview' => 'bg-emerald-100 text-emerald-700',
                            'accepted' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                        ];
                        $statusColor = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <tr class="hover:bg-[#F9FAFB] transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-[#F3F4F6] rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="font-bold text-[#073057]">{{ $initials }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-[#073057]">{{ $job?->title ?? 'Job Title' }}</h4>
                                    <p class="text-sm text-[#6B7280]">{{ $company?->name ?? 'Company' }} · {{ $job?->location?->name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#6B7280]">{{ $application->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColor }} capitalize">{{ $application->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($job)
                                <a href="{{ route('job.show', $job->slug ?? $job->id) }}" class="p-2 text-[#6B7280] hover:text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer" title="View Job">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($applications->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-[#E5E7EB]">
            <p class="text-sm text-[#6B7280]">Showing {{ $applications->firstItem() }}-{{ $applications->lastItem() }} of {{ $applications->total() }} applications</p>
            {{ $applications->links() }}
        </div>
        @endif
        @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-[#E5E7EB] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <h3 class="text-lg font-semibold text-[#073057] mb-2">No Applications Yet</h3>
            <p class="text-[#6B7280] mb-6">Start applying to jobs to track your applications here.</p>
            <a href="{{ route('job.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Browse Jobs
            </a>
        </div>
        @endif
    </div>
@endsection
