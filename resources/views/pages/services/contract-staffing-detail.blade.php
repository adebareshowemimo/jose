@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')

{{-- Hero --}}
<section class="relative py-16 bg-[#073057] overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-[#073057] via-[#073057] to-[#0a4275] opacity-95"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#1AAD94]/15 text-[#7DE1D1] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.12em]">
                <iconify-icon icon="lucide:briefcase"></iconify-icon>
                Contract Role
            </span>
            @if ($job->is_urgent)
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-red-300">Urgent</span>
            @endif
            @if ($job->is_featured)
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-amber-300">Featured</span>
            @endif
        </div>
        <h1 class="text-[36px] md:text-[48px] font-extrabold text-white leading-tight">{{ $job->title }}</h1>
        <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-white/70 text-sm">
            @if ($job->location || $job->address)
                <span class="inline-flex items-center gap-1.5"><iconify-icon icon="lucide:map-pin"></iconify-icon> {{ $job->location?->name ?? $job->address }}</span>
            @endif
            @if ($job->hours)
                <span class="inline-flex items-center gap-1.5"><iconify-icon icon="lucide:clock"></iconify-icon> {{ $job->hours }}</span>
            @endif
            @if ($job->category)
                <span class="inline-flex items-center gap-1.5"><iconify-icon icon="lucide:tag"></iconify-icon> {{ $job->category->name }}</span>
            @endif
            @if ($job->deadline)
                <span class="inline-flex items-center gap-1.5"><iconify-icon icon="lucide:calendar"></iconify-icon> Apply by {{ $job->deadline->format('M d, Y') }}</span>
            @endif
        </div>
    </div>
</section>

{{-- Main content --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-[1fr_360px] gap-10 items-start">
            {{-- Description --}}
            <article class="prose prose-slate max-w-none">
                {!! $job->description !!}

                @if ($job->qualification)
                    <h3 class="mt-8">Qualifications</h3>
                    <p>{{ $job->qualification }}</p>
                @endif
            </article>

            {{-- Sidebar --}}
            <aside class="space-y-6 lg:sticky lg:top-24">
                <div class="rounded-2xl border border-[#E5E7EB] bg-[#F9FAFB] p-6">
                    <h3 class="text-[14px] font-bold uppercase tracking-[0.1em] text-[#073057] mb-4">Role Snapshot</h3>
                    <dl class="space-y-3 text-sm">
                        @if ($job->salary_min || $job->salary_max)
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#6B7280]">Salary</dt>
                                <dd class="font-bold text-[#073057] text-right">
                                    {{ $job->salary_min ? number_format((float) $job->salary_min, 0) : '?' }}–{{ $job->salary_max ? number_format((float) $job->salary_max, 0) : '?' }}
                                    <span class="font-normal text-[#6B7280] text-xs">/ {{ $job->salary_type ?? '—' }}</span>
                                </dd>
                            </div>
                        @endif
                        @if ($job->experience_required)
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#6B7280]">Experience</dt>
                                <dd class="font-semibold text-[#073057] text-right">{{ $job->experience_required }}</dd>
                            </div>
                        @endif
                        @if ($job->vacancies)
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#6B7280]">Vacancies</dt>
                                <dd class="font-semibold text-[#073057]">{{ $job->vacancies }}</dd>
                            </div>
                        @endif
                        @if ($job->hours_type)
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#6B7280]">Schedule</dt>
                                <dd class="font-semibold text-[#073057]">{{ ucfirst(str_replace('-', ' ', $job->hours_type)) }}</dd>
                            </div>
                        @endif
                        @if ($job->deadline)
                            <div class="flex justify-between gap-3">
                                <dt class="text-[#6B7280]">Deadline</dt>
                                <dd class="font-semibold text-[#073057]">{{ $job->deadline->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <x-jobs.application-form :job="$job" />
            </aside>
        </div>
    </div>
</section>

@endsection
