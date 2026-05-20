@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    @if (!empty($img['mobilization_hero']))
        <img src="{{ $img['mobilization_hero'] }}" alt="Mobilisation Services" class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Mobilisation Services</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">Getting your people where they need to be — ready, compliant, and on time.</p>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:plane"></iconify-icon>
                    Mobilisation Services
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Getting your people where they need to be — ready, compliant, and on time.</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">In the Maritime and Energy sector, time is critical. Delays in mobilising crew or personnel cost money and disrupt operations. Through our licensed mobilisation partner, Jose Ocean Jobs manages the full end-to-end process of moving personnel from where they are to where they need to be — safely, efficiently, and with every requirement covered before they step foot on site or on board.</p>
                <p class="text-[#073057] font-bold leading-relaxed mb-8">Nothing is left to chance. Nothing is left behind.</p>
                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.15em] text-[#073057]">What is covered</div>
                <ul class="space-y-3 mb-10">
                    <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Visas and work permits for personnel travelling to or working across different countries</span></li>
                    <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Travel arrangements including flights, transfers, and ground transportation</span></li>
                    <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Accommodation at mobilisation points, offshore bases, or assignment locations</span></li>
                    <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Onboarding coordination including documentation checks, inductions, and certification verification</span></li>
                    <li class="flex items-start gap-2 text-[#4B5563]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0"></iconify-icon><span>Medical assessments including offshore medicals, fitness-to-work certificates, and destination-specific health clearances</span></li>
                </ul>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Mobilize your team
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[480px] flex items-center justify-center text-white">
                <div class="text-center p-12">
                    <iconify-icon icon="lucide:plane" class="text-7xl mb-4 opacity-80"></iconify-icon>
                    <p class="text-lg font-bold uppercase tracking-[0.15em] opacity-80">On-site. On-time. Ready.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Need mobilization support for an upcoming project?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Tell us your destination, headcount, and start date — we will quote and execute.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact Jose Ocean Jobs
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>
@endsection
