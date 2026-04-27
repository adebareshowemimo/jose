@extends('layouts.dashboard')

@section('title', 'Employer Dashboard')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
    {{-- Welcome Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#073057]">Welcome back, {{ auth()->user()->company->name ?? 'Company' }}!</h2>
        <p class="text-[#6B7280]">Here's what's happening with your job postings today.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        {{-- Posted Jobs --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $postedJobs ?? 18 }}</p>
                    <p class="text-sm text-[#6B7280]">Posted Jobs</p>
                </div>
            </div>
        </div>

        {{-- Applications --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $applications ?? 245 }}</p>
                    <p class="text-sm text-[#6B7280]">Applications</p>
                </div>
            </div>
        </div>

        {{-- Closing Soon --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $closingSoon ?? 0 }}</p>
                    <p class="text-sm text-[#6B7280]">Closing Soon</p>
                </div>
            </div>
        </div>

        {{-- Shortlisted --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $shortlisted ?? 48 }}</p>
                    <p class="text-sm text-[#6B7280]">Shortlisted</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid xl:grid-cols-3 gap-6">
        {{-- Profile Views Chart --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-[#E5E7EB]">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <h3 class="text-lg font-semibold text-[#073057]">Job Views & Applications</h3>
                <select class="text-sm border border-[#E5E7EB] rounded-lg px-3 py-1.5 text-[#4B5563] focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option>Last 6 months</option>
                    <option>Last 12 months</option>
                    <option>This year</option>
                </select>
            </div>
            <div class="p-5">
                @php
                    $maxApps = $chartData->max('applications') ?: 1;
                @endphp
                <div class="h-64 flex items-end gap-3">
                    @foreach($chartData as $point)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full flex gap-1 justify-center">
                                <div class="flex-1 bg-[#1AAD94] rounded-t" style="height: {{ ($point['applications'] / $maxApps) * 200 }}px"></div>
                            </div>
                            <span class="text-xs text-[#6B7280]">{{ $point['month'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-center gap-6 mt-4 pt-4 border-t border-[#E5E7EB]">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-[#1AAD94] rounded-sm"></span>
                        <span class="text-sm text-[#6B7280]">Applications</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB]">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <h3 class="text-lg font-semibold text-[#073057]">Recent Activity</h3>
                <a href="{{ route('employer.applicants') }}" class="text-sm text-[#1AAD94] hover:underline">View all</a>
            </div>
            <div class="divide-y divide-[#E5E7EB]">
                @forelse($recentNotifications as $notification)
                    <div class="p-4 hover:bg-[#F9FAFB] transition">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-[#374151]">
                                    <strong>{{ $notification->candidate?->user?->name ?? 'Unknown' }}</strong>
                                    applied for
                                    <span class="text-[#1AAD94]">{{ $notification->jobListing?->title ?? 'a job' }}</span>
                                </p>
                                <p class="text-xs text-[#9CA3AF]">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @switch($notification->status)
                                    @case('applied') bg-blue-100 text-blue-700 @break
                                    @case('reviewed') bg-yellow-100 text-yellow-700 @break
                                    @case('shortlisted') bg-emerald-100 text-emerald-700 @break
                                    @case('interviewed') bg-purple-100 text-purple-700 @break
                                    @case('offered') bg-indigo-100 text-indigo-700 @break
                                    @case('hired') bg-green-100 text-green-700 @break
                                    @case('rejected') bg-red-100 text-red-700 @break
                                    @default bg-gray-100 text-gray-700
                                @endswitch
                            ">{{ ucfirst($notification->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-sm text-[#6B7280]">No recent activity yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Applicants --}}
    <div class="mt-6 bg-white rounded-xl border border-[#E5E7EB]">
        <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-semibold text-[#073057]">Recent Applicants</h3>
            <a href="{{ route('employer.applicants') }}" class="text-sm text-[#1AAD94] hover:underline">View all</a>
        </div>
        <div class="grid lg:grid-cols-2 gap-4 p-5">
            @forelse($recentApplicants as $applicant)
                @php
                    $candidateUser = $applicant->candidate?->user;
                    $candidateName = $candidateUser?->name ?? 'Unknown';
                    $initials = collect(explode(' ', $candidateName))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                    $location = $applicant->candidate?->location?->name;
                    $salary = $applicant->candidate?->expected_salary;
                    $salaryType = $applicant->candidate?->salary_type;
                    $skills = $applicant->candidate?->skills?->take(3) ?? collect();
                @endphp
                <div class="border border-[#E5E7EB] rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-[#073057] to-[#1AAD94] rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-[#073057]">{{ $candidateName }}</h4>
                            <p class="text-sm text-[#1AAD94] mb-1">{{ $applicant->jobListing?->title ?? 'N/A' }}</p>
                            <div class="flex flex-wrap gap-2 text-xs text-[#6B7280] mb-3">
                                @if($location)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $location }}
                                    </span>
                                @endif
                                @if($salary)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        ${{ number_format($salary) }}{{ $salaryType ? '/' . $salaryType : '' }}
                                    </span>
                                @endif
                                <span class="text-xs text-[#9CA3AF]">{{ $applicant->created_at->diffForHumans() }}</span>
                            </div>
                            @if($skills->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach($skills as $skill)
                                        <span class="px-2 py-0.5 bg-[#F3F4F6] text-[#374151] text-xs rounded">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @switch($applicant->status)
                                        @case('applied') bg-blue-100 text-blue-700 @break
                                        @case('reviewed') bg-yellow-100 text-yellow-700 @break
                                        @case('shortlisted') bg-emerald-100 text-emerald-700 @break
                                        @case('interviewed') bg-purple-100 text-purple-700 @break
                                        @case('offered') bg-indigo-100 text-indigo-700 @break
                                        @case('hired') bg-green-100 text-green-700 @break
                                        @case('rejected') bg-red-100 text-red-700 @break
                                        @default bg-gray-100 text-gray-700
                                    @endswitch
                                ">{{ ucfirst($applicant->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 p-8 text-center">
                    <p class="text-sm text-[#6B7280]">No applicants yet. Post a job to start receiving applications.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
