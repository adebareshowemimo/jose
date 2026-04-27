@extends('layouts.dashboard')

@section('title', 'Browse Resumes')
@section('page-title', 'Browse Resumes')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Browse Resumes</h2>
            <p class="text-[#6B7280]">Search real candidate profiles with uploaded CVs.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">Searchable CVs</p>
            <p class="mt-2 text-3xl font-bold text-[#073057]">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">Available Candidates</p>
            <p class="mt-2 text-3xl font-bold text-[#1AAD94]">{{ number_format($stats['available'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <p class="text-sm text-[#6B7280]">New CVs This Week</p>
            <p class="mt-2 text-3xl font-bold text-[#073057]">{{ number_format($stats['new_this_week'] ?? 0) }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('employer.resumes') }}" class="bg-white rounded-xl border border-[#E5E7EB] p-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#073057] mb-2">Search Keywords</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, job title, skill, certification..."
                        class="w-full pl-10 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <svg class="w-5 h-5 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Position</label>
                <input type="text" name="position" value="{{ request('position') }}" placeholder="Chief Officer"
                    class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Experience</label>
                <select name="experience" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Any</option>
                    @foreach(['0-2' => '0-2 Years', '3-5' => '3-5 Years', '5-10' => '5-10 Years', '10+' => '10+ Years'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('experience') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Location</label>
                <select name="location_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Any</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected((string) request('location_id') === (string) $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Availability</label>
                <select name="availability" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Any</option>
                    <option value="available" @selected(request('availability') === 'available')>Available</option>
                    <option value="unavailable" @selected(request('availability') === 'unavailable')>Unavailable</option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-4 mt-4 pt-4 border-t border-[#E5E7EB]">
            <select name="sort" class="px-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <option value="">Sort: Newest</option>
                <option value="experience" @selected(request('sort') === 'experience')>Most Experienced</option>
                <option value="name" @selected(request('sort') === 'name')>Name A-Z</option>
            </select>
            <div class="flex flex-wrap gap-2">
                @if(request()->hasAny(['search', 'position', 'experience', 'location_id', 'availability', 'sort']))
                    <a href="{{ route('employer.resumes') }}" class="px-4 py-2.5 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Reset</a>
                @endif
                <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Search Resumes</button>
            </div>
        </div>
    </form>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-[#6B7280]">
            Showing <span class="font-semibold text-[#073057]">{{ $candidates->firstItem() ?? 0 }}-{{ $candidates->lastItem() ?? 0 }}</span>
            of <span class="font-semibold text-[#073057]">{{ $candidates->total() }}</span> candidates
        </p>
    </div>

    <div class="space-y-4">
        @forelse($candidates as $candidate)
            @php
                $candidateName = $candidate->user?->name ?? 'Unknown candidate';
                $initials = collect(explode(' ', $candidateName))->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))->take(2)->join('');
                $resume = $candidate->resumes->firstWhere('is_default', true) ?? $candidate->resumes->first();
                $salary = $candidate->expected_salary ? number_format((float) $candidate->expected_salary).' / '.($candidate->salary_type ?? 'salary') : 'Salary not set';
            @endphp

            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-start gap-4">
                    <div class="w-16 h-16 bg-[#073057]/10 rounded-full flex items-center justify-center text-[#073057] font-bold text-xl shrink-0">
                        {{ $initials ?: 'C' }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-lg text-[#073057]">{{ $candidateName }}</h4>
                                    <span class="px-2 py-0.5 {{ $candidate->is_available ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'bg-gray-100 text-gray-600' }} text-xs font-medium rounded">
                                        {{ $candidate->is_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                </div>
                                <p class="text-[#1AAD94] font-medium">{{ $candidate->title ?? 'Candidate profile' }}</p>
                            </div>
                            <span class="text-xs text-[#6B7280]">CV uploaded {{ $resume?->created_at?->diffForHumans() }}</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-[#6B7280] mb-3">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $candidate->experience_years ? $candidate->experience_years.' years' : 'Experience not set' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 0 016 0z"/></svg>
                                {{ $candidate->location?->name ?? 'Location not set' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $salary }}
                            </span>
                        </div>

                        @if($candidate->skills->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($candidate->skills->take(8) as $skill)
                                    <span class="px-2.5 py-1 bg-[#F3F4F6] text-[#4B5563] text-xs font-medium rounded-lg">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if($candidate->categories->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs text-[#6B7280]">Categories:</span>
                                @foreach($candidate->categories->take(4) as $category)
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex md:flex-col gap-2 md:w-auto w-full">
                        @if($candidate->slug)
                            <a href="{{ route('candidate.detail', $candidate->slug) }}" target="_blank" class="flex-1 md:flex-none px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-semibold rounded-xl transition text-center">View Profile</a>
                        @endif
                        @if($resume)
                            <a href="{{ asset($resume->file_path) }}" target="_blank" class="flex-1 md:flex-none px-5 py-2.5 border border-[#E5E7EB] text-[#4B5563] text-sm font-semibold rounded-xl hover:bg-[#F9FAFB] transition text-center">Open CV</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-10 text-center">
                <h3 class="text-lg font-bold text-[#073057]">No resumes found</h3>
                <p class="mt-2 text-[#6B7280]">Searchable candidates with uploaded CVs will appear here. Adjust filters if you expected results.</p>
            </div>
        @endforelse
    </div>

    @if($candidates->hasPages() || $candidates->total() > 0)
        <div class="bg-white rounded-xl border border-[#E5E7EB] px-4 py-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <p class="text-sm text-[#6B7280]">
                    Showing {{ $candidates->firstItem() ?? 0 }}-{{ $candidates->lastItem() ?? 0 }} of {{ $candidates->total() }} candidates
                </p>
                {{ $candidates->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
