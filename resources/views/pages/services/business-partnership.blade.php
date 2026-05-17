@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    @if (!empty($img['crew_management']))
        <img src="{{ $img['crew_management'] }}" alt="Business Partnerships" class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Business Partnerships</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">Expanding What We Can Do for You — Through the Power of Trusted, Licensed Partners.</p>
    </div>
</section>

{{-- What Is This Service? --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:handshake"></iconify-icon>
                    Business Partnerships
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">What Is This Service?</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">At Jose Ocean Jobs, we are committed to being more than just a jobs and training platform. We understand that the Maritime and Energy sector has wide-ranging needs — and that our clients, whether individuals or businesses, often require specialised services that go far beyond recruitment.</p>
                <p class="text-[#4B5563] leading-relaxed mb-5">That is why we have built a network of carefully selected, fully licensed business partners who deliver specialist maritime and energy services alongside us. Through these partnerships, we are able to extend our offering and ensure that you have access to everything you need — all connected through one trusted name.</p>
                <p class="text-[#4B5563] leading-relaxed mb-8">Our Business Partnership services are delivered in collaboration with licensed and accredited professionals in their respective fields, giving you the quality, compliance, and confidence you deserve.</p>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Enquire About Our Partnership Services
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[480px] flex items-center justify-center text-white">
                <div class="text-center p-12">
                    <iconify-icon icon="lucide:handshake" class="text-7xl mb-4 opacity-80"></iconify-icon>
                    <p class="text-lg font-bold uppercase tracking-[0.15em] opacity-80">One trusted name</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Our Partnership Services --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:layers"></iconify-icon>
                Our Partnership Services
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-3">A full ecosystem of maritime and energy services.</h2>
            <p class="text-[#4B5563]">Each service is delivered by a fully licensed, accredited partner — vetted by Jose Ocean Jobs and held to our standards of quality and integrity.</p>
        </div>

        @php
            $partnershipServices = [
                [
                    'route' => 'services.ship-chandelling',
                    'icon' => 'lucide:anchor',
                    'title' => 'Ship Chandelling',
                    'tagline' => 'Supplying vessels with everything they need — wherever they are.',
                    'desc' => 'Ship chandelling is the supply of provisions, stores, equipment, and consumables to vessels in port. Through our licensed chandelling partner, we ensure that ships are stocked and ready for sea with everything from food and beverages to safety equipment, cleaning supplies, deck stores, and more. Whether you are managing a single vessel or a fleet, our partner delivers reliable, timely, and cost-effective chandelling services so your crew is comfortable and your ship is fully equipped before it sets sail.',
                    'covered' => [
                        'Provisions and catering supplies',
                        'Deck, engine, and cabin stores',
                        'Safety and survival equipment',
                        'Bonded stores and duty-free supplies',
                        'Emergency and last-minute supply requests',
                    ],
                    'color' => 'teal',
                ],
                [
                    'route' => 'services.crew-management',
                    'icon' => 'lucide:users',
                    'title' => 'Crew Management',
                    'tagline' => 'The right crew, managed the right way.',
                    'desc' => 'Managing a crew is one of the most complex and critical responsibilities in the Maritime and Energy sector. Through our licensed crew management partner, we offer comprehensive crew management solutions that take the pressure off ship owners and operators — handling everything from recruitment and contracts to payroll and welfare. Our partner operates in full compliance with MLC (Maritime Labour Convention) standards, ensuring that every crew member is treated fairly, paid correctly, and supported throughout their time on board.',
                    'covered' => [
                        'Crew recruitment and selection',
                        'Contract management and documentation',
                        'Payroll and allotment processing',
                        'Visa, travel, and joining arrangements',
                        'Crew welfare and repatriation',
                        'Flag state compliance and certification checks',
                    ],
                    'color' => 'navy',
                ],
                [
                    'route' => 'services.marine-insurance',
                    'icon' => 'lucide:shield-check',
                    'title' => 'Marine Insurance',
                    'tagline' => 'Protecting what matters most — at sea and on shore.',
                    'desc' => 'The Maritime and Energy sector carries real risk — and having the right insurance in place is not optional, it is essential. Through our licensed marine insurance partner, we connect vessel owners, operators, cargo owners, and maritime businesses with tailored insurance solutions that provide genuine protection and peace of mind. Our partner works across a range of marine insurance products, ensuring that whatever your exposure, there is a policy designed to cover it.',
                    'covered' => [
                        'Hull and machinery insurance',
                        'Protection and indemnity (P&I) cover',
                        'Cargo and freight insurance',
                        'Offshore and energy risk cover',
                        'Liability insurance for maritime businesses',
                        'Small vessel and pleasure craft cover',
                    ],
                    'color' => 'teal',
                ],
                [
                    'route' => 'services.marine-procurement',
                    'icon' => 'lucide:package',
                    'title' => 'Marine Procurement',
                    'tagline' => 'Sourcing the right equipment and supplies — efficiently and cost-effectively.',
                    'desc' => 'Marine procurement is about getting the right products, parts, and materials to the right place at the right time — and at the right price. Through our licensed procurement partner, we manage the sourcing, purchasing, and logistics of marine equipment and supplies for vessels, ports, and Maritime and Energy sector operations of all sizes. From technical spare parts to large-scale equipment orders, our partner brings industry expertise, supplier relationships, and procurement systems that save you time, reduce costs, and keep your operations running smoothly.',
                    'covered' => [
                        'Marine spare parts and technical equipment',
                        'Safety and navigation equipment',
                        'Deck and engine room supplies',
                        'Port and terminal equipment sourcing',
                        'Vendor management and supplier negotiations',
                        'Logistics and delivery coordination',
                    ],
                    'color' => 'navy',
                ],
                [
                    'route' => 'services.mobilization',
                    'icon' => 'lucide:plane',
                    'title' => 'Mobilisation Services',
                    'tagline' => 'Getting your people where they need to be — ready, compliant, and on time.',
                    'desc' => 'In the Maritime and Energy sector, time is critical. Delays in mobilising crew or personnel cost money and disrupt operations. Through our licensed mobilisation partner, we manage the full end-to-end process of moving personnel from where they are to where they need to be — safely, efficiently, and with every requirement covered before they step foot on site or on board. Nothing is left to chance. Nothing is left behind.',
                    'covered' => [
                        'Visas and work permits for personnel travelling to or working across different countries',
                        'Travel arrangements including flights, transfers, and ground transportation',
                        'Accommodation at mobilisation points, offshore bases, or assignment locations',
                        'Onboarding coordination including documentation checks, inductions, and certification verification',
                        'Medical assessments including offshore medicals, fitness-to-work certificates, and destination-specific health clearances',
                    ],
                    'color' => 'teal',
                ],
            ];
        @endphp

        <div class="space-y-8">
            @foreach ($partnershipServices as $svc)
                @php $isTeal = $svc['color'] === 'teal'; @endphp
                <div class="rounded-[28px] border border-[#E5E7EB] bg-white p-8 md:p-10 shadow-sm hover:shadow-lg transition-all">
                    <div class="grid lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl {{ $isTeal ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'bg-[#073057]/10 text-[#073057]' }}">
                                <iconify-icon icon="{{ $svc['icon'] }}" class="text-3xl"></iconify-icon>
                            </div>
                            <h3 class="text-[24px] font-extrabold text-[#073057] mb-2">{{ $svc['title'] }}</h3>
                            <p class="text-sm italic text-[#1AAD94] font-semibold mb-5">{{ $svc['tagline'] }}</p>
                            <a href="{{ route($svc['route']) }}" class="inline-flex items-center gap-2 text-[13px] font-bold uppercase tracking-[0.08em] {{ $isTeal ? 'text-[#1AAD94]' : 'text-[#073057]' }} hover:gap-3 transition-all">
                                Learn more
                                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                            </a>
                        </div>
                        <div class="lg:col-span-2">
                            <p class="text-[#4B5563] leading-relaxed mb-5">{{ $svc['desc'] }}</p>
                            <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.15em] text-[#073057]">What is covered</div>
                            <ul class="grid sm:grid-cols-2 gap-x-6 gap-y-2">
                                @foreach ($svc['covered'] as $item)
                                    <li class="flex items-start gap-2 text-sm text-[#4B5563]">
                                        <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-0.5 shrink-0"></iconify-icon>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why Licensed Partners + Who Is This For --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:shield-check"></iconify-icon>
                    Why Licensed Partners
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Why We Work With Licensed Partners</h2>
                <p class="text-[#4B5563] leading-relaxed mb-6">We believe that every service we connect you with should meet the highest standards of quality, compliance, and professionalism. That is why every business partner we work with is:</p>
                <ul class="space-y-3">
                    @foreach ([
                        'Fully licensed and accredited in their field',
                        'Compliant with international maritime and energy regulations and standards',
                        'Experienced in serving the Maritime and Energy sector',
                        'Vetted and trusted by Jose Ocean Jobs before any partnership is formed',
                        'Committed to the same level of service and integrity that we uphold',
                    ] as $reason)
                        <li class="flex items-start gap-3 text-[#4B5563]">
                            <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0 text-xl"></iconify-icon>
                            <span>{{ $reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:user-check"></iconify-icon>
                    Who Is This For?
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">These services are ideal for:</h2>
                <ul class="space-y-3">
                    @foreach ([
                        'Ship owners and operators needing reliable chandelling and crew management',
                        'Cargo owners and freight operators requiring marine insurance',
                        'Maritime and energy businesses sourcing equipment and supplies through procurement',
                        'Fleet managers looking for a single point of contact for multiple services',
                        'New entrants to the Maritime and Energy sector needing guidance across several areas at once',
                    ] as $audience)
                        <li class="flex items-start gap-3 text-[#4B5563]">
                            <iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1 shrink-0 text-xl"></iconify-icon>
                            <span>{{ $audience }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:list-checks"></iconify-icon>
                The Process
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">How It Works</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-5">
            @foreach ([
                ['01', 'Tell Us What You Need', 'Reach out and let us know which partnership service you require.'],
                ['02', 'We Make the Introduction', 'We connect you directly with the relevant licensed partner best suited to your needs.'],
                ['03', 'Service Delivery', 'The partner delivers the service with full professionalism and compliance.'],
                ['04', 'We Stay Involved', 'Jose Ocean Jobs remains your point of contact, ensuring the experience meets your expectations throughout.'],
                ['05', 'Ongoing Relationship', 'We are here for the long term, supporting you across every service whenever you need us.'],
            ] as $step)
                <div class="rounded-[24px] bg-white border border-[#E5E7EB] p-6 shadow-sm hover:shadow-lg transition-all">
                    <div class="text-[#1AAD94] text-[24px] font-extrabold leading-none mb-3">{{ $step[0] }}</div>
                    <h3 class="text-[16px] font-extrabold text-[#073057] mb-2">{{ $step[1] }}</h3>
                    <p class="text-sm text-[#6B7280] leading-relaxed">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <p class="text-[#7DE1D1] text-[11px] font-bold uppercase tracking-[0.2em] mb-3">One Trusted Name. A World of Services.</p>
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Enquire About Our Partnership Services</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">We have done the hard work of finding the right partners so you do not have to. Whatever your maritime and energy business needs — we have got you covered.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Get in Touch Today
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>
@endsection
