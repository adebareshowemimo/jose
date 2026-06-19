@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['crew_management'] ?? '' }}"
         alt="Crew Management"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[32px] sm:text-[48px] md:text-[64px] font-extrabold text-white leading-none">Crew Management</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">The right crew, managed the right way.</p>
    </div>
</section>

{{-- Main Content --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:anchor"></iconify-icon>
                    Crew Management
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">The right crew, managed the right way.</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">Managing a crew is one of the most complex and critical responsibilities in the Maritime and Energy sector. Through our licensed crew management partner, Jose Ocean Jobs offers comprehensive crew management solutions that take the pressure off ship owners and operators — handling everything from recruitment and contracts to payroll and welfare.</p>
                <p class="text-[#4B5563] leading-relaxed mb-8">Our partner operates in full compliance with MLC (Maritime Labour Convention) standards, ensuring that every crew member is treated fairly, paid correctly, and supported throughout their time on board.</p>
                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.15em] text-[#073057]">What is covered</div>
                <ul class="space-y-3 mb-10">
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Crew recruitment and selection</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Contract management and documentation</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Payroll and allotment processing</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Visa, travel, and joining arrangements</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Crew welfare and repatriation</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Flag state compliance and certification checks</span></li>
                </ul>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Get in Touch
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $img['crew_management'] ?? '' }}"
                     alt="Crew Management"
                     class="w-full h-[480px] object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Let's manage your crew together</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Get in touch to discuss your crewing requirements — we will respond promptly.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact Jose Ocean Jobs
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
