@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['ship_chandelling'] ?? '' }}"
         alt="Ship Chandelling"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Ship Chandelling</h1>
        <p class="mt-4 max-w-xl text-lg text-white/70">Quality vessel provisioning — delivered reliably to port.</p>
    </div>
</section>

{{-- Main Content --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:anchor"></iconify-icon>
                    Ship Chandelling
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Everything a vessel needs — provisions, spares, consumables.</h2>
                <p class="text-[#4B5563] leading-relaxed mb-8">We supply vessels with everything they need during a port call — from provisions and bonded stores to deck and engine consumables, sourced reliably and delivered on schedule to any Nigerian port.</p>
                <ul class="space-y-3 mb-10">
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Provisions and victuals — fresh, frozen, and dry goods</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Bonded stores (duty-free spirits, tobacco, and beverages)</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Deck and engine stores</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Safety and medical supplies</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Competitive pricing with transparent, itemised quotations</span></li>
                </ul>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Request a Quotation
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $img['ship_chandelling'] ?? '' }}"
                     alt="Ship Chandelling"
                     class="w-full h-[480px] object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Need provisions delivered to port?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Send us your vessel's requirements and port of call for a same-day quotation.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact JCL
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
