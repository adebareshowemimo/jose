@extends('layouts.app')

@section('title', ($pageTitle ?? 'Training & Events').' — Jose Consulting Limited')

@section('content')
@php
    $profile = $profile ?? [];
    $programs = $programs ?? [];
    $events = $events ?? [];
    $industryEvents = $industry_events ?? [];
    $img = $jclImages ?? [];
@endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['offshore_vessel'] ?? '' }}"
         alt="Maritime training and professional development"
         class="absolute inset-0 w-full h-full object-cover opacity-50" loading="eager" />
    <div class="absolute inset-0 hero-overlay"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-tight max-w-3xl">Trainings</h1>
        <p class="mt-4 max-w-xl text-lg text-white/70">Building workforce competence through internationally recognized programmes.</p>
    </div>
</section>

{{-- Training Programmes --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-16">
            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Professional Development</div>
            <h2 class="text-[48px] font-extrabold text-[#073057] leading-tight">Training Programmes</h2>
            <p class="mt-4 text-lg text-[#6B7280]">Industry-aligned training delivered by certified professionals, designed to meet international maritime and energy sector standards.</p>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($programs as $program)
                <a href="{{ route('services.training') }}" class="group rounded-[32px] bg-white border border-[#E0E0E0] p-8 shadow-sm transition-all hover:shadow-2xl hover:-translate-y-2 hover:border-[#1AAD94] flex flex-col">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-[#073057]/5 flex items-center justify-center mb-6">
                        <iconify-icon icon="{{ $program['icon'] }}" width="28" class="text-[#1AAD94]"></iconify-icon>
                    </div>

                    {{-- Category badge --}}
                    <div class="inline-flex self-start rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-[#1AAD94] mb-4">{{ $program['category'] }}</div>

                    <h3 class="text-xl font-bold text-[#073057] mb-3 leading-snug">{{ $program['title'] }}</h3>
                    <p class="text-[#6B7280] text-sm leading-relaxed mb-6 flex-1">{{ $program['description'] }}</p>

                    {{-- Meta --}}
                    <div class="flex items-center gap-4 text-xs text-[#6B7280] border-t border-[#E0E0E0] pt-4">
                        <span class="flex items-center gap-1.5">
                            <iconify-icon icon="lucide:clock" width="14"></iconify-icon>
                            {{ $program['duration'] }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <iconify-icon icon="lucide:monitor" width="14"></iconify-icon>
                            {{ $program['mode'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Upcoming Events & Seminars --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-16">
            <div class="inline-flex rounded-full bg-[#073057]/5 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#073057] mb-6">Calendar</div>
            <h2 class="text-[48px] font-extrabold text-[#073057] leading-tight">Upcoming Events &amp; Seminars</h2>
            <p class="mt-4 text-lg text-[#6B7280]">JCL-organized and co-hosted events designed for maritime and energy professionals — from hands-on workshops to strategic conferences.</p>
        </div>

        <div class="space-y-6">
            @foreach($events as $event)
                <article class="group rounded-[32px] border border-[#E0E0E0] bg-[#F9FAFB] p-8 md:p-10 transition-all hover:shadow-xl hover:border-[#1AAD94]/30">
                    <div class="flex flex-col md:flex-row md:items-start gap-6">
                        {{-- Date block --}}
                        <div class="flex-shrink-0 w-28 text-center">
                            <div class="rounded-2xl bg-[#073057] text-white p-4">
                                <div class="text-[11px] font-bold uppercase tracking-widest text-[#7DE1D1] mb-1">{{ \Illuminate\Support\Str::before($event['date'], ',') }}</div>
                                <div class="text-[11px] font-medium text-white/60">{{ \Illuminate\Support\Str::after($event['date'], ', ') ?: $event['date'] }}</div>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <span class="inline-flex rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-[#1AAD94]">{{ $event['type'] }}</span>
                                <span class="text-xs text-[#6B7280] flex items-center gap-1.5">
                                    <iconify-icon icon="lucide:map-pin" width="14"></iconify-icon>
                                    {{ $event['location'] }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-[#073057] mb-3">{{ $event['title'] }}</h3>
                            <p class="text-[#6B7280] leading-relaxed">{{ $event['description'] }}</p>
                        </div>

                        {{-- Status --}}
                        <div class="flex-shrink-0 self-start">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#1AAD94]/10 px-4 py-2 text-xs font-bold text-[#1AAD94]">
                                <span class="w-2 h-2 rounded-full bg-[#1AAD94] animate-pulse"></span>
                                Upcoming
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Industry Events --}}
<section class="bg-[#073057] py-24 relative overflow-hidden">
    <div class="absolute inset-0 dot-pattern opacity-20"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mb-16">
            <div class="inline-flex rounded-full bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-6">Global Calendar</div>
            <h2 class="text-[48px] font-extrabold text-white leading-tight">Industry Conferences</h2>
            <p class="mt-4 text-lg text-white/60">Key maritime and energy industry events where JCL maintains an active presence — connecting talent, clients, and knowledge networks.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach($industryEvents as $conf)
                <article class="rounded-[24px] bg-white/5 border border-white/10 backdrop-blur-sm p-8 transition-all hover:bg-white/10 hover:border-[#7DE1D1]/30 flex flex-col">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-[#7DE1D1] mb-4">{{ $conf['date'] }}</div>
                    <h3 class="text-xl font-bold text-white mb-2">{{ $conf['title'] }}</h3>
                    <p class="text-white/50 text-sm flex items-center gap-1.5 mb-4">
                        <iconify-icon icon="lucide:map-pin" width="14"></iconify-icon>
                        {{ $conf['location'] }}
                    </p>
                    <p class="text-white/60 text-sm leading-relaxed flex-1">{{ $conf['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Why Train with JCL --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="grid gap-12 lg:grid-cols-2 items-center">
            <div>
                <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Why JCL</div>
                <h2 class="text-[40px] font-extrabold text-[#073057] leading-tight mb-6">Training built around real-world workforce demands.</h2>
                <p class="text-lg text-[#6B7280] leading-relaxed mb-8">JCL's training model bridges the gap between certification and competence — ensuring professionals are not just qualified, but operationally ready for the industries they serve.</p>

                <div class="space-y-5">
                    @php
                        $reasons = [
                            ['icon' => 'lucide:award', 'title' => 'Internationally Recognized Certifications', 'desc' => 'STCW, NEBOSH, BOSIET and other globally accepted standards.'],
                            ['icon' => 'lucide:users', 'title' => 'Industry-Experienced Trainers', 'desc' => 'Delivered by professionals with decades of maritime and energy sector experience.'],
                            ['icon' => 'lucide:globe', 'title' => 'Hybrid Delivery Model', 'desc' => 'Flexible in-person, online, and blended formats to suit operational schedules.'],
                            ['icon' => 'lucide:briefcase', 'title' => 'Employer-Connected Pathways', 'desc' => 'Training linked to actual recruitment pipelines and workforce planning.'],
                        ];
                    @endphp
                    @foreach($reasons as $reason)
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-[#073057]/5 flex items-center justify-center">
                                <iconify-icon icon="{{ $reason['icon'] }}" width="20" class="text-[#1AAD94]"></iconify-icon>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#073057] mb-1">{{ $reason['title'] }}</h4>
                                <p class="text-sm text-[#6B7280]">{{ $reason['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $img['maritime_training'] ?? '' }}"
                     alt="Maritime professionals in training environment"
                     class="w-full h-[480px] object-cover" loading="lazy" />
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
@if(!empty($profile['final_cta']))
    @php $cta = $profile['final_cta']; @endphp
    <section class="bg-[#073057] py-24 relative overflow-hidden">
        <div class="absolute left-1/2 top-0 w-[600px] h-[600px] rounded-full bg-[#1AAD94]/5 -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-[40px] md:text-[48px] font-extrabold text-white leading-tight max-w-3xl mx-auto mb-6">Interested in our training programmes or upcoming events?</h2>
            <p class="text-lg text-white/60 max-w-2xl mx-auto mb-10">Whether you are looking to upskill your workforce, attend a conference, or partner on training initiatives — let us start the conversation.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94] px-8 py-4 text-sm font-bold text-white shadow-lg hover:bg-[#159b84] transition">
                    <iconify-icon icon="lucide:mail" width="18"></iconify-icon>
                    Contact JCL
                </a>
                <a href="{{ route('job.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 px-8 py-4 text-sm font-bold text-white hover:bg-white/20 transition">
                    <iconify-icon icon="lucide:briefcase" width="18"></iconify-icon>
                    Browse Jobs
                </a>
            </div>
        </div>
    </section>
@endif
@endsection
