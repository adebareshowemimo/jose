@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    @if (!empty($img['self_employment_hero']))
        <img src="{{ $img['self_employment_hero'] }}" alt="Self Employment Setup Services" class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Self Employment Setup Services</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">We Give You the Space, Tools, and Support to Launch Your Own Business — You Bring the Vision.</p>
    </div>
</section>

{{-- What Is This Service? --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:rocket"></iconify-icon>
                    Self Employment Setup
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">What Is This Service?</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">Starting your own business is one of the boldest steps you can take — but the biggest barriers are often the most practical ones: Where do I operate? What equipment do I need? Who will help me get started?</p>
                <p class="text-[#4B5563] leading-relaxed mb-5">At Jose Ocean Jobs, we remove those barriers. Our Self Employment Setup Service is designed to give aspiring entrepreneurs and self-starters everything they need to hit the ground running — from a physical space to work in, to the tools, appliances, and even a workforce to support them from day one.</p>
                <p class="text-[#4B5563] leading-relaxed mb-8">Whether you have a business idea that has been sitting on the back burner or you are ready to launch right now, we set you up for success.</p>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Book Your Free Consultation
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="relative rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[480px]">
                @if (!empty($img['self_employment_hero']))
                    <img src="{{ $img['self_employment_hero'] }}" alt="Self Employment Setup Services" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
                @endif
            </div>
        </div>
    </div>
</section>

{{-- What We Provide --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:package-2"></iconify-icon>
                What We Provide
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">Everything you need to start operating.</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ([
                ['lucide:building-2', 'Workspace and Space Allocation', "We provide ready-to-use workspaces suited to your business type. No need to worry about finding a location, signing long leases, or setting up from scratch — we handle it. Whether you need a small unit, an open floor, or a shared commercial space, we match you with the right environment."],
                ['lucide:wrench', 'Tools and Appliances', 'Every business needs the right equipment to operate. We supply the tools and appliances relevant to your trade or industry so you can start working immediately without the heavy upfront investment.'],
                ['lucide:users', 'Workforce Support', 'Need hands on deck from the start? We can connect you with skilled workers, assistants, or temporary staff to support your operation as it gets off the ground. You focus on running your business — we help you build the team behind it.'],
                ['lucide:handshake', 'Employer Engagement', 'We bridge the gap between you as a new employer and the wider business community. Through our employer engagement support, we help you understand your responsibilities, connect with relevant networks, and position your new business for growth and credibility from the start.'],
            ] as $item)
                <div class="rounded-[24px] bg-white border border-[#E5E7EB] p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="h-14 w-14 rounded-2xl bg-[#1AAD94]/10 text-[#1AAD94] flex items-center justify-center mb-5">
                        <iconify-icon icon="{{ $item[0] }}" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="text-[20px] font-extrabold text-[#073057] mb-3">{{ $item[1] }}</h3>
                    <p class="text-[#6B7280] text-sm leading-relaxed">{{ $item[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Who Is This For --}}
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
                        'Individuals who want to work for themselves but do not know where to start',
                        'People transitioning out of employment into self-employment',
                        'Those with a trade or skill ready to turn it into a business',
                        'Anyone who needs infrastructure and support before they can launch',
                        'Newcomers to the industry looking for a supported start',
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
                    <iconify-icon icon="lucide:star"></iconify-icon>
                    Why Jose Ocean Jobs?
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">Why Choose Jose Ocean Jobs?</h2>
                <ul class="space-y-3">
                    @foreach ([
                        'We understand the Maritime and Energy sector inside out',
                        'Practical, hands-on support — not just advice',
                        'Everything you need is in one place',
                        'We grow with you, from setup to scale',
                        'Trusted by employers and workers across the industry',
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
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['01', 'Consultation', 'You meet with our team to discuss your business idea, goals, and what you need to get started.'],
                ['02', 'Setup Plan', 'We put together a tailored setup package covering your space, tools, and support requirements.'],
                ['03', 'Space and Equipment Allocation', 'We assign your workspace and supply the necessary appliances and tools.'],
                ['04', 'Workforce Matching', 'If needed, we connect you with available workers suited to your operation.'],
                ['05', 'Employer Engagement', 'We walk you through your role as an employer and connect you with the right networks.'],
                ['06', 'Launch', 'You are ready to go. We remain on hand to support you as you grow.'],
            ] as $step)
                <div class="rounded-[24px] bg-white border border-[#E5E7EB] p-7 shadow-sm hover:shadow-lg transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-full bg-[#1AAD94] text-white font-extrabold flex items-center justify-center text-sm">{{ $step[0] }}</div>
                        <h3 class="text-[17px] font-extrabold text-[#073057]">{{ $step[1] }}</h3>
                    </div>
                    <p class="text-sm text-[#6B7280] leading-relaxed">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Ready to Work for Yourself?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Don't let logistics hold your dream back. Let us set the stage — you run the show. Get Started Today — Book Your Free Setup Consultation.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Book Your Free Consultation
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>
@endsection
