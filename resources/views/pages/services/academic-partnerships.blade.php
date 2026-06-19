@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    @if (!empty($img['academic_partnerships_hero']))
        <img src="{{ $img['academic_partnerships_hero'] }}" alt="Academic Partnerships" class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[32px] sm:text-[48px] md:text-[64px] font-extrabold text-white leading-none">Academic Partnerships</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">Where Education Meets Industry — Building Knowledge, Creating Opportunities.</p>
    </div>
</section>

{{-- What Is This Service? --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:graduation-cap"></iconify-icon>
                    Academic Partnerships
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">What Is This Service?</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">The Maritime and Energy sector is constantly evolving — and the institutions that educate the next generation of professionals must evolve with it. At Jose Ocean Jobs, we work directly with schools, universities, colleges, and vocational institutions to build meaningful academic partnerships that bring the industry into the classroom and take students closer to the world they are preparing to enter.</p>
                <p class="text-[#4B5563] leading-relaxed mb-8">This is not just about placements and pipelines. It is about creating a genuine relationship between academia and industry — one that enriches learning, exposes students to real-world maritime and energy careers, and positions institutions as active contributors to the growth of the sector.</p>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Start a Partnership Conversation
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="relative rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[480px]">
                @if (!empty($img['academic_partnerships_hero']))
                    <img src="{{ $img['academic_partnerships_hero'] }}" alt="Academic Partnerships" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
                @endif
            </div>
        </div>
    </div>
</section>

{{-- What We Offer --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:layers"></iconify-icon>
                What We Offer
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">Bringing the industry into the classroom.</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['lucide:mic', 'Industry Seminars & Guest Lectures', 'We organise and facilitate seminars, guest lectures, and panel discussions delivered by experienced Maritime and Energy sector professionals. Students get direct access to real insights, career journeys, and industry knowledge that goes far beyond what any textbook can offer.'],
                ['lucide:book-open', 'Curriculum Collaboration', 'We partner with academic institutions to ensure that course content stays relevant to the current demands of the Maritime and Energy sector. Working alongside educators, we help integrate industry standards, emerging trends, and practical knowledge into existing programmes.'],
                ['lucide:compass', 'Career Awareness & Exposure Events', 'We bring the industry to students through career days, industry tours, networking events, and interactive sessions — helping young people understand the full range of opportunities available to them in the Maritime and Energy sector before they even graduate.'],
                ['lucide:flask-conical', 'Research Partnerships', 'We connect academic research departments with real industry challenges — supporting studies, projects, and dissertations that are grounded in the actual needs of the Maritime and Energy sector and giving institutions the credibility of genuine industry collaboration.'],
                ['lucide:award', 'Scholarship & Sponsorship Facilitation', 'We work with institutions to identify and facilitate scholarship and sponsorship opportunities for students — connecting academic programmes with organisations in the sector that want to invest in the next generation of talent.'],
            ] as $offer)
                <div class="rounded-[24px] bg-white border border-[#E5E7EB] p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="h-14 w-14 rounded-2xl bg-[#1AAD94]/10 text-[#1AAD94] flex items-center justify-center mb-5">
                        <iconify-icon icon="{{ $offer[0] }}" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="text-[18px] font-extrabold text-[#073057] mb-3">{{ $offer[1] }}</h3>
                    <p class="text-sm text-[#6B7280] leading-relaxed">{{ $offer[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Who Is This For + How It Works --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:user-check"></iconify-icon>
                    Who Is This For?
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">This service is designed for:</h2>
                <ul class="space-y-3">
                    @foreach ([
                        'Universities and colleges with maritime, marine, or energy-related programmes',
                        'Vocational and technical training institutions',
                        'Secondary schools looking to introduce students to Maritime and Energy sector careers',
                        'Academic staff and department heads seeking industry collaboration',
                        'Research departments looking for real-world industry engagement',
                    ] as $audience)
                        <li class="flex items-start gap-3 text-[#4B5563]">
                            <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0 text-xl"></iconify-icon>
                            <span>{{ $audience }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:list-checks"></iconify-icon>
                    How It Works
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">A partnership built to last.</h2>
                <div class="space-y-5">
                    @foreach ([
                        ['01', 'Initial Conversation', 'We sit down with your institution to understand your programmes, your students, and what kind of partnership would add the most value.'],
                        ['02', 'Partnership Design', 'We build a collaboration plan tailored to your institution — whether that is a seminar series, a curriculum review, a research tie-up, or a sponsorship programme.'],
                        ['03', 'Activation', 'We bring the right industry professionals, organisations, and resources into the partnership and get things moving.'],
                        ['04', 'Ongoing Engagement', 'We stay involved, keep the relationship active, and continue to develop the partnership as both the institution and the industry grow.'],
                    ] as $step)
                        <div class="flex gap-4">
                            <div class="shrink-0 h-10 w-10 rounded-full bg-[#1AAD94] text-white font-extrabold flex items-center justify-center text-sm">{{ $step[0] }}</div>
                            <div>
                                <h3 class="text-[16px] font-extrabold text-[#073057] mb-1">{{ $step[1] }}</h3>
                                <p class="text-sm text-[#6B7280] leading-relaxed">{{ $step[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why Academic Partnerships Matter --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl rounded-[32px] bg-white border border-[#E5E7EB] shadow-sm p-10 md:p-14">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                <iconify-icon icon="lucide:lightbulb"></iconify-icon>
                Why It Matters
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Why Academic Partnerships Matter in the Maritime and Energy Sector</h2>
            <p class="text-[#4B5563] leading-relaxed mb-5">The Maritime and Energy sector faces a significant skills gap. As experienced professionals retire and the sector expands into new areas like offshore renewables, energy technology, and sustainable fisheries, the demand for qualified talent is growing faster than ever.</p>
            <p class="text-[#4B5563] leading-relaxed">Academic partnerships are not just good for students and institutions — they are essential for the future of the industry. By working together, we ensure that the right knowledge, skills, and passion are being developed in the right people at the right time.</p>
        </div>
    </div>
</section>

{{-- Why Jose Ocean Jobs --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:star"></iconify-icon>
                    Why Jose Ocean Jobs?
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">A partner you can build with.</h2>
                <ul class="space-y-3">
                    @foreach ([
                        'Deep roots and an extensive network across the Maritime and Energy sector',
                        'Trusted by both employers and educational institutions',
                        'A genuine commitment to building long-term, meaningful partnerships',
                        'Experience connecting talent at every stage — from students to senior professionals',
                        'A dedicated team that manages and nurtures every partnership we facilitate',
                    ] as $reason)
                        <li class="flex items-start gap-3 text-[#4B5563]">
                            <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0 text-xl"></iconify-icon>
                            <span>{{ $reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[400px] flex items-center justify-center text-white">
                <div class="text-center p-12">
                    <iconify-icon icon="lucide:school" class="text-7xl mb-4 opacity-80"></iconify-icon>
                    <p class="text-lg font-bold uppercase tracking-[0.15em] opacity-80">Knowledge meets opportunity</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Let's Build Something That Lasts</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Education and industry are stronger together. Let Jose Ocean Jobs bring them closer.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Start a Partnership Conversation
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>
@endsection
