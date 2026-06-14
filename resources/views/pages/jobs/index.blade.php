@extends('layouts.app')

@section('title', $pageTitle ?? 'Find Maritime Jobs')

@section('content')
{{-- Hero Search Section --}}
<section class="bg-gradient-to-br from-[#073057] to-[#0a4275] pt-24 pb-32 relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Find Your Maritime/Logistics &amp; Energy Sector Jobs</h1>
        </div>

        {{-- Search Form --}}
        <div class="max-w-5xl mx-auto">
            <form action="{{ route('job.index') }}" method="GET" class="bg-white rounded-2xl shadow-2xl p-3 md:p-4">
                {{-- Keep any active sidebar filters / sort when running a new keyword search --}}
                <x-ui.hidden-query :except="['keyword', 's', 'location', 'category']" />
                <div class="grid md:grid-cols-4 gap-3">
                    <div class="md:col-span-1">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="keyword" value="{{ $keyword ?? request('keyword') }}" placeholder="Job title, rank, skills..."
                                class="w-full pl-12 pr-4 py-3.5 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <input type="text" name="location" value="{{ request('location') }}" placeholder="City, country, or port..."
                                class="w-full pl-12 pr-4 py-3.5 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <select name="category" class="w-full px-4 py-3.5 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none text-[#4B5563] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:20px] bg-[right_12px_center] bg-no-repeat">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (int) request('category') === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <button type="submit" class="w-full py-3.5 px-6 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Search Jobs
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="py-12 bg-[#F9FAFB] -mt-16 relative z-20">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-4 gap-6">
            {{-- Filter Sidebar --}}
            <aside class="lg:col-span-1" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="lg:hidden w-full flex items-center justify-between p-4 bg-white rounded-xl border border-[#E5E7EB] mb-4">
                    <span class="font-semibold text-[#073057]">Filters</span>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-[#6B7280] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <form :class="{ 'hidden': !open }" action="{{ route('job.index') }}" method="GET" class="lg:block space-y-4">
                    {{-- Keep the keyword/location/category/sort context applied from the hero + header --}}
                    <x-ui.hidden-query :except="['type', 'salary']" />

                    {{-- Job Type --}}
                    @if($jobTypes->isNotEmpty())
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
                        <h4 class="font-semibold text-[#073057] mb-4">Job Type</h4>
                        <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                            @foreach($jobTypes as $jobType)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="type[]" value="{{ $jobType->id }}"
                                    {{ in_array((string) $jobType->id, array_map('strval', (array) request('type')), true) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" />
                                <span class="text-sm text-[#4B5563] group-hover:text-[#073057]">{{ $jobType->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Salary Range (monthly) --}}
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
                        <h4 class="font-semibold text-[#073057] mb-4">Salary Range</h4>
                        <div class="space-y-3">
                            @foreach(['0-5000' => '$0 - $5,000', '5000-10000' => '$5,000 - $10,000', '10000-15000' => '$10,000 - $15,000', '15000+' => '$15,000+'] as $value => $label)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="salary[]" value="{{ $value }}"
                                    {{ in_array($value, (array) request('salary'), true) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" />
                                <span class="text-sm text-[#4B5563] group-hover:text-[#073057]">{{ $label }}/month</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 px-6 bg-[#073057] hover:bg-[#0a4275] text-white font-semibold rounded-xl transition">Apply Filters</button>
                    @if(request()->hasAny(['keyword', 's', 'location', 'category', 'type', 'salary', 'sort']))
                        <a href="{{ route('job.index') }}" class="block text-center text-sm text-[#6B7280] hover:text-[#073057]">Clear all filters</a>
                    @endif
                </form>
            </aside>

            {{-- Job Listings --}}
            <div class="lg:col-span-3" x-data="{ view: 'list' }">
                {{-- Results Header --}}
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        @if($jobs->total() > 0)
                            <p class="text-[#6B7280]">Showing <span class="font-semibold text-[#073057]">{{ $jobs->firstItem() }}-{{ $jobs->lastItem() }}</span> of <span class="font-semibold text-[#073057]">{{ $jobs->total() }}</span> jobs</p>
                        @else
                            <p class="text-[#6B7280]">No jobs found</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('job.index') }}" method="GET">
                            <x-ui.hidden-query :except="['sort']" />
                            <select name="sort" onchange="this.form.submit()" class="px-4 py-2 border border-[#E5E7EB] rounded-lg text-sm text-[#4B5563] focus:ring-2 focus:ring-[#1AAD94] outline-none">
                                <option value="relevant" {{ request('sort', 'relevant') === 'relevant' ? 'selected' : '' }}>Most Relevant</option>
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="salary" {{ request('sort') === 'salary' ? 'selected' : '' }}>Highest Salary</option>
                            </select>
                        </form>
                        <div class="hidden sm:flex gap-1">
                            <button type="button" @click="view = 'list'" :class="view === 'list' ? 'bg-[#1AAD94] text-white' : 'text-[#6B7280] hover:bg-[#F3F4F6]'" class="p-2 rounded-lg transition" aria-label="List view">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </button>
                            <button type="button" @click="view = 'grid'" :class="view === 'grid' ? 'bg-[#1AAD94] text-white' : 'text-[#6B7280] hover:bg-[#F3F4F6]'" class="p-2 rounded-lg transition" aria-label="Grid view">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Job Cards --}}
                @if($jobs->isEmpty())
                    <div class="bg-white rounded-xl border border-dashed border-[#D1D5DB] p-16 text-center">
                        <svg class="w-12 h-12 mx-auto text-[#D1D5DB] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="text-[#6B7280] mb-4">No jobs match your search right now. Try adjusting your filters.</p>
                        @if(request()->hasAny(['keyword', 's', 'location', 'category', 'type', 'salary']))
                            <a href="{{ route('job.index') }}" class="inline-block text-sm font-semibold text-[#1AAD94] hover:underline">Clear all filters</a>
                        @endif
                    </div>
                @else
                <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : 'space-y-4'">
                    @foreach($jobs as $job)
                    @php
                        $detailUrl = route('job.detail', $job->slug);
                        $companyName = $job->company?->name ?? 'JCL Talent Network';
                        $locationName = $job->location?->name ?? $job->address ?? 'Worldwide';
                        $typeName = $job->jobType?->name ?? ucfirst((string) $job->hours_type ?: 'Job');
                        $salaryLabel = ($job->salary_min || $job->salary_max)
                            ? trim(($job->salary_min ? '$'.number_format((float) $job->salary_min) : '').($job->salary_min && $job->salary_max ? ' - ' : '').($job->salary_max ? '$'.number_format((float) $job->salary_max) : '')).($job->salary_type ? ' / '.$job->salary_type : '')
                            : 'Negotiable';
                        $isSaved = in_array($job->id, $savedJobIds ?? [], true);
                    @endphp
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 hover:shadow-lg hover:border-[#1AAD94]/30 transition group {{ $job->is_featured ? 'ring-2 ring-[#1AAD94]/20' : '' }}">
                        <div class="flex gap-4" :class="view === 'grid' ? 'flex-col' : 'flex-col md:flex-row md:items-center'">
                            {{-- Company Logo --}}
                            <div class="w-16 h-16 bg-[#F3F4F6] rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if($job->company?->logo)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($job->company->logo, ['http', '/']) ? $job->company->logo : asset('storage/'.$job->company->logo) }}" alt="{{ $companyName }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl font-bold text-[#073057]">{{ strtoupper(substr($companyName, 0, 2)) }}</span>
                                @endif
                            </div>

                            {{-- Job Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-lg font-semibold text-[#073057] group-hover:text-[#1AAD94] transition">
                                        <a href="{{ $detailUrl }}" class="hover:underline">{{ $job->title }}</a>
                                    </h3>
                                    @if($job->is_featured)
                                    <span class="px-2 py-0.5 bg-[#1AAD94] text-white text-xs font-medium rounded-full">Featured</span>
                                    @endif
                                    @if($job->is_urgent)
                                    <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-medium rounded-full">Urgent</span>
                                    @endif
                                </div>
                                <p class="text-[#6B7280] mb-2">{{ $companyName }}</p>
                                <div class="flex flex-wrap gap-4 text-sm text-[#6B7280]">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $locationName }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $salaryLabel }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $job->created_at?->diffForHumans() ?? 'Recently' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 md:gap-3" :class="view === 'grid' ? '' : 'md:flex-col'">
                                <a href="{{ $detailUrl }}#apply" class="px-5 py-2.5 bg-[#073057] hover:bg-[#0a4275] text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">Apply Now</a>
                                @auth
                                    <form method="POST" action="{{ route('user.bookmark.toggle', $job) }}">
                                        @csrf
                                        <button type="submit" class="p-2.5 rounded-lg transition {{ $isSaved ? 'text-red-500 bg-red-50' : 'text-[#6B7280] hover:text-red-500 hover:bg-red-50' }}" title="{{ $isSaved ? 'Remove from saved jobs' : 'Save job' }}">
                                            <svg class="w-5 h-5" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('auth.login') }}?intended={{ urlencode($detailUrl) }}" class="p-2.5 text-[#6B7280] hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Sign in to save jobs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </a>
                                @endauth
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-[#E5E7EB]">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">{{ $typeName }}</span>
                            @if($job->category)
                                <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-xs font-medium rounded-full">{{ $job->category->name }}</span>
                            @endif
                            @if($job->experience_required)
                                <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-xs font-medium rounded-full">{{ $job->experience_required }}</span>
                            @endif
                            <a href="{{ $detailUrl }}" class="ml-auto inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-[#1AAD94] hover:gap-2 transition-all">
                                View Details
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $jobs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
