@extends('layouts.dashboard')

@section('title', 'All Applicants')
@section('page-title', 'All Applicants')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">All Applicants</h2>
            <p class="text-[#6B7280]">Review and manage real applications submitted to your jobs.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('employer.applicants') }}" class="bg-white rounded-xl border border-[#E5E7EB] p-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, candidate title, or job..."
                        class="w-full pl-10 pr-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <svg class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <select name="job_id" class="px-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <option value="">All Jobs</option>
                @foreach($jobs as $job)
                    <option value="{{ $job->id }}" @selected((string) request('job_id') === (string) $job->id)>{{ $job->title }}</option>
                @endforeach
            </select>

            <select name="status" class="px-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <option value="">All Status</option>
                @foreach(['pending' => 'Pending', 'reviewed' => 'Reviewed', 'shortlisted' => 'Shortlisted', 'interviewed' => 'Interviewed', 'offered' => 'Offered', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="sort" class="px-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <option value="">Newest First</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest First</option>
                <option value="name" @selected(request('sort') === 'name')>Candidate Name</option>
            </select>

            <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Apply</button>
            @if(request()->hasAny(['search', 'job_id', 'status', 'sort']))
                <a href="{{ route('employer.applicants') }}" class="px-4 py-2.5 border border-[#E5E7EB] text-[#4B5563] font-medium rounded-xl hover:bg-[#F9FAFB] transition">Reset</a>
            @endif
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4 text-center">
            <p class="text-2xl font-bold text-[#073057]">{{ number_format($stats['total'] ?? 0) }}</p>
            <p class="text-sm text-[#6B7280]">Total Applicants</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4 text-center">
            <p class="text-2xl font-bold text-amber-500">{{ number_format($stats['pending'] ?? 0) }}</p>
            <p class="text-sm text-[#6B7280]">Pending Review</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4 text-center">
            <p class="text-2xl font-bold text-[#1AAD94]">{{ number_format($stats['shortlisted'] ?? 0) }}</p>
            <p class="text-sm text-[#6B7280]">Shortlisted</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ number_format($stats['rejected'] ?? 0) }}</p>
            <p class="text-sm text-[#6B7280]">Rejected</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($applications as $application)
            @php
                $candidate = $application->candidate;
                $candidateName = $candidate?->user?->name ?? 'Unknown candidate';
                $initials = collect(explode(' ', $candidateName))->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))->take(2)->join('');
                $resume = $application->resume ?? $candidate?->resumes?->firstWhere('is_default', true) ?? $candidate?->resumes?->first();
                $status = $application->status ?: 'pending';
                $statusStyles = [
                    'applied' => 'bg-blue-100 text-blue-700',
                    'pending' => 'bg-amber-100 text-amber-700',
                    'reviewed' => 'bg-yellow-100 text-yellow-700',
                    'shortlisted' => 'bg-emerald-100 text-emerald-700',
                    'interviewed' => 'bg-purple-100 text-purple-700',
                    'offered' => 'bg-indigo-100 text-indigo-700',
                    'hired' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                ];
            @endphp

            <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-[#073057]/10 rounded-full flex items-center justify-center text-[#073057] font-semibold text-lg shrink-0">
                        {{ $initials ?: 'C' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-semibold text-[#073057] truncate">{{ $candidateName }}</h4>
                                <p class="text-sm text-[#1AAD94] truncate">{{ $candidate?->title ?? 'Candidate profile' }}</p>
                            </div>
                            <span class="text-xs text-[#6B7280] shrink-0">{{ $application->created_at?->diffForHumans() }}</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-[#6B7280]">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $candidate?->experience_years ? $candidate->experience_years.' years' : 'Experience not set' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $candidate?->location?->name ?? 'Location not set' }}
                            </span>
                        </div>

                        <p class="text-xs text-[#6B7280] mt-2">Applied for: <span class="text-[#073057]">{{ $application->jobListing?->title ?? 'Deleted job' }}</span></p>

                        @if($candidate?->skills?->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach($candidate->skills->take(4) as $skill)
                                    <span class="px-2 py-0.5 bg-[#F3F4F6] text-[#4B5563] text-xs rounded">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-3 mt-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusStyles[$status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($status) }}</span>
                            <div class="flex items-center gap-1">
                                @if($candidate?->slug)
                                    <a href="{{ route('candidate.detail', $candidate->slug) }}" target="_blank" class="p-2 text-[#6B7280] hover:text-[#073057] hover:bg-[#F3F4F6] rounded-lg transition" title="View Profile">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                @endif
                                @if($resume)
                                    <a href="{{ asset($resume->file_path) }}" target="_blank" class="p-2 text-[#6B7280] hover:text-[#073057] hover:bg-[#F3F4F6] rounded-lg transition" title="Open CV">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 bg-white rounded-xl border border-[#E5E7EB] p-10 text-center">
                <h3 class="text-lg font-bold text-[#073057]">No applicants found</h3>
                <p class="mt-2 text-[#6B7280]">Applications submitted to your jobs will appear here. Adjust filters if you expected results.</p>
            </div>
        @endforelse
    </div>

    @if($applications->hasPages() || $applications->total() > 0)
        <div class="bg-white rounded-xl border border-[#E5E7EB] px-4 py-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <p class="text-sm text-[#6B7280]">
                    Showing {{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} applicants
                </p>
                {{ $applications->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
