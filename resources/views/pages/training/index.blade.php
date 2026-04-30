@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')
@php
    $img = $jclImages ?? [];
    $isApprenticeship = ($filterType ?? null) === 'apprenticeship';
    $heroEyebrow = $isApprenticeship ? 'Apprenticeship Programmes' : 'Professional Training';
    $heroTitle = $isApprenticeship
        ? 'Launch your maritime career through structured apprenticeships'
        : 'Internationally recognised training, on your schedule';
    $heroSub = $isApprenticeship
        ? 'Earn while you learn. Hands-on apprenticeships into maritime, offshore, and energy careers — paid programmes that combine certification with real industry placement.'
        : 'STCW, BOSIET, soft-skills and technical programmes built around international competency frameworks. Enrol online and get instant confirmation.';
    $listingRoute = $isApprenticeship ? 'career.apprenticeship' : 'training.index';
@endphp

{{-- Hero --}}
<section class="relative h-[460px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['training_hero'] ?? '' }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-25" loading="eager">
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/90 to-[#073057]/40"></div>
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1AAD94]/15 border border-[#1AAD94]/30 text-[#7DE1D1] text-[11px] font-bold uppercase tracking-[0.15em] mb-5">
            <iconify-icon icon="lucide:graduation-cap"></iconify-icon>
            {{ $heroEyebrow }}
        </span>
        <h1 class="text-[40px] md:text-[58px] font-extrabold text-white leading-[1.05] max-w-3xl">{{ $heroTitle }}</h1>
        <p class="mt-5 max-w-2xl text-lg text-white/75 leading-relaxed">{{ $heroSub }}</p>
    </div>
</section>

{{-- Programs grid --}}
<section class="py-16 md:py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        @if (! empty($categories))
            <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
                <a href="{{ route($listingRoute) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition
                          {{ ! request('category') ? 'bg-[#073057] text-white' : 'bg-white text-[#073057] hover:bg-[#073057]/5 border border-gray-200' }}">
                    All
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route($listingRoute, ['category' => $cat]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition
                              {{ request('category') === $cat ? 'bg-[#073057] text-white' : 'bg-white text-[#073057] hover:bg-[#073057]/5 border border-gray-200' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($programs->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-16 text-center max-w-2xl mx-auto">
                <iconify-icon icon="lucide:graduation-cap" class="text-5xl text-gray-300"></iconify-icon>
                <h3 class="mt-3 text-xl font-bold text-[#073057]">No programmes available right now</h3>
                <p class="mt-2 text-[#6B7280]">New programmes are added regularly. <a href="{{ route('contact.index') }}" class="text-[#1AAD94] font-semibold">Get in touch</a> to be notified when relevant intakes open.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($programs as $program)
                    <article class="group flex flex-col bg-white rounded-2xl border border-[#E0E0E0] shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        <a href="{{ route('training.show', $program->slug) }}"
                           class="block aspect-[16/10] overflow-hidden relative {{ $program->image_url ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }}">
                            @if ($program->image_url)
                                <img src="{{ $program->image_url }}" alt="{{ $program->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center px-6">
                                    <span class="text-center font-extrabold text-white/90 text-2xl leading-tight">{{ $program->title }}</span>
                                </div>
                            @endif
                            @if ($program->category)
                                <span class="absolute top-3 left-3 inline-flex px-3 py-1 rounded-full bg-white/95 backdrop-blur text-[#1AAD94] text-[11px] font-bold uppercase tracking-wider shadow">{{ $program->category }}</span>
                            @endif
                            @if ($program->is_featured)
                                <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-400 text-amber-900 text-[10px] font-bold uppercase tracking-wider shadow">
                                    <iconify-icon icon="lucide:star" class="text-xs"></iconify-icon>
                                    Featured
                                </span>
                            @endif
                        </a>

                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-lg font-extrabold text-[#073057] mb-2 group-hover:text-[#1AAD94] transition-colors leading-snug">
                                <a href="{{ route('training.show', $program->slug) }}">{{ $program->title }}</a>
                            </h3>

                            <div class="space-y-1 mb-3 text-xs text-[#6B7280]">
                                @if ($program->duration)
                                    <p class="flex items-center gap-1.5"><iconify-icon icon="lucide:clock" class="text-[#1AAD94]"></iconify-icon> {{ $program->duration }}</p>
                                @endif
                                @if ($program->level)
                                    <p class="flex items-center gap-1.5"><iconify-icon icon="lucide:bar-chart-2" class="text-[#1AAD94]"></iconify-icon> {{ $program->level }}</p>
                                @endif
                                @if ($program->starts_at)
                                    <p class="flex items-center gap-1.5"><iconify-icon icon="lucide:calendar" class="text-[#1AAD94]"></iconify-icon> Starts {{ $program->starts_at->format('M d, Y') }}</p>
                                @endif
                            </div>

                            @if ($program->short_description)
                                <p class="text-[#4B5563] text-sm leading-relaxed mb-5 line-clamp-3">{{ $program->short_description }}</p>
                            @endif

                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                                <span class="text-xl font-extrabold text-[#073057]">
                                    @if ($program->isFree())
                                        <span class="text-[#1AAD94]">Free</span>
                                    @else
                                        {{ money($program->price, $program->currency ?? 'USD') }}
                                    @endif
                                </span>
                                <a href="{{ route('training.show', $program->slug) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#073057] hover:bg-[#1AAD94] text-white text-xs font-bold uppercase tracking-wider rounded-lg transition">
                                    View
                                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($programs instanceof \Illuminate\Pagination\AbstractPaginator && $programs->hasPages())
                <div class="mt-10">{{ $programs->links() }}</div>
            @endif
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="relative py-16 md:py-20 bg-[#073057] overflow-hidden">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#1AAD94]/20 rounded-full blur-3xl"></div>
    <div class="container mx-auto px-6 relative">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Need a custom programme for your team?</h2>
            <p class="text-white/75 text-lg mb-8 max-w-xl mx-auto">We design closed-cohort training and apprenticeship intakes for employers, port authorities, and partner institutions.</p>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:brightness-110 text-white font-bold uppercase tracking-widest text-sm rounded-xl transition shadow-lg">
                Talk to our training desk
                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
        </div>
    </div>
</section>
@endsection
