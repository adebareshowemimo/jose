@extends('layouts.app')

@section('title', ($pageTitle ?? 'Leadership & Experts').' — Jose Consulting Limited')

@section('content')
@php
    $leaders = $leaders ?? [];
    $img = $jclImages ?? [];
@endphp

{{-- HERO --}}
<section class="relative h-[480px] flex items-center overflow-hidden bg-[#073057]">
    <div class="absolute inset-0 z-0">
        <img src="{{ $img['business_meeting'] ?? '' }}"
             alt="Professional team of consultants and industry experts"
             class="w-full h-full object-cover opacity-60" loading="eager" />
        <div class="absolute inset-0 hero-overlay"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10 text-white">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="text-white/70 mb-8" />
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6">{{ $pageTitle ?? 'Leadership & Experts' }}</h1>
        <p class="text-xl md:text-2xl text-white/70 max-w-2xl leading-relaxed">{{ $pageDescription ?? 'The strategic minds and operational veterans driving JCL\'s vision to architect global maritime talent pathways.' }}</p>
    </div>
</section>

{{-- LEADER CARDS --}}
<section class="bg-[#F9FAFB] py-24 md:py-32">
    <div class="container mx-auto px-6">
        <div class="grid gap-8 md:grid-cols-2">
            @foreach($leaders as $leader)
                <article class="rounded-[24px] bg-white p-10 border border-[#E0E0E0] card-hover-lift">
                    <div class="flex flex-col md:flex-row md:items-start gap-8 mb-8">
                        <div class="w-20 h-20 shrink-0 rounded-full bg-[#073057] flex items-center justify-center text-white font-bold text-2xl shadow-xl">
                            {{ collect(explode(' ', $leader['name']))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                        </div>
                        <div class="flex-1">
                            <div class="inline-flex px-3 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-[10px] font-bold uppercase tracking-wider rounded-full mb-3">{{ $leader['category'] }}</div>
                            <h3 class="text-3xl font-extrabold text-[#073057] mb-1">{{ $leader['name'] }}</h3>
                            <p class="text-[#1AAD94] text-[12px] font-bold uppercase tracking-widest">{{ $leader['title'] }}</p>
                        </div>
                    </div>
                    <p class="text-[#6B7280] leading-relaxed mb-10">{{ $leader['summary'] }}</p>
                    <div class="flex flex-wrap gap-2 pt-8 border-t border-[#E0E0E0]">
                        @foreach($leader['highlights'] as $highlight)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[11px] font-semibold">
                                <iconify-icon icon="lucide:badge-check" class="text-[#1AAD94]"></iconify-icon>
                                <span>{{ $highlight }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- DELIVERY + EXPLORE --}}
<section class="bg-white py-24 md:py-32">
    <div class="container mx-auto px-6">
        <div class="grid gap-8 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="bg-white rounded-[32px] border border-[#E0E0E0] overflow-hidden p-8 md:p-12 shadow-sm">
                <div class="inline-flex px-4 py-1.5 bg-[#073057] text-white text-[11px] font-bold uppercase tracking-widest rounded-full mb-8">What this means for delivery</div>
                <h2 class="text-[40px] md:text-[52px] font-extrabold text-[#073057] leading-[1.1] mb-8">A combined force of industry-shaping capability.</h2>
                <p class="text-lg text-[#6B7280] leading-relaxed mb-12 max-w-2xl">The result is a delivery model that can support both individual employability pathways and organization-level workforce transformation conversations with practical, globally informed insight.</p>
                <img src="{{ $img['sailor_repairs'] ?? '' }}"
                     alt="Hands-on technical expertise in maritime operations"
                     class="w-full h-[400px] object-cover rounded-[24px]" loading="lazy" />
            </div>

            <div class="bg-[#073057] rounded-[32px] p-8 md:p-12 text-white flex flex-col justify-center">
                <div class="inline-flex px-4 py-1.5 border border-white/20 text-[#1AAD94] text-[11px] font-bold uppercase tracking-widest rounded-full mb-10 w-fit">Explore further</div>
                <h3 class="text-3xl font-bold mb-12">Connect with our specialized team today.</h3>
                <div class="space-y-4">
                    <a href="{{ route('about.index') }}" class="flex items-center justify-between w-full px-8 py-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group font-bold">
                        <span class="text-[15px]">About JCL</span>
                        <iconify-icon icon="lucide:arrow-right" class="group-hover:translate-x-2 transition-transform"></iconify-icon>
                    </a>
                    <a href="{{ route('partnerships.index') }}" class="flex items-center justify-between w-full px-8 py-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group font-bold">
                        <span class="text-[15px]">Partnerships & Expertise</span>
                        <iconify-icon icon="lucide:arrow-right" class="group-hover:translate-x-2 transition-transform"></iconify-icon>
                    </a>
                    <a href="{{ route('contact.index') }}" class="flex items-center justify-between w-full px-8 py-5 bg-[#1AAD94] rounded-2xl hover:brightness-110 transition-all group font-bold">
                        <span class="text-[15px]">Contact an Expert</span>
                        <iconify-icon icon="lucide:message-square" class="group-hover:scale-110 transition-transform"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
