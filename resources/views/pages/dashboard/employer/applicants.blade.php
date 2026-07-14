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

    <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
        <div class="px-5 py-4 border-b border-[#E5E7EB] flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-[#073057]">Applicant Directory</h3>
                <p class="text-xs text-[#6B7280] mt-0.5">Review candidates across all active job listings</p>
            </div>
            <span class="px-3 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-semibold rounded-full">{{ number_format($applications->total()) }} applicants</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-sm">
                <thead>
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-left">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280]">Candidate</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280]">Profile</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280]">Applied Job</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280]">Experience / Location</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280]">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280]">Applied</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#6B7280] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
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

            <tr class="hover:bg-[#F9FAFB] transition-colors">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#073057]/10 rounded-full flex items-center justify-center text-[#073057] font-semibold shrink-0">{{ $initials ?: 'C' }}</div>
                        <div class="min-w-0">
                            @if($candidate)
                                <a href="{{ route('employer.candidates.show', $candidate) }}" class="font-semibold text-[#073057] hover:text-[#1AAD94] hover:underline truncate max-w-[180px] block">{{ $candidateName }}</a>
                            @else
                                <p class="font-semibold text-[#073057] truncate max-w-[180px]">{{ $candidateName }}</p>
                            @endif
                            <p class="text-xs text-[#6B7280] truncate max-w-[180px]">{{ $candidate?->user?->email ?? 'Email not available' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4">
                    @if($candidate)
                        <a href="{{ route('employer.candidates.show', $candidate) }}" class="font-medium text-[#1AAD94] hover:underline">{{ $candidate->title ?? 'Candidate profile' }}</a>
                    @else
                        <p class="font-medium text-[#1AAD94]">Candidate profile</p>
                    @endif
                    @if($candidate?->skills?->isNotEmpty())
                        <p class="text-xs text-[#6B7280] mt-1 truncate max-w-[180px]">{{ $candidate->skills->take(3)->pluck('name')->join(', ') }}</p>
                    @endif
                </td>
                <td class="px-5 py-4 font-medium text-[#073057]">{{ $application->jobListing?->title ?? 'Deleted job' }}</td>
                <td class="px-5 py-4 text-[#6B7280]">
                    <p>{{ $candidate?->experience_years ? $candidate->experience_years.' years experience' : 'Experience not set' }}</p>
                    <p class="text-xs mt-1">{{ $candidate?->location?->name ?? 'Location not set' }}</p>
                </td>
                <td class="px-5 py-4"><span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusStyles[$status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($status) }}</span></td>
                <td class="px-5 py-4 text-[#6B7280] whitespace-nowrap">
                    <p>{{ $application->created_at?->format('M d, Y') }}</p>
                    <p class="text-xs mt-1">{{ $application->created_at?->diffForHumans() }}</p>
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="inline-flex items-center gap-1">
                                @if($candidate?->slug)
                                    <a href="{{ route('employer.candidates.show', $candidate) }}" class="p-2 text-[#6B7280] hover:text-[#073057] hover:bg-[#F3F4F6] rounded-lg transition" title="View full candidate details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                @endif
                                @if($resume)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($resume->file_path) }}" target="_blank" rel="noopener" class="p-2 text-[#6B7280] hover:text-[#073057] hover:bg-[#F3F4F6] rounded-lg transition" title="Open CV">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-5 py-12 text-center"><h3 class="text-lg font-bold text-[#073057]">No applicants found</h3><p class="mt-2 text-[#6B7280]">Applications submitted to your jobs will appear here. Adjust filters if you expected results.</p></td></tr>
        @endforelse
                </tbody>
            </table>
        </div>
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
