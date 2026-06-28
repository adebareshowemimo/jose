@extends('layouts.dashboard')

@section('title', 'Candidate Dashboard')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    {{-- Welcome Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#073057]">Welcome back, {{ strtok(auth()->user()->name ?? 'Candidate', ' ') }}!</h2>
        <p class="text-[#6B7280]">Ready to jump back in? Here's what's happening with your job search.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        {{-- Applied Jobs --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $appliedJobs ?? 0 }}</p>
                    <p class="text-sm text-[#6B7280]">Applied Jobs</p>
                </div>
            </div>
        </div>

        {{-- Job Alerts --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $jobAlerts ?? 0 }}</p>
                    <p class="text-sm text-[#6B7280]">Job Alerts</p>
                </div>
            </div>
        </div>

        {{-- Messages --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $messages ?? 0 }}</p>
                    <p class="text-sm text-[#6B7280]">Messages</p>
                </div>
            </div>
        </div>

        {{-- Saved Jobs --}}
        <div class="bg-white rounded-xl p-5 border border-[#E5E7EB] hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#073057]">{{ $savedJobs ?? 0 }}</p>
                    <p class="text-sm text-[#6B7280]">Saved Jobs</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid xl:grid-cols-3 gap-6">
        {{-- Profile Views Chart --}}
        @php
            $profileViews = $profileViews ?? [];
            $profileViewCounts = array_column($profileViews, 'count');
            $profileViewsTotal = array_sum($profileViewCounts);
            $profileViewsMax = $profileViewCounts ? max($profileViewCounts) : 0;
            $profileViewsMax = max(1, $profileViewsMax);
        @endphp
        <div class="xl:col-span-2 bg-white rounded-xl border border-[#E5E7EB]">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <div>
                    <h3 class="text-lg font-semibold text-[#073057]">Profile Views</h3>
                    <p class="text-xs text-[#6B7280] mt-0.5">{{ number_format($profileViewsTotal) }} views in the last 6 months</p>
                </div>
            </div>
            <div class="p-5">
                @if($profileViewsTotal === 0)
                    <div class="h-64 flex flex-col items-center justify-center text-center text-[#6B7280]">
                        <svg class="w-12 h-12 mb-3 text-[#E5E7EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <p class="font-medium">No profile views yet</p>
                        <p class="text-sm">Views appear here when employers open your profile.</p>
                    </div>
                @else
                    <div class="h-64 flex items-end gap-4">
                        @foreach($profileViews as $point)
                            @php $barHeight = $point['count'] > 0 ? max(4, round($point['count'] / $profileViewsMax * 100)) : 0; @endphp
                            <div class="flex-1 flex flex-col items-center gap-2">
                                <span class="text-xs font-semibold text-[#073057]">{{ $point['count'] }}</span>
                                <div class="w-full bg-[#1AAD94]/10 rounded-t-lg relative flex-1 flex items-end" title="{{ $point['count'] }} views in {{ $point['label'] }}">
                                    <div class="w-full bg-[#1AAD94] rounded-t-lg transition-all" style="height: {{ $barHeight }}%"></div>
                                </div>
                                <span class="text-xs text-[#6B7280]">{{ $point['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Notifications --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB]">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <h3 class="text-lg font-semibold text-[#073057]">Notifications</h3>
                <a href="{{ route('user.notifications') }}" class="text-sm text-[#1AAD94] hover:underline">View all</a>
            </div>
            <div class="divide-y divide-[#E5E7EB]">
                @forelse($recentNotifications ?? [] as $notification)
                    @php
                        $data = $notification->data ?? [];
                        $title = $data['title'] ?? 'Notification';
                        $message = $data['message'] ?? '';
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <a href="{{ route('user.notifications.read', $notification->id) }}"
                       class="block p-4 transition {{ $isUnread ? 'bg-[#F0FBF8] hover:bg-[#E6F7F2]' : 'hover:bg-[#F9FAFB]' }}">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full {{ $isUnread ? 'bg-[#1AAD94] text-white' : 'bg-[#E5E7EB] text-[#6B7280]' }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-[#374151] font-medium">{{ $title }}</p>
                                @if($message)
                                    <p class="text-sm text-[#6B7280] truncate">{{ $message }}</p>
                                @endif
                                <p class="text-xs text-[#9CA3AF] mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-[#6B7280]">
                        <svg class="w-10 h-10 mx-auto mb-3 text-[#E5E7EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <p class="text-sm font-medium">No notifications yet</p>
                        <p class="text-xs">You'll see updates about your account here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Applied Jobs --}}
    <div class="mt-6 bg-white rounded-xl border border-[#E5E7EB]">
        <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-semibold text-[#073057]">Jobs Applied Recently</h3>
            <a href="{{ route('user.applied-jobs') }}" class="text-sm text-[#1AAD94] hover:underline">View all</a>
        </div>
        <div class="grid lg:grid-cols-2 gap-4 p-5">
            @forelse($recentApplications ?? [] as $application)
            @php
                $job = $application->jobListing;
                $company = $job?->company;
                $initials = $company ? strtoupper(substr($company->name, 0, 2)) : 'JB';
                $statusColors = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'reviewing' => 'bg-blue-100 text-blue-700',
                    'interview' => 'bg-emerald-100 text-emerald-700',
                    'accepted' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                ];
                $statusColor = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-700';
            @endphp
            <div class="border border-[#E5E7EB] rounded-xl p-4 hover:shadow-md transition group">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-[#F3F4F6] rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold text-[#073057]">{{ $initials }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-[#073057] group-hover:text-[#1AAD94] transition">{{ $job?->title ?? 'Job Title' }}</h4>
                        <p class="text-sm text-[#6B7280] mb-2">{{ $company?->name ?? 'Company' }}</p>
                        <div class="flex flex-wrap gap-2 text-xs text-[#6B7280]">
                            @if($job?->location)
                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> {{ $job->location->name }}</span>
                            @endif
                            @if($job?->salary_max)
                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${{ number_format($job->salary_max) }}/{{ $job->salary_type ?? 'month' }}</span>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-3">
                            @if($job?->jobType)
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">{{ $job->jobType->name }}</span>
                            @endif
                            <span class="px-2.5 py-1 {{ $statusColor }} text-xs font-medium rounded-full capitalize">{{ $application->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="lg:col-span-2 text-center py-8 text-[#6B7280]">
                <svg class="w-12 h-12 mx-auto mb-4 text-[#E5E7EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <p class="font-medium">No applications yet</p>
                <p class="text-sm">Start applying to jobs to see them here.</p>
                <a href="{{ route('job.index') }}" class="inline-block mt-4 px-5 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-lg transition">Browse Jobs</a>
            </div>
            @endforelse
        </div>
    </div>
@endsection
