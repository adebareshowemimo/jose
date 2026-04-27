@extends('layouts.dashboard')

@section('title', 'Resume Alerts')
@section('page-title', 'Resume Alerts')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Resume Alerts</h2>
            <p class="text-[#6B7280]">Live resume matches generated from your active job posts.</p>
        </div>
        <a href="{{ route('employer.new-job') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
            Post New Job
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">Active Alerts</p>
            <p class="mt-2 text-3xl font-bold text-[#073057]">{{ number_format($stats['active_alerts'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-[#9CA3AF]">One alert per active job</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">Matched Resumes</p>
            <p class="mt-2 text-3xl font-bold text-[#073057]">{{ number_format($stats['total_matches'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-[#9CA3AF]">Searchable candidates with CVs</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">New Today</p>
            <p class="mt-2 text-3xl font-bold text-[#073057]">{{ number_format($stats['matches_today'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-[#9CA3AF]">CV uploads matching jobs</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">High Confidence</p>
            <p class="mt-2 text-3xl font-bold text-[#073057]">{{ number_format($stats['high_confidence'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-[#9CA3AF]">70% match score or better</p>
        </div>
    </div>

    @if($activeJobs->isEmpty())
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-10 text-center">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center text-[#1AAD94]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="text-lg font-bold text-[#073057]">No active resume alerts yet</h3>
            <p class="mt-2 text-[#6B7280]">Create or activate a job post to start matching candidate resumes automatically.</p>
            <a href="{{ route('employer.new-job') }}" class="mt-5 inline-flex px-5 py-2.5 bg-[#1AAD94] text-white font-semibold rounded-xl">Post New Job</a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB]">
                <h3 class="font-semibold text-[#073057] text-lg">Alerts By Job</h3>
                <p class="text-sm text-[#6B7280]">Counts update from current candidate profiles and uploaded CVs.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Job Alert</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Location</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Matches</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">New Today</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @foreach($alertRows as $row)
                            <tr class="hover:bg-[#F9FAFB] transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-[#073057]">{{ $row['job']->title }}</p>
                                    <p class="text-sm text-[#6B7280]">{{ $row['job']->category?->name ?? $row['job']->jobType?->name ?? 'General role' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#4B5563]">{{ $row['job']->location?->name ?? 'Any location' }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-[#073057]">{{ $row['matches']->count() }}</span>
                                    <span class="text-sm text-[#6B7280]">matched resumes</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#4B5563]">{{ $row['matches_today'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $row['matches']->isNotEmpty() ? 'bg-[#1AAD94]/10 text-[#158f7a]' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $row['matches']->isNotEmpty() ? 'Matching' : 'No matches yet' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-[#073057] text-lg">Recent Resume Matches</h3>
                    <p class="text-sm text-[#6B7280]">Top candidates matched against your active alerts.</p>
                </div>
                <a href="{{ route('employer.resumes') }}" class="text-sm font-semibold text-[#1AAD94] hover:text-[#158f7a]">Browse all resumes</a>
            </div>

            <div class="divide-y divide-[#E5E7EB]">
                @forelse($recentMatches as $match)
                    @php
                        $candidate = $match['candidate'];
                        $resume = $match['resume'];
                        $candidateName = $candidate->user?->name ?? 'Candidate';
                        $initials = collect(explode(' ', $candidateName))->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))->take(2)->join('');
                    @endphp
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 rounded-full bg-[#073057]/10 text-[#073057] flex items-center justify-center font-bold shrink-0">{{ $initials ?: 'C' }}</div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-[#073057]">{{ $candidateName }}</h4>
                                    <span class="px-2 py-0.5 rounded bg-[#1AAD94]/10 text-[#158f7a] text-xs font-semibold">{{ $match['score'] }}% match</span>
                                </div>
                                <p class="text-sm text-[#6B7280]">
                                    {{ $candidate->title ?? 'Candidate profile' }}
                                    @if($candidate->experience_years)
                                        · {{ $candidate->experience_years }} years
                                    @endif
                                    @if($candidate->location?->name)
                                        · {{ $candidate->location->name }}
                                    @endif
                                </p>
                                <p class="text-sm text-[#4B5563] mt-1">Alert: <span class="font-medium">{{ $match['job']->title }}</span></p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($match['reasons'] as $reason)
                                        <span class="px-2.5 py-1 rounded-lg bg-[#F3F4F6] text-xs text-[#4B5563]">{{ $reason }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                            @if($candidate->slug)
                                <a href="{{ route('candidate.detail', $candidate->slug) }}" target="_blank" class="px-4 py-2 border border-[#E5E7EB] text-[#073057] text-sm font-semibold rounded-lg hover:bg-[#F9FAFB]">View Profile</a>
                            @endif
                            @if($resume)
                                <a href="{{ asset($resume->file_path) }}" target="_blank" class="px-4 py-2 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:bg-[#158f7a]">Open CV</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center">
                        <h3 class="font-semibold text-[#073057]">No matching resumes yet</h3>
                        <p class="mt-2 text-sm text-[#6B7280]">Matches will appear when searchable candidates upload CVs that align with your active jobs.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
