@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-[#073057] py-24 lg:py-32">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(26,173,148,0.18),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(26,173,148,0.12),transparent_30%)]"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-8 backdrop-blur-sm">
                <iconify-icon icon="lucide:layers" class="text-sm"></iconify-icon>
                <span>What We Offer</span>
            </div>
            <h1 class="text-[48px] md:text-[64px] font-extrabold leading-[1.05] tracking-tight text-white mb-6">
                End-to-end <span class="text-[#1AAD94]">Maritime &amp; Energy</span> Workforce Solutions
            </h1>
            <p class="text-xl text-white/70 leading-relaxed max-w-2xl mb-10">
                From training and crew management to marine procurement and insurance — JCL delivers comprehensive services that power the maritime and energy industry.
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

{{-- SERVICES GRID --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">

        <div class="text-center mb-16">
            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Our Services</div>
            <h2 class="text-[42px] font-extrabold text-[#073057] leading-tight">Everything your workforce needs, in one place</h2>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-[#6B7280]">Each service is designed to meet international industry standards and deliver measurable results for individuals and organisations.</p>
        </div>

        @php
            $services = [
                ['route' => 'services.training',          'icon' => 'lucide:graduation-cap', 'title' => 'Training',                       'desc' => 'Professional training programs aligned to international maritime and energy standards — from STCW and NEBOSH to soft skills and leadership.', 'color' => 'teal'],
                ['route' => 'services.crew-management',   'icon' => 'lucide:users',           'title' => 'Crew Management',                'desc' => 'End-to-end crew management services for vessel operators — sourcing, documentation, welfare, and rotation support.', 'color' => 'navy'],
                ['route' => 'services.ship-chandelling',  'icon' => 'lucide:package',         'title' => 'Ship Chandelling',               'desc' => 'Comprehensive vessel supply and ship chandelling services to keep your fleet operational and well-provisioned.', 'color' => 'teal'],
                ['route' => 'services.crew-abandonment',  'icon' => 'lucide:life-buoy',       'title' => 'Solution to Crew Abandonment',   'desc' => 'Specialist support and resolution services for seafarers and vessels affected by crew abandonment situations.', 'color' => 'navy'],
                ['route' => 'services.marine-procurement','icon' => 'lucide:anchor',          'title' => 'Marine Procurement',             'desc' => 'Strategic marine procurement — sourcing equipment, spares, and technical materials for vessel and offshore operations.', 'color' => 'teal'],
                ['route' => 'services.marine-insurance',  'icon' => 'lucide:shield-check',    'title' => 'Marine Insurance',               'desc' => 'Marine insurance advisory and placement services helping operators and professionals secure the right coverage globally.', 'color' => 'navy'],
                ['route' => 'services.travel-management', 'icon' => 'lucide:plane',           'title' => 'Travel Management Service',      'desc' => 'End-to-end travel management for crew and offshore personnel — visas, flights, and logistics coordination.', 'color' => 'teal'],
            ];
        @endphp

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($services as $svc)
            @php $isTeal = $svc['color'] === 'teal'; @endphp
            <div class="group flex flex-col rounded-[28px] border border-[#E5E7EB] bg-white p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl {{ $isTeal ? 'bg-[#1AAD94]/10 text-[#1AAD94] group-hover:bg-[#1AAD94] group-hover:text-white' : 'bg-[#073057]/8 text-[#073057] group-hover:bg-[#073057] group-hover:text-white' }} transition-colors">
                    <iconify-icon icon="{{ $svc['icon'] }}" class="text-2xl"></iconify-icon>
                </div>
                <h3 class="text-[20px] font-extrabold text-[#073057] mb-3">{{ $svc['title'] }}</h3>
                <p class="text-[#6B7280] text-sm leading-relaxed flex-grow mb-6">{{ $svc['desc'] }}</p>
                <a href="{{ route($svc['route']) }}"
                   class="inline-flex items-center gap-2 text-[13px] font-bold uppercase tracking-[0.08em] {{ $isTeal ? 'text-[#1AAD94]' : 'text-[#073057]' }} hover:gap-3 transition-all">
                    Learn more
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- STATS STRIP --}}
<section class="bg-[#F9FAFB] py-16">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([
                ['value' => '7+',    'label' => 'Service Areas'],
                ['value' => '100+',  'label' => 'Trained Professionals'],
                ['value' => 'Global','label' => 'Industry Standards'],
                ['value' => '24/7',  'label' => 'Client Support'],
            ] as $stat)
            <div>
                <div class="text-[42px] font-extrabold text-[#073057] leading-none">{{ $stat['value'] }}</div>
                <div class="mt-2 text-sm font-semibold uppercase tracking-[0.1em] text-[#6B7280]">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="bg-[#073057] py-24">
    <div class="container mx-auto px-6 text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-[42px] font-extrabold text-white mb-6">Ready to talk about your workforce needs?</h2>
            <p class="text-lg text-white/70 mb-10">Our team is ready to design a solution that fits your organisation — from training to full crew management.</p>
            <a href="{{ route('contact.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] rounded-[10px] text-white text-[14px] font-bold uppercase tracking-[0.1em] hover:brightness-110 shadow-lg transition-all">
                Contact JCL
                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
        </div>
    </div>
</section>

@endsection
