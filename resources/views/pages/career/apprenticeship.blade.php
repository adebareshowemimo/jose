@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['deck_officer'] ?? '' }}"
         alt="Apprenticeship Programme"
         class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Apprenticeship<br><span class="text-[#1AAD94]">Programme</span></h1>
        <p class="mt-4 max-w-xl text-lg text-white/70">Build a career in maritime or energy through structured, work-based learning.</p>
    </div>
</section>

{{-- Intro --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:graduation-cap"></iconify-icon>
                    Apprenticeship
                </div>
                <h2 class="text-4xl font-extrabold text-[#073057] leading-tight mb-6">Learn by doing.<br>Earn while you grow.</h2>
                <p class="text-[#4B5563] leading-relaxed mb-4">The JCL Apprenticeship Programme offers school leavers and early-career candidates a structured pathway into the maritime and energy industry. Over 18–24 months, apprentices combine practical workplace experience with certified training — emerging ready for full employment.</p>
                <p class="text-[#4B5563] leading-relaxed">Our apprentices work directly with vessel operators, port logistics teams, offshore facilities, and energy companies within JCL's employer network — gaining exposure that no classroom alone can provide.</p>
                <div class="mt-8">
                    <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                        Apply Now
                        <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                    </a>
                </div>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl">
                <img src="{{ $img['safety_officer'] ?? '' }}"
                     alt="Apprentice at work"
                     class="w-full h-[420px] object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- Key Details --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-8 text-center shadow-sm">
                <div class="h-14 w-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                    <iconify-icon icon="lucide:calendar" class="text-2xl"></iconify-icon>
                </div>
                <h4 class="text-[#073057] font-extrabold text-lg mb-2">Programme Duration</h4>
                <p class="text-[#6B7280] text-sm leading-relaxed">18–24 months of structured work-based learning and certified training modules.</p>
            </div>
            <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-8 text-center shadow-sm">
                <div class="h-14 w-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                    <iconify-icon icon="lucide:user-check" class="text-2xl"></iconify-icon>
                </div>
                <h4 class="text-[#073057] font-extrabold text-lg mb-2">Eligibility</h4>
                <p class="text-[#6B7280] text-sm leading-relaxed">Open to school leavers (minimum WAEC / SSCE), OND holders, and early-career individuals up to age 28.</p>
            </div>
            <div class="bg-white rounded-[24px] border border-[#E0E0E0] p-8 text-center shadow-sm">
                <div class="h-14 w-14 rounded-full bg-[#1AAD94]/10 flex items-center justify-center mx-auto mb-4 text-[#1AAD94]">
                    <iconify-icon icon="lucide:award" class="text-2xl"></iconify-icon>
                </div>
                <h4 class="text-[#073057] font-extrabold text-lg mb-2">On Completion</h4>
                <p class="text-[#6B7280] text-sm leading-relaxed">Receive an industry-recognised certificate and a priority referral into JCL's employer network.</p>
            </div>
        </div>
    </div>
</section>

{{-- How to Apply --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-extrabold text-[#073057]">How to Apply</h2>
                <p class="mt-3 text-[#6B7280]">Four simple steps to start your maritime career journey.</p>
            </div>
            <ol class="space-y-6">
                @foreach([
                    ['title' => 'Submit your application', 'desc' => 'Send your CV and a brief cover letter via the contact form below.'],
                    ['title' => 'Initial screening', 'desc' => 'JCL reviews your background and matches you to an appropriate placement track.'],
                    ['title' => 'Assessment & interview', 'desc' => 'A short aptitude or competency interview with our career team.'],
                    ['title' => 'Programme enrolment', 'desc' => 'Confirmation of placement, start date, and programme handbook.'],
                ] as $i => $step)
                <li class="flex gap-5 items-start">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#073057] text-white font-bold text-sm">{{ $i + 1 }}</div>
                    <div class="pt-1.5">
                        <h5 class="font-bold text-[#073057] mb-1">{{ $step['title'] }}</h5>
                        <p class="text-sm text-[#6B7280]">{{ $step['desc'] }}</p>
                    </div>
                </li>
                @endforeach
            </ol>
            <div class="mt-12 text-center">
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#073057] hover:brightness-110 text-white font-bold rounded-xl transition-all shadow-lg uppercase tracking-wider text-sm">
                    Apply Now
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Start your maritime career with JCL</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Take the first step toward a globally competitive career in the maritime and energy sectors.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Get in Touch
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
