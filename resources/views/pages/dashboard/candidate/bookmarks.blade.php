@extends('layouts.dashboard')

@section('title', 'Saved Jobs')
@section('page-title', 'Saved Jobs')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Saved Jobs</h2>
            <p class="text-[#6B7280]">Jobs you've saved for later review</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-[#6B7280]">{{ $totalSaved ?? 0 }} jobs saved</span>
        </div>
    </div>

    @if(isset($savedJobs) && $savedJobs->count() > 0)
    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="relative flex-1 max-w-md">
            <input type="text" placeholder="Search saved jobs..." class="w-full pl-10 pr-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none" />
            <svg class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    {{-- Saved Jobs Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($savedJobs as $wishlist)
        @php
            $job = $wishlist->wishlistable;
            $company = $job?->company;
            $initial = $company ? substr($company->name, 0, 1) : 'J';
        @endphp
        @if($job)
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 hover:shadow-md transition group relative">
            {{-- Remove Bookmark Button --}}
            <form action="{{ route('user.bookmark.remove', $wishlist) }}" method="POST" class="absolute top-4 right-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-amber-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Remove from Saved">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                </button>
            </form>

            <div class="flex items-start gap-4 pr-10">
                <div class="w-14 h-14 bg-[#073057]/10 rounded-xl flex items-center justify-center text-[#073057] font-bold text-lg shrink-0">
                    {{ $initial }}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-[#073057] group-hover:text-[#1AAD94] transition truncate">{{ $job->title }}</h4>
                    <p class="text-sm text-[#1AAD94]">{{ $company?->name ?? 'Company' }}</p>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-[#6B7280]">
                        @if($job->location)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $job->location->name }}
                        </span>
                        @endif
                        @if($job->salary_max)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ${{ number_format($job->salary_min ?? 0) }} - ${{ number_format($job->salary_max) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-[#E5E7EB]">
                <div class="flex items-center gap-2">
                    @if($job->jobType)
                    <span class="px-2.5 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-medium rounded-lg">{{ $job->jobType->name }}</span>
                    @endif
                    @if($job->deadline)
                    <span class="text-xs text-[#6B7280]">Closes {{ $job->deadline->format('M d, Y') }}</span>
                    @endif
                </div>
                <a href="{{ route('job.show', $job->slug ?? $job->id) }}" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition cursor-pointer">View Job</a>
            </div>

            <p class="text-xs text-[#9CA3AF] mt-3">Saved {{ $wishlist->created_at->diffForHumans() }}</p>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($savedJobs->hasPages())
    <div class="flex items-center justify-between mt-6">
        <p class="text-sm text-[#6B7280]">Showing {{ $savedJobs->firstItem() }}-{{ $savedJobs->lastItem() }} of {{ $savedJobs->total() }} jobs</p>
        {{ $savedJobs->links() }}
    </div>
    @endif

    @else
    {{-- Empty State --}}
    <div class="text-center py-16 bg-white rounded-xl border border-[#E5E7EB]">
        <div class="w-20 h-20 bg-[#F3F4F6] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-[#073057] mb-2">No Saved Jobs</h3>
        <p class="text-[#6B7280] mb-6">Start saving jobs you're interested in to review later</p>
        <a href="{{ route('job.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition cursor-pointer">
            Browse Jobs
        </a>
    </div>
    @endif
@endsection
