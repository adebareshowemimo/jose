@extends('layouts.dashboard')

@section('title', 'Available Jobs')
@section('page-title', 'Available Jobs')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@php
    // Colour map covering every application status the system uses.
    $statusColors = [
        'applied' => 'bg-amber-100 text-amber-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'reviewed' => 'bg-blue-100 text-blue-700',
        'reviewing' => 'bg-blue-100 text-blue-700',
        'shortlisted' => 'bg-indigo-100 text-indigo-700',
        'interviewed' => 'bg-emerald-100 text-emerald-700',
        'interview' => 'bg-emerald-100 text-emerald-700',
        'accepted' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
    ];
    $hasFilters = request()->hasAny(['keyword', 'location', 'category', 'type', 'applied']);
@endphp

@section('content')
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#073057]">Available Jobs</h2>
        <p class="text-[#6B7280]">Browse open positions and track the ones you've applied for</p>
    </div>

    {{-- Filter Bar --}}
    <form action="{{ route('user.available-jobs') }}" method="GET"
          class="bg-white rounded-xl border border-[#E5E7EB] p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Keyword --}}
            <div class="relative lg:col-span-2">
                <svg class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Job title or keyword..."
                       class="w-full pl-10 pr-4 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none" />
            </div>

            {{-- Location --}}
            <div class="relative">
                <svg class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input type="text" name="location" value="{{ request('location') }}" placeholder="Location..."
                       class="w-full pl-10 pr-4 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none" />
            </div>

            {{-- Category --}}
            <select name="category" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none text-[#4B5563]">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (int) request('category') === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-3">
            {{-- Job Type --}}
            <select name="type" class="px-4 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none text-[#4B5563]">
                <option value="">All Job Types</option>
                @foreach($jobTypes as $type)
                    <option value="{{ $type->id }}" {{ (int) request('type') === (int) $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>

            {{-- Applied-only toggle --}}
            <label class="inline-flex items-center gap-2 px-4 py-2.5 border border-[#E5E7EB] rounded-lg text-sm text-[#4B5563] cursor-pointer hover:bg-[#F9FAFB]">
                <input type="checkbox" name="applied" value="1" {{ request('applied') === '1' ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" onchange="this.form.submit()" />
                Applied only ({{ $appliedCount }})
            </label>

            <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-semibold rounded-lg transition cursor-pointer">Apply Filters</button>

            @if($hasFilters)
                <a href="{{ route('user.available-jobs') }}" class="px-4 py-2.5 text-sm text-[#6B7280] hover:text-[#073057]">Clear filters</a>
            @endif
        </div>
    </form>

    {{-- Results --}}
    @if($jobs->count() > 0)
        <div class="space-y-4">
            @foreach($jobs as $job)
                @php
                    $company = $job->company;
                    $initials = $company ? strtoupper(substr($company->name, 0, 2)) : 'JB';
                    $locationName = $job->location?->name ?? $job->address ?? 'Worldwide';
                    $typeName = $job->jobType?->name ?? ucfirst((string) $job->hours_type ?: 'Job');
                    $salaryLabel = ($job->salary_min || $job->salary_max)
                        ? trim(($job->salary_min ? '$'.number_format((float) $job->salary_min) : '').($job->salary_min && $job->salary_max ? ' - ' : '').($job->salary_max ? '$'.number_format((float) $job->salary_max) : '')).($job->salary_type ? ' / '.$job->salary_type : '')
                        : 'Negotiable';
                    $appliedStatus = $appliedStatuses[$job->id] ?? null;
                    $statusColor = $appliedStatus ? ($statusColors[$appliedStatus] ?? 'bg-gray-100 text-gray-700') : '';
                    $isSaved = in_array($job->id, $savedJobIds ?? [], true);
                @endphp
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 hover:shadow-md transition">
                    <div class="flex flex-wrap items-start gap-4">
                        {{-- Company logo / initials --}}
                        <div class="w-14 h-14 bg-[#F3F4F6] rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="font-bold text-[#073057] text-lg">{{ $initials }}</span>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-semibold text-[#073057] text-lg truncate">{{ $job->title }}</h3>
                                        @if($job->is_featured)
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-[#1AAD94]/10 text-[#1AAD94] uppercase tracking-wide">Featured</span>
                                        @endif
                                        @if($job->is_urgent)
                                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-red-100 text-red-600 uppercase tracking-wide">Urgent</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-[#6B7280] mt-0.5">{{ $company?->name ?? 'Company' }}</p>
                                </div>

                                {{-- Applied status badge --}}
                                @if($appliedStatus)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColor }} capitalize flex-shrink-0">{{ $appliedStatus }}</span>
                                @endif
                            </div>

                            {{-- Meta --}}
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-sm text-[#6B7280]">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $locationName }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $typeName }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $salaryLabel }}
                                </span>
                                @if($job->deadline)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Closes {{ $job->deadline->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap items-center justify-end gap-2 mt-4 pt-4 border-t border-[#F3F4F6]">
                        <form action="{{ route('user.bookmark.toggle', $job->id) }}" method="POST">
                            @csrf
                            <button type="submit" title="{{ $isSaved ? 'Remove from saved' : 'Save job' }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-[#E5E7EB] {{ $isSaved ? 'text-[#1AAD94]' : 'text-[#6B7280]' }} hover:bg-[#F9FAFB] transition cursor-pointer">
                                <svg class="w-4 h-4" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                {{ $isSaved ? 'Saved' : 'Save' }}
                            </button>
                        </form>

                        @if($appliedStatus)
                            <a href="{{ route('job.detail', $job->slug ?? $job->id) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-[#F3F4F6] text-[#4B5563] hover:bg-[#E5E7EB] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Applied
                            </a>
                        @else
                            <a href="{{ route('job.detail', $job->slug ?? $job->id) }}"
                               class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold rounded-lg bg-[#1AAD94] hover:bg-[#158f7a] text-white transition">
                                View &amp; Apply
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($jobs->hasPages())
            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] text-center py-16">
            <svg class="w-16 h-16 mx-auto text-[#E5E7EB] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <h3 class="text-lg font-semibold text-[#073057] mb-2">No Jobs Found</h3>
            <p class="text-[#6B7280] mb-6">
                @if($hasFilters)
                    No jobs match your filters right now. Try adjusting or clearing them.
                @else
                    There are no open positions at the moment. Check back soon.
                @endif
            </p>
            @if($hasFilters)
                <a href="{{ route('user.available-jobs') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
                    Clear Filters
                </a>
            @endif
        </div>
    @endif
@endsection
