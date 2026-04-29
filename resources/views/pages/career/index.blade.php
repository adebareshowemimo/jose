@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')

@php($img = $jclImages ?? [])

{{-- HERO --}}
<section class="relative overflow-hidden bg-[#073057] py-24 lg:py-32">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(26,173,148,0.18),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(26,173,148,0.12),transparent_30%)]"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-8 backdrop-blur-sm">
                <iconify-icon icon="lucide:compass" class="text-sm"></iconify-icon>
                <span>Career Pathways</span>
            </div>
            <h1 class="text-[48px] md:text-[64px] font-extrabold leading-[1.05] tracking-tight text-white mb-6">
                Leaping Forward Into <span class="text-[#1AAD94]">Opportunities</span>
            </h1>
            <p class="text-xl text-white/70 leading-relaxed max-w-2xl mb-10">
                Structured entry points designed for the next generation of maritime and energy professionals. Choose the pathway that fits your goals.
            </p>
            <nav aria-label="breadcrumb">
                <ol class="flex items-center gap-2 text-[13px]">
                    @foreach ($breadcrumbs as $crumb)
                        @if ($loop->last)
                            <li class="text-[#1AAD94] font-semibold">{{ $crumb['label'] }}</li>
                        @else
                            <li>
                                <a href="{{ $crumb['url'] }}" class="text-white/60 hover:text-white transition-colors">{{ $crumb['label'] }}</a>
                                <span class="ml-2 text-white/30">/</span>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>
</section>

{{-- BANNER --}}
<section class="relative">
    <img src="{{ $img['career_banner'] ?? '' }}"
         alt="Professional in command of their career journey"
         class="w-full h-[280px] md:h-[420px] object-cover" loading="lazy" />
</section>

{{-- PROGRAMMES --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">

        <div class="text-center mb-16">
            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Where Your Journey Starts</div>
            <h2 class="text-[42px] font-extrabold text-[#073057] leading-tight">Choose your career entry pathway</h2>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-[#6B7280]">Both programmes are designed to give you hands-on experience, industry mentorship, and a clear route to employment.</p>
        </div>

        <div class="grid gap-8 md:grid-cols-2">

            {{-- Apprenticeship --}}
            <div class="group relative rounded-[32px] border border-[#E5E7EB] bg-white p-10 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col">
                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#073057]/8 text-[#073057] group-hover:bg-[#073057] group-hover:text-white transition-colors">
                    <iconify-icon icon="lucide:graduation-cap" class="text-3xl"></iconify-icon>
                </div>
                <div class="inline-flex rounded-full bg-[#073057]/8 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#073057] mb-4 self-start">18–24 Months</div>
                <h3 class="text-[26px] font-extrabold text-[#073057] mb-3">Apprenticeship Programme</h3>
                <p class="text-[#6B7280] leading-relaxed mb-6">A structured work-based learning programme combining on-the-job experience with industry training. Designed for school leavers and early-career individuals ready to build a career in maritime or energy.</p>
                <ul class="space-y-3 mb-8 flex-grow">
                    @foreach(['18–24 month structured programme', 'Mentorship from industry professionals', 'Certified qualifications on completion', 'Pathway to full employment'] as $point)
                    <li class="flex items-center gap-3 text-[#374151]">
                        <span class="flex-shrink-0 flex h-5 w-5 items-center justify-center rounded-full bg-[#1AAD94]/15 text-[#1AAD94]">
                            <iconify-icon icon="lucide:check" class="text-xs"></iconify-icon>
                        </span>
                        <span class="text-sm font-medium">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('career.apprenticeship') }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#073057] rounded-[10px] text-white text-[13px] font-bold uppercase tracking-[0.08em] hover:bg-[#1AAD94] transition-colors self-start">
                    Learn About Apprenticeships
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>

            {{-- Internship --}}
            <div class="group relative rounded-[32px] border border-[#E5E7EB] bg-white p-10 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col">
                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#1AAD94]/10 text-[#1AAD94] group-hover:bg-[#1AAD94] group-hover:text-white transition-colors">
                    <iconify-icon icon="lucide:briefcase" class="text-3xl"></iconify-icon>
                </div>
                <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#1AAD94] mb-4 self-start">3–6 Months</div>
                <h3 class="text-[26px] font-extrabold text-[#073057] mb-3">Internship Programme</h3>
                <p class="text-[#6B7280] leading-relaxed mb-6">Short-term placements for students and graduates to gain real-world exposure within the maritime and energy industry. Work alongside JCL professionals and our employer network.</p>
                <ul class="space-y-3 mb-8 flex-grow">
                    @foreach(['3–6 month placements', 'Rotational exposure across departments', 'Live project involvement', 'Reference letter and career guidance on completion'] as $point)
                    <li class="flex items-center gap-3 text-[#374151]">
                        <span class="flex-shrink-0 flex h-5 w-5 items-center justify-center rounded-full bg-[#1AAD94]/15 text-[#1AAD94]">
                            <iconify-icon icon="lucide:check" class="text-xs"></iconify-icon>
                        </span>
                        <span class="text-sm font-medium">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('career.internship') }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] rounded-[10px] text-white text-[13px] font-bold uppercase tracking-[0.08em] hover:brightness-110 transition-all self-start">
                    Learn About Internships
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- WHY JCL --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <div class="inline-flex rounded-full bg-[#073057]/8 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#073057] mb-6">Why JCL</div>
            <h2 class="text-[42px] font-extrabold text-[#073057]">What sets our programmes apart</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach([
                ['icon' => 'lucide:users', 'title' => 'Industry Mentorship', 'desc' => 'Learn directly from experienced maritime and energy professionals who guide you every step of the way.'],
                ['icon' => 'lucide:award', 'title' => 'Recognised Qualifications', 'desc' => 'Leave with certifications that meet international industry standards and open doors globally.'],
                ['icon' => 'lucide:network', 'title' => 'Employer Network', 'desc' => 'Gain direct access to JCL\'s network of shipping companies, energy firms, and logistics operators.'],
            ] as $feature)
            <div class="rounded-[24px] bg-white border border-[#E5E7EB] p-8 hover:shadow-lg transition-shadow">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#1AAD94]/10 text-[#1AAD94]">
                    <iconify-icon icon="{{ $feature['icon'] }}" class="text-2xl"></iconify-icon>
                </div>
                <h3 class="text-lg font-bold text-[#073057] mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-[#6B7280] leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="bg-[#073057] py-24">
    <div class="container mx-auto px-6 text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-[42px] font-extrabold text-white mb-6">Not sure which programme suits you?</h2>
            <p class="text-lg text-white/70 mb-10">Reach out to our team and we'll help match you to the right career entry pathway based on your background and goals.</p>
            <a href="{{ route('contact.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] rounded-[10px] text-white text-[14px] font-bold uppercase tracking-[0.1em] hover:brightness-110 shadow-lg transition-all">
                Talk to Our Team
                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
        </div>
    </div>
</section>

@endsection
