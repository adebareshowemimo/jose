@extends('layouts.app')

@section('title', ($pageTitle ?? 'Partnerships & Expertise').' — Jose Consulting Limited')

@section('content')
@php
    $profile = $profile ?? [];
    $partnerships = $partnerships ?? [];
    $img = $jclImages ?? [];
@endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['aerial_container'] ?? '' }}"
         alt="Aerial view of container terminal representing global partnership reach"
         class="absolute inset-0 w-full h-full object-cover opacity-50" loading="eager" />
    <div class="absolute inset-0 hero-overlay"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-tight max-w-3xl">{{ $pageTitle ?? 'Amplifying Impact through Strategic Alliances' }}</h1>
        @if(!empty($pageDescription))
            <p class="mt-4 max-w-xl text-lg text-white/70">{{ $pageDescription }}</p>
        @endif
    </div>
</section>

{{-- Expertise & Mobilization --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="rounded-[32px] bg-white border border-[#E0E0E0] p-10 shadow-sm flex flex-col h-full">
                <div class="inline-flex self-start rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Expertise & Scale</div>
                <h2 class="text-[36px] font-extrabold text-[#073057] leading-tight mb-6">Scaling specialist delivery for the global maritime fleet.</h2>
                <p class="text-[#6B7280] leading-relaxed mb-8">Through its leadership network and technical partners, JCL can mobilize specialized training teams within two weeks' notice, enabling a flexible response to corporate, institutional, or group needs.</p>
                <div class="rounded-[24px] overflow-hidden mb-8 shadow-md">
                    <img src="{{ $img['container_port'] ?? '' }}"
                         alt="Port operations and logistics capability"
                         class="w-full h-64 object-cover" loading="lazy" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(($profile['expertise'] ?? []) as $area)
                        <div class="rounded-[12px] bg-[#F3F4F6] p-4 text-center border border-[#E0E0E0]">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-[#073057]">{{ $area }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col gap-8">
                <div class="rounded-[32px] bg-[#073057] p-10 text-white shadow-xl relative overflow-hidden flex-1 flex flex-col justify-center">
                    <div class="absolute right-0 top-0 h-40 w-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="inline-flex rounded-full bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-6">Mobilization Promise</div>
                        <h2 class="text-[32px] font-extrabold leading-tight mb-6">Responsive support across international corridors.</h2>
                        <p class="text-white/70 leading-relaxed text-lg">JCL's partnership model is designed to stay practical: mobilize the right experts, connect training to operational reality, and keep programs aligned with industry-recognized expectations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Strategic Partners --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6 mb-16">
        <div class="inline-flex rounded-full bg-[#073057]/5 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#073057] mb-6">Global Network</div>
        <h2 class="text-[48px] font-extrabold text-[#073057]">Strategic Partnerships</h2>
    </div>
    <div class="container mx-auto px-6">
        <div class="grid gap-8 lg:grid-cols-2">
            @foreach($partnerships as $index => $partner)
                <article class="group rounded-[32px] border border-[#E0E0E0] overflow-hidden transition-all hover:shadow-2xl">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ $index === 0 ? ($img['offshore_vessel'] ?? '') : ($img['cargo_colorful'] ?? '') }}"
                             alt="{{ $partner['name'] }} operations"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                    </div>
                    <div class="p-10">
                        <div class="flex justify-between items-start mb-6">
                            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-[#1AAD94]">{{ $partner['lead'] }}</div>
                            <iconify-icon icon="lucide:network" class="text-2xl text-[#073057]/20"></iconify-icon>
                        </div>
                        <h3 class="text-[28px] font-extrabold text-[#073057] mb-2">{{ $partner['name'] }}</h3>
                        <p class="font-bold text-[#073057] mb-4">{{ $partner['focus'] }}</p>
                        <p class="text-[#6B7280] leading-relaxed">{{ $partner['strength'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Value Proposition + CTA --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="grid gap-8 lg:grid-cols-[1.3fr_0.7fr]">
            <div class="rounded-[32px] bg-white border border-[#E0E0E0] p-12 shadow-sm h-full">
                <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-8">Value Proposition</div>
                <h2 class="text-[40px] font-extrabold text-[#073057] leading-tight mb-8">Connecting local talent to international standards.</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach(array_chunk($profile['edge'] ?? [], ceil(count($profile['edge'] ?? []) / 2)) as $column)
                        <div class="space-y-4">
                            @foreach($column as $item)
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#1AAD94]/10 text-[#1AAD94]">
                                        <iconify-icon icon="lucide:check-circle-2" class="text-lg"></iconify-icon>
                                    </div>
                                    <p class="font-medium text-[#2C2C2C]">{{ $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[32px] bg-[#073057] p-10 text-white shadow-xl h-full flex flex-col">
                <div class="inline-flex self-start rounded-full bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-8">Take the next step</div>
                <p class="text-white/70 text-lg leading-relaxed mb-10">Ready to integrate our expertise into your operations or join our elite network of maritime professionals?</p>
                <div class="flex flex-col gap-4 mt-auto">
                    <a href="{{ route('contact.index') }}" class="inline-flex justify-center px-8 py-4 bg-[#1AAD94] rounded-[12px] text-white text-[14px] font-bold uppercase tracking-widest hover:brightness-110 transition-all">Partner with JCL</a>
                    <a href="{{ route('auth.register') }}" class="inline-flex justify-center px-8 py-4 border-2 border-white/20 rounded-[12px] text-white text-[14px] font-bold uppercase tracking-widest hover:bg-white/10 transition-all">Register as Talent</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
