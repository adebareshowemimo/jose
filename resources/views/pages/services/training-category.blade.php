@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')
@php
    $img = $jclImages ?? [];
    $heroUrl = $category->hero_image_url
        ?: ($category->slug === 'soft-skills' ? ($img['soft_skills_hero'] ?? '') : ($img['safety_officer'] ?? ''));
    $bullets = is_array($category->bullet_points) ? $category->bullet_points : [];
@endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $heroUrl }}"
         alt="{{ $category->name }}"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">{{ $category->name }}</h1>
        @if ($category->short_description)
            <p class="mt-4 max-w-xl text-lg text-white/70">{{ $category->short_description }}</p>
        @endif
    </div>
</section>

{{-- Main Content --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="{{ $category->icon ?: 'lucide:graduation-cap' }}"></iconify-icon>
                    {{ $category->name }}
                </div>
                @if ($category->intro_html)
                    <div class="prose max-w-none text-[#4B5563] leading-relaxed mb-8">
                        {!! $category->intro_html !!}
                    </div>
                @endif
                @if (! empty($bullets))
                    <ul class="space-y-3 mb-10">
                        @foreach ($bullets as $bullet)
                            <li class="flex items-start gap-2 text-[#4B5563]">
                                <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon>
                                <span>{{ $bullet }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                        Enquire Now
                        <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                    </a>
                </div>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $heroUrl }}"
                     alt="{{ $category->name }}"
                     class="w-full h-[480px] object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- Available Programs --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-4">Programs</div>
            <h2 class="text-[38px] font-extrabold text-[#073057] leading-tight">Available {{ $category->name }} Programs</h2>
            <p class="mt-3 max-w-2xl mx-auto text-lg text-[#6B7280]">Courses currently open for enrolment.</p>
        </div>

        @if ($dbPrograms->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($dbPrograms as $program)
                    <article class="flex flex-col rounded-[24px] border border-[#E5E7EB] bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        @if ($program->image_url)
                            <a href="{{ route('training.show', $program->slug) }}" class="block aspect-[16/10] bg-gray-100 overflow-hidden">
                                <img src="{{ $program->image_url }}" alt="{{ $program->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                            </a>
                        @endif

                        <div class="p-7 flex-1 flex flex-col">
                            @if (! $program->image_url)
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#073057]/8 text-[#073057]">
                                    <iconify-icon icon="{{ $category->icon ?: 'lucide:graduation-cap' }}" class="text-xl"></iconify-icon>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                @if ($program->category)
                                    <span class="inline-block rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#1AAD94]">{{ $program->category }}</span>
                                @endif
                                @if ($program->is_featured)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wider">
                                        <iconify-icon icon="lucide:star" class="text-xs"></iconify-icon>
                                        Featured
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-[17px] font-extrabold text-[#073057] mb-2 leading-snug">
                                <a href="{{ route('training.show', $program->slug) }}" class="hover:text-[#1AAD94] transition-colors">{{ $program->title }}</a>
                            </h4>

                            @if ($program->short_description)
                                <p class="text-[#6B7280] text-sm leading-relaxed flex-grow mb-5 line-clamp-3">{{ $program->short_description }}</p>
                            @endif

                            <div class="flex items-center flex-wrap gap-4 text-[12px] text-[#9CA3AF] font-semibold border-t border-[#F3F4F6] pt-4 mb-5">
                                @if ($program->duration)
                                    <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:clock" class="text-[#1AAD94]"></iconify-icon> {{ $program->duration }}</span>
                                @endif
                                @if ($program->level)
                                    <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:bar-chart-2" class="text-[#1AAD94]"></iconify-icon> {{ $program->level }}</span>
                                @endif
                                @if ($program->starts_at)
                                    <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:calendar" class="text-[#1AAD94]"></iconify-icon> {{ $program->starts_at->format('M d') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-base font-extrabold text-[#073057]">
                                    @if ($program->isFree())
                                        <span class="text-[#1AAD94]">Free</span>
                                    @else
                                        {{ money($program->price, $program->currency ?? 'USD') }}
                                    @endif
                                </span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('training.show', $program->slug) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 border-2 border-[#073057] hover:bg-[#073057] hover:text-white text-[#073057] text-[11px] font-bold uppercase tracking-wider rounded-lg transition">
                                        <iconify-icon icon="lucide:eye" class="text-xs"></iconify-icon>
                                        View
                                    </a>
                                    @auth
                                        <form method="POST" action="{{ route('training.enrol', $program) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg transition shadow">
                                                <iconify-icon icon="lucide:graduation-cap" class="text-xs"></iconify-icon>
                                                {{ $program->isFree() ? 'Join' : 'Apply' }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('auth.login', ['redirect' => route('training.show', $program->slug)]) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg transition shadow">
                                            <iconify-icon icon="lucide:graduation-cap" class="text-xs"></iconify-icon>
                                            {{ $program->isFree() ? 'Join' : 'Apply' }}
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="max-w-xl mx-auto text-center rounded-[24px] border border-dashed border-[#E5E7EB] bg-white px-8 py-12">
                <iconify-icon icon="lucide:calendar-clock" class="text-3xl text-[#1AAD94] mb-3"></iconify-icon>
                <p class="text-[#6B7280] text-sm">Programs being scheduled — <a href="{{ route('contact.index') }}" class="text-[#1AAD94] font-semibold hover:underline">contact us</a> for upcoming dates.</p>
            </div>
        @endif
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Need a customised training program?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Speak to one of our specialists today.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact JCL
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
