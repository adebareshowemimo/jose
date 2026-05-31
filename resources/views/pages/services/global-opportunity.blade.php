@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    @if (!empty($img['global_opportunity_hero']))
        <img src="{{ $img['global_opportunity_hero'] }}" alt="Global Opportunities" class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Global Opportunities</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">Your Skills Have No Borders — We Help You Take Them to the World.</p>
    </div>
</section>

{{-- What Is This Service? --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:globe-2"></iconify-icon>
                    Global Opportunities
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">What Is This Service?</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">The Maritime and Energy sector is one of the most truly global industries on the planet. Ships sail every sea, offshore platforms operate on every coast, marine research spans every ocean, and maritime trade connects every continent. That means the opportunities for skilled professionals are not limited to one country — they are worldwide.</p>
                <p class="text-[#4B5563] leading-relaxed mb-8">At Jose Ocean Jobs, our Global Opportunities service is built to connect individuals, businesses, and organisations with openings, partnerships, and possibilities far beyond their home shores. Whether you are a professional looking to work internationally, a business seeking to expand into new markets, or an organisation wanting to tap into global talent — we open the door.</p>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Speak to Our Team
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="relative rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[480px]">
                @if (!empty($img['global_opportunity_hero']))
                    <img src="{{ $img['global_opportunity_hero'] }}" alt="Global Opportunities" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
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
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">A full-service global pathway.</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['lucide:plane-takeoff', 'International Job Placements', 'We connect skilled Maritime and Energy sector professionals with employment opportunities across the globe. From seafarers and marine engineers to offshore energy specialists and fisheries experts — if your skills are in demand somewhere in the world, we help you get there.'],
                ['lucide:users-round', 'Cross-Border Recruitment for Employers', 'For businesses and organisations operating internationally, we source and place qualified candidates from a global talent pool. We understand the complexities of international hiring — from visa requirements to certification recognition — and we guide you through every step.'],
                ['lucide:trending-up', 'Market Entry & International Expansion', 'For companies looking to grow beyond their current borders, we provide connections, introductions, and insights into new maritime and energy markets. We help you identify the right partners and make your move with confidence.'],
                ['lucide:file-check-2', 'International Compliance & Documentation', 'Working across borders comes with paperwork — certifications, visas, work permits, STCW compliance, flag state requirements, and more. We provide guidance on the documentation and compliance requirements needed in different countries and jurisdictions.'],
                ['lucide:network', 'Global Industry Network Access', 'Through our growing network of international partners, employers, institutions, and industry bodies, we give you access to connections that would otherwise take years to build. One conversation can open doors across multiple regions.'],
                ['lucide:sparkles', 'Emerging Market Opportunities', 'The Maritime and Energy sector is growing rapidly in emerging economies — particularly in Africa, Southeast Asia, and Latin America. We identify and present opportunities in these markets for professionals and businesses ready to be part of something big from the ground up.'],
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
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">This service is perfect for:</h2>
                <ul class="space-y-3">
                    @foreach ([
                        'Maritime and Energy sector professionals seeking to work abroad',
                        'Seafarers looking for international vessel placements',
                        'Offshore energy and renewables workers exploring global roles',
                        'Businesses wanting to recruit from an international talent pool',
                        'Companies looking to expand their operations into new countries',
                        'Organisations seeking global partnerships in the Maritime and Energy sector',
                        'Graduates and early-career professionals ready to explore the world',
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
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">A clear path from interest to placement.</h2>
                <div class="space-y-5">
                    @foreach ([
                        ['01', 'Profile and Goals Assessment', 'We start by understanding who you are, what you offer, and where in the world you want to go — or grow.'],
                        ['02', 'Global Matching', 'We search our international network to identify the right opportunities, employers, or markets that align with your profile and ambitions.'],
                        ['03', 'Introduction and Connection', 'We make the right introductions and facilitate conversations between you and the relevant international parties.'],
                        ['04', 'Compliance and Documentation Support', 'We guide you through the international requirements specific to your destination or target market.'],
                        ['05', 'Placement and Follow-Up', 'Once placed or connected, we follow up to ensure everything is running smoothly and remain available for ongoing support.'],
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

{{-- Regions We Connect You To --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:map"></iconify-icon>
                Regions
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">Regions We Connect You To</h2>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['lucide:globe', 'Europe', 'North Sea, offshore renewables, major shipping hubs'],
                ['lucide:compass', 'Africa', 'Fast-growing maritime and oil and gas sectors'],
                ['lucide:anchor', 'Asia-Pacific', 'Shipbuilding, fishing, and major port operations'],
                ['lucide:ship', 'Americas', 'Offshore energy, cruise industry, and maritime trade'],
                ['lucide:sun', 'Middle East', 'Offshore oil and gas, logistics, and port development'],
                ['lucide:waves', 'Remote and Deep Sea', 'Expeditions, research vessels, and specialist operations worldwide'],
            ] as $region)
                <div class="rounded-[20px] bg-white border border-[#E5E7EB] p-6 flex items-start gap-4 hover:shadow-lg transition-all">
                    <div class="shrink-0 h-12 w-12 rounded-xl bg-[#073057]/10 text-[#073057] flex items-center justify-center">
                        <iconify-icon icon="{{ $region[0] }}" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="text-[17px] font-extrabold text-[#073057] mb-1">{{ $region[1] }}</h3>
                        <p class="text-sm text-[#6B7280] leading-relaxed">{{ $region[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why Global Opportunities Matter Now + Why JOJ --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:clock"></iconify-icon>
                    Right Now
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Why Global Opportunities Matter Now</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">The world needs the Maritime and Energy sector — for trade, energy, food, and exploration. And the industry needs skilled, passionate, globally mobile professionals and forward-thinking businesses more than ever.</p>
                <p class="text-[#4B5563] leading-relaxed mb-5">Climate change, the energy transition, and the rise of blue economy initiatives mean that new roles and new markets are opening up every single day.</p>
                <p class="text-[#073057] font-bold leading-relaxed">There has never been a better time to think globally.</p>
            </div>
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:star"></iconify-icon>
                    Why Jose Ocean Jobs?
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">A trusted partner on every coast.</h2>
                <ul class="space-y-3">
                    @foreach ([
                        'A growing international network spanning multiple continents',
                        'Deep understanding of global maritime and energy standards and requirements',
                        'Experience placing professionals in roles across different countries',
                        'Trusted by international employers seeking quality talent',
                        'Committed to opening real, meaningful global pathways — not just listings',
                    ] as $reason)
                        <li class="flex items-start gap-3 text-[#4B5563]">
                            <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0 text-xl"></iconify-icon>
                            <span>{{ $reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">The World Is Waiting — Are You Ready?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Your next opportunity might not be around the corner. It might be across an ocean. Let us help you get there.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Explore Global Opportunities
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>
@endsection
