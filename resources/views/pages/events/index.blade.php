@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['aerial_container'] ?? '' }}"
         alt="Events"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Events</h1>
        <p class="mt-4 max-w-xl text-lg text-white/70">JCL-hosted events, industry conferences, and maritime sector gatherings.</p>
    </div>
</section>

{{-- JCL Hosted Events --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:calendar-days"></iconify-icon>
                JCL Events
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057]">Events hosted &amp; organised by JCL</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($events as $event)
                <div class="flex flex-col bg-white rounded-[24px] border border-[#E0E0E0] shadow-sm overflow-hidden">
                    <div class="p-7 flex-1">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-[#1AAD94]/10 text-[#1AAD94]">{{ $event['type'] }}</span>
                            @if (($event['status'] ?? '') === 'upcoming')
                                <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-green-100 text-green-700">Upcoming</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-extrabold text-[#073057] mb-3">{{ $event['title'] }}</h3>
                        <p class="text-sm text-[#6B7280] mb-1 flex items-center gap-1.5">
                            <iconify-icon icon="lucide:calendar" class="shrink-0"></iconify-icon>
                            {{ $event['date'] }}
                        </p>
                        <p class="text-sm text-[#6B7280] mb-4 flex items-center gap-1.5">
                            <iconify-icon icon="lucide:map-pin" class="shrink-0"></iconify-icon>
                            {{ $event['location'] }}
                        </p>
                        <p class="text-[#4B5563] text-sm leading-relaxed">{{ $event['description'] }}</p>
                    </div>
                    <div class="px-7 pb-7">
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-bold rounded-xl transition-all">
                            Register Interest
                            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Industry Events --}}
<section class="py-24 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:globe"></iconify-icon>
                Industry Calendar
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057]">Key global maritime events</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($industry_events as $ie)
                <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-7 shadow-sm text-center">
                    <div class="h-12 w-12 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                        <iconify-icon icon="lucide:globe-2" class="text-xl"></iconify-icon>
                    </div>
                    <h4 class="text-[#073057] font-extrabold text-base mb-3">{{ $ie['title'] }}</h4>
                    <p class="text-sm text-[#6B7280] mb-1 flex items-center justify-center gap-1.5">
                        <iconify-icon icon="lucide:calendar" class="shrink-0"></iconify-icon>
                        {{ $ie['date'] }}
                    </p>
                    <p class="text-sm text-[#6B7280] mb-3 flex items-center justify-center gap-1.5">
                        <iconify-icon icon="lucide:map-pin" class="shrink-0"></iconify-icon>
                        {{ $ie['location'] }}
                    </p>
                    <p class="text-[#4B5563] text-sm leading-relaxed">{{ $ie['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Want to host an event with JCL?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">We partner with industry bodies, employers, and training institutions to run impactful maritime and energy events.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Get in Touch
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
