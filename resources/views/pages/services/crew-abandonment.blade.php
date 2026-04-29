@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['offshore_vessel'] ?? '' }}"
         alt="Crew Abandonment Support"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Crew Abandonment Support</h1>
        <p class="mt-4 max-w-xl text-lg text-white/70">Protecting seafarers' rights and ensuring safe repatriation when vessels fail their crew.</p>
    </div>
</section>

{{-- Main Content --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:anchor"></iconify-icon>
                    Crew Abandonment Solutions
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">When seafarers are stranded, JCL acts.</h2>
                <p class="text-[#4B5563] leading-relaxed mb-8">When seafarers are left stranded without wages, provisions, or a means to return home, swift action is critical. Jose Ocean Jobs works to protect abandoned crew — liaising with authorities, providing welfare support, and coordinating repatriation to get seafarers home safely.</p>
                <ul class="space-y-3 mb-10">
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Liaison with flag state and port state control authorities</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Engagement with ITF inspectors and seafarer welfare organisations</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Emergency provisions and welfare support for stranded crew</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Repatriation logistics and travel documentation</span></li>
                        <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Formal complaint filing and wage recovery guidance</span></li>
                </ul>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Contact Us Now
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $img['offshore_vessel'] ?? '' }}"
                     alt="Crew Abandonment Support"
                     class="w-full h-[480px] object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- MLC Info --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                <iconify-icon icon="lucide:shield-check"></iconify-icon>
                MLC 2006
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] mb-6">Understanding the MLC 2006 Standard</h2>
            <p class="text-[#4B5563] leading-relaxed">The Maritime Labour Convention (MLC 2006) sets out shipowners' obligations to crew — including living conditions, payment, and repatriation. JCL helps both seafarers and vessel operators understand and uphold these standards, reducing the risk of abandonment and protecting everyone involved.</p>
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Need immediate crew welfare assistance?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">If you are dealing with an abandonment situation, contact us immediately — we respond 24/7.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact JCL
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
