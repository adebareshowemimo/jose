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
            <form class="bg-white rounded-2xl shadow-2xl p-3 md:p-4">
                <div class="grid md:grid-cols-4 gap-3">
                    <div class="md:col-span-1">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="keyword" placeholder="Job title, rank, skills..." 
                                class="w-full pl-12 pr-4 py-3.5 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <input type="text" name="location" placeholder="City, country, or port..." 
                                class="w-full pl-12 pr-4 py-3.5 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <select name="category" class="w-full px-4 py-3.5 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none text-[#4B5563] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:20px] bg-[right_12px_center] bg-no-repeat">
                            <option value="">All Categories</option>
                            <option>Deck Officers</option>
                            <option>Engineering</option>
                            <option>Hospitality</option>
                            <option>Catering</option>
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
                <button @click="open = !open" class="lg:hidden w-full flex items-center justify-between p-4 bg-white rounded-xl border border-[#E5E7EB] mb-4">
                    <span class="font-semibold text-[#073057]">Filters</span>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-[#6B7280] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div :class="{ 'hidden': !open }" class="lg:block space-y-4">
                    {{-- Job Type --}}
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
                        <h4 class="font-semibold text-[#073057] mb-4">Job Type</h4>
                        <div class="space-y-3">
                            @foreach(['Full Time', 'Contract', 'Freelance', 'Temporary', 'Remote'] as $type)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" class="w-4 h-4 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" />
                                <span class="text-sm text-[#4B5563] group-hover:text-[#073057]">{{ $type }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Experience Level --}}
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
                        <h4 class="font-semibold text-[#073057] mb-4">Experience Level</h4>
                        <div class="space-y-3">
                            @foreach(['Entry Level', '1-2 Years', '3-5 Years', '5+ Years', '10+ Years'] as $exp)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" class="w-4 h-4 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" />
                                <span class="text-sm text-[#4B5563] group-hover:text-[#073057]">{{ $exp }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Salary Range --}}
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
                        <h4 class="font-semibold text-[#073057] mb-4">Salary Range</h4>
                        <div class="space-y-3">
                            @foreach(['$0 - $5,000', '$5,000 - $10,000', '$10,000 - $15,000', '$15,000+'] as $range)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" class="w-4 h-4 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" />
                                <span class="text-sm text-[#4B5563] group-hover:text-[#073057]">{{ $range }}/month</span>
                            </label>
                            @endforeach
                        </div>
                    </div>


                </div>
            </aside>

            {{-- Job Listings --}}
            <div class="lg:col-span-3" x-data="{ view: 'list' }">
                {{-- Results Header --}}
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-[#6B7280]">Showing <span class="font-semibold text-[#073057]">{{ $showing ?? '1-10' }}</span> of <span class="font-semibold text-[#073057]">{{ $totalResults ?? '2,543' }}</span> jobs</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <select class="px-4 py-2 border border-[#E5E7EB] rounded-lg text-sm text-[#4B5563] focus:ring-2 focus:ring-[#1AAD94] outline-none">
                            <option>Most Relevant</option>
                            <option>Newest</option>
                            <option>Highest Salary</option>
                        </select>
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
                <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : 'space-y-4'">
                    @php
                    $sampleJobs = [
                        ['title' => 'Chief Officer - Container Vessel', 'company' => 'Maersk Line', 'location' => 'Rotterdam, Netherlands', 'salary' => '$8,500/month', 'type' => 'Full Time', 'urgent' => true, 'featured' => true, 'posted' => '2 hours ago'],
                        ['title' => 'Second Engineer - LNG Tanker', 'company' => 'Shell Shipping', 'location' => 'Singapore', 'salary' => '$9,200/month', 'type' => 'Contract', 'urgent' => false, 'featured' => true, 'posted' => '5 hours ago'],
                        ['title' => 'Hotel Manager - Cruise Ship', 'company' => 'Royal Caribbean', 'location' => 'Miami, USA', 'salary' => '$7,800/month', 'type' => 'Full Time', 'urgent' => false, 'featured' => false, 'posted' => '1 day ago'],
                        ['title' => 'Able Seaman - Bulk Carrier', 'company' => 'Pacific Basin', 'location' => 'Hong Kong', 'salary' => '$3,200/month', 'type' => 'Contract', 'urgent' => true, 'featured' => false, 'posted' => '2 days ago'],
                    ];
                    @endphp

                    @foreach($jobs ?? $sampleJobs as $job)
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 hover:shadow-lg hover:border-[#1AAD94]/30 transition group {{ ($job['featured'] ?? false) ? 'ring-2 ring-[#1AAD94]/20' : '' }}">
                        <div class="flex gap-4" :class="view === 'grid' ? 'flex-col' : 'flex-col md:flex-row md:items-center'">
                            {{-- Company Logo --}}
                            <div class="w-16 h-16 bg-[#F3F4F6] rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold text-[#073057]">{{ strtoupper(substr($job['company'] ?? 'C', 0, 2)) }}</span>
                            </div>
                            
                            {{-- Job Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-lg font-semibold text-[#073057] group-hover:text-[#1AAD94] transition">{{ $job['title'] }}</h3>
                                    @if($job['featured'] ?? false)
                                    <span class="px-2 py-0.5 bg-[#1AAD94] text-white text-xs font-medium rounded-full">Featured</span>
                                    @endif
                                    @if($job['urgent'] ?? false)
                                    <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-medium rounded-full">Urgent</span>
                                    @endif
                                </div>
                                <p class="text-[#6B7280] mb-2">{{ $job['company'] }}</p>
                                <div class="flex flex-wrap gap-4 text-sm text-[#6B7280]">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $job['location'] }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $job['salary'] }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $job['posted'] ?? $job['type'] ?? 'Recently' }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Actions --}}
                            <div class="flex items-center gap-2 md:gap-3" :class="view === 'grid' ? '' : 'md:flex-col'">
                                <a href="#" class="px-5 py-2.5 bg-[#073057] hover:bg-[#0a4275] text-white text-sm font-semibold rounded-lg transition">Apply Now</a>
                                <button class="p-2.5 text-[#6B7280] hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Save Job">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Tags --}}
                        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-[#E5E7EB]">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">{{ $job['type'] }}</span>
                            <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-xs font-medium rounded-full">Container Ship</span>
                            <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-xs font-medium rounded-full">STCW Required</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="flex items-center justify-center gap-2 mt-8">
                    <button class="p-2 text-[#6B7280] hover:bg-[#F3F4F6] rounded-lg" disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="w-10 h-10 bg-[#1AAD94] text-white font-medium rounded-lg">1</button>
                    <button class="w-10 h-10 text-[#4B5563] hover:bg-[#F3F4F6] font-medium rounded-lg">2</button>
                    <button class="w-10 h-10 text-[#4B5563] hover:bg-[#F3F4F6] font-medium rounded-lg">3</button>
                    <span class="px-2 text-[#9CA3AF]">...</span>
                    <button class="w-10 h-10 text-[#4B5563] hover:bg-[#F3F4F6] font-medium rounded-lg">25</button>
                    <button class="p-2 text-[#6B7280] hover:bg-[#F3F4F6] rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
