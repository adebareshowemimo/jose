@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')

{{-- Hero --}}
<section class="relative py-20 bg-[#073057] overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-[#073057] via-[#073057] to-[#0a4275] opacity-90"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[42px] md:text-[52px] font-extrabold text-white leading-tight">Open Contract Staffing Roles</h1>
        <p class="mt-3 text-white/70 max-w-xl text-base">Browse current fixed-term and project-based roles. Submit an application directly from any listing.</p>
    </div>
</section>

{{-- Filters --}}
<section class="bg-white border-b border-gray-100">
    <div class="container mx-auto px-6 py-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#6B7280] mb-1 block">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Role title or keyword..." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
            <div>
                <label class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#6B7280] mb-1 block">Category</label>
                <select name="category" class="px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (int) request('category') === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#6B7280] mb-1 block">Location</label>
                <select name="location" class="px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All locations</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (int) request('location') === (int) $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] text-white text-sm font-bold uppercase tracking-widest rounded-lg hover:brightness-110">Filter</button>
            @if(request()->hasAny(['q', 'category', 'location']))
                <a href="{{ route('services.contract-staffing.jobs') }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>
</section>

{{-- Listings --}}
<section class="py-16 bg-[#F9FAFB] min-h-[480px]">
    <div class="container mx-auto px-6">
        @if ($jobs->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-16 text-center">
                <iconify-icon icon="lucide:briefcase" class="text-5xl text-gray-300 mb-3"></iconify-icon>
                <p class="text-[#6B7280]">No open contract roles match your filters right now.</p>
                <a href="{{ route('services.contract-staffing') }}" class="inline-block mt-4 text-sm font-bold uppercase tracking-[0.1em] text-[#1AAD94] hover:underline">Back to overview</a>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($jobs as $job)
                    <a href="{{ route('services.contract-staffing.detail', $job->slug) }}" class="group flex flex-col rounded-[24px] border border-[#E5E7EB] bg-white p-7 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#1AAD94]/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#1AAD94]">
                                <iconify-icon icon="lucide:briefcase"></iconify-icon>
                                Contract
                            </span>
                            @if ($job->is_urgent)
                                <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-red-600">Urgent</span>
                            @endif
                        </div>
                        <h3 class="text-[18px] font-extrabold text-[#073057] mb-2">{{ $job->title }}</h3>
                        <div class="text-sm text-[#6B7280] mb-4 flex flex-wrap gap-x-3 gap-y-1">
                            @if ($job->location || $job->address)
                                <span class="inline-flex items-center gap-1"><iconify-icon icon="lucide:map-pin"></iconify-icon> {{ $job->location?->name ?? $job->address }}</span>
                            @endif
                            @if ($job->hours)
                                <span class="inline-flex items-center gap-1"><iconify-icon icon="lucide:clock"></iconify-icon> {{ $job->hours }}</span>
                            @endif
                            @if ($job->category)
                                <span class="inline-flex items-center gap-1"><iconify-icon icon="lucide:tag"></iconify-icon> {{ $job->category->name }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-[#6B7280] line-clamp-3 mb-5">{{ \Illuminate\Support\Str::limit(strip_tags((string) $job->description), 160) }}</p>
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                            @if ($job->salary_min || $job->salary_max)
                                <span class="text-[13px] font-bold text-[#073057]">
                                    {{ $job->salary_min ? number_format((float) $job->salary_min, 0) : '?' }}–{{ $job->salary_max ? number_format((float) $job->salary_max, 0) : '?' }}
                                    <span class="text-[11px] font-normal text-[#6B7280]">/ {{ $job->salary_type ?? '—' }}</span>
                                </span>
                            @else
                                <span class="text-[12px] text-[#6B7280]">Salary on request</span>
                            @endif
                            <span class="inline-flex items-center gap-1 text-[12px] font-bold uppercase tracking-[0.08em] text-[#1AAD94] group-hover:gap-2 transition-all">
                                View
                                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
