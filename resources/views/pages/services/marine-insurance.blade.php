@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['aerial_container'] ?? '' }}"
         alt="Marine Insurance"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Marine Insurance</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">Protecting what matters most — at sea and on shore.</p>
    </div>
</section>

{{-- Main Content --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:anchor"></iconify-icon>
                    Marine Insurance
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Protecting what matters most — at sea and on shore.</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">The Maritime and Energy sector carries real risk — and having the right insurance in place is not optional, it is essential. Through our licensed marine insurance partner, Jose Ocean Jobs connects vessel owners, operators, cargo owners, and maritime businesses with tailored insurance solutions that provide genuine protection and peace of mind.</p>
                <p class="text-[#4B5563] leading-relaxed mb-8">Our partner works across a range of marine insurance products, ensuring that whatever your exposure, there is a policy designed to cover it.</p>
                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.15em] text-[#073057]">What is covered</div>
                <ul class="space-y-3 mb-10">
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Hull and machinery insurance</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Protection and indemnity (P&amp;I) cover</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Cargo and freight insurance</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Offshore and energy risk cover</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Liability insurance for maritime businesses</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Small vessel and pleasure craft cover</span></li>
                </ul>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Discuss Your Cover Needs
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $img['aerial_container'] ?? '' }}"
                     alt="Marine Insurance"
                     class="w-full h-[480px] object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- Cover Types --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-8 text-center shadow-sm">
                <div class="h-14 w-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                    <iconify-icon icon="lucide:shield" class="text-2xl"></iconify-icon>
                </div>
                <h4 class="text-[#073057] font-extrabold text-lg mb-2">H&amp;M Cover</h4>
                <p class="text-[#6B7280] text-sm leading-relaxed">Physical loss or damage to vessel hull and machinery from insured perils.</p>
            </div>
            <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-8 text-center shadow-sm">
                <div class="h-14 w-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                    <iconify-icon icon="lucide:users" class="text-2xl"></iconify-icon>
                </div>
                <h4 class="text-[#073057] font-extrabold text-lg mb-2">P&amp;I Protection</h4>
                <p class="text-[#6B7280] text-sm leading-relaxed">Third-party liability cover including pollution, collision, and crew liabilities.</p>
            </div>
            <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-8 text-center shadow-sm">
                <div class="h-14 w-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                    <iconify-icon icon="lucide:package" class="text-2xl"></iconify-icon>
                </div>
                <h4 class="text-[#073057] font-extrabold text-lg mb-2">Cargo Insurance</h4>
                <p class="text-[#6B7280] text-sm leading-relaxed">Coverage for goods in transit — including total loss, general average, and war risk.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Protect your maritime assets today</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Speak with us about your insurance needs and we will connect you with Our Team.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact Jose Ocean Jobs
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
