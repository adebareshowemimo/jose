@extends('layouts.app')

@section('title', ($pageTitle ?? 'About JCL').' — Jose Consulting Limited')

@section('content')
@php
    $profile = $profile ?? [];
    $img = $jclImages ?? [];
@endphp

{{-- HERO --}}
<section class="relative hero-gradient py-24 lg:py-40 text-white overflow-hidden">
    <img src="{{ $img['about_page'] ?? '' }}"
         alt="About Jose Consulting Limited"
         class="absolute inset-0 w-full h-full object-cover opacity-25" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/75 to-[#073057]/40"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-8 backdrop-blur-sm" style="display:none" aria-hidden="true">
                <iconify-icon icon="lucide:anchor" class="text-sm"></iconify-icon>
                <span>Established 2012</span>
            </div>
            <h1 class="text-[56px] font-extrabold leading-[1.05] tracking-tight md:text-[82px] mb-8">{{ $pageTitle ?? 'About JCL' }}</h1>
            <p class="text-xl md:text-2xl leading-relaxed text-white/80 max-w-2xl">{{ $pageDescription ?? 'We provide the bridge between elite maritime talent and global industry opportunity through expert training, strategic consulting, and operational excellence.' }}</p>
        </div>
    </div>
</section>

{{-- WHO WE ARE --}}
<section class="bg-[#F9FAFB] py-24 dot-pattern">
    <div class="container mx-auto px-6">
        <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <div class="bg-white rounded-[32px] p-8 lg:p-12 shadow-sm border border-gray-100">
                    <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-8">Who we are</div>
                    <h2 class="text-[42px] font-extrabold text-[#073057] mb-8">{{ $profile['name'] ?? 'Jose Consulting Limited' }}</h2>
                    <p class="text-lg leading-relaxed text-gray-600 mb-10">{{ $profile['summary'] ?? '' }}</p>

                    <img src="{{ $img['about_page'] ?? '' }}"
                         alt="About Jose Consulting Limited"
                         class="w-full h-[400px] object-cover rounded-[24px] mb-12 shadow-lg" loading="lazy" />

                    <div class="bg-[#F9FAFB] border border-gray-100 rounded-[24px] p-10 lg:p-14">
                        <span class="sr-only">Jose Consulting operates two sectors: Maritime/Logistics and Energy Workforce Development.</span>

                        <div class="flex justify-center">
                            <div class="rounded-xl bg-[#073057] text-white px-10 py-4 text-base font-extrabold shadow-md tracking-wide">2 SECTORS</div>
                        </div>

                        <div class="relative h-12" aria-hidden="true">
                            <div class="absolute left-1/2 -translate-x-1/2 top-0 w-[2px] h-6 bg-[#1AAD94]"></div>
                            <div class="absolute top-[18px] left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-[#7DE1D1]"></div>
                            <div class="absolute top-6 left-1/4 right-1/4 h-[2px] bg-[#1AAD94]"></div>
                            <div class="absolute top-6 left-1/4 -translate-x-1/2 w-[2px] h-6 bg-[#1AAD94]"></div>
                            <div class="absolute top-6 right-1/4 translate-x-1/2 w-[2px] h-6 bg-[#1AAD94]"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div class="rounded-xl border-2 border-[#1AAD94] bg-white p-7 text-center shadow-sm">
                                <div class="text-[12px] font-bold uppercase tracking-widest text-[#1AAD94] mb-3">Maritime/Logistics</div>
                                <p class="text-sm leading-relaxed text-gray-600">Recruitment, crew management, ship chandelling, and logistics workforce development.</p>
                            </div>
                            <div class="rounded-xl border-2 border-[#1AAD94] bg-white p-7 text-center shadow-sm">
                                <div class="text-[12px] font-bold uppercase tracking-widest text-[#1AAD94] mb-3">Energy Workforce Development</div>
                                <p class="text-sm leading-relaxed text-gray-600">Training, consulting, and career pathways for the energy sector.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#073057] rounded-[32px] p-10 lg:p-14 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#1AAD94]/10 blur-[80px] rounded-full"></div>
                <div class="relative z-10">
                    <div class="inline-flex rounded-full bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-10">The JCL Edge</div>
                    <h3 class="text-3xl font-extrabold mb-10 leading-tight">Why organizations and professionals choose JCL</h3>
                    <ul class="space-y-8">
                        @foreach(($profile['edge'] ?? []) as $item)
                            <li class="flex items-start gap-5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#1AAD94] text-white glow-teal">
                                    <iconify-icon icon="lucide:check-circle-2" class="text-lg"></iconify-icon>
                                </div>
                                <span class="text-white/75 leading-relaxed">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- IMAGE STRIP + VISION / MISSION --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            <img src="{{ $img['deck_officer'] ?? '' }}" alt="Maritime deck operations"
                 class="rounded-[24px] h-[300px] w-full object-cover shadow-md hover:scale-[1.02] transition-transform" loading="lazy" />
            <img src="{{ $img['offshore_vessel'] ?? '' }}" alt="Offshore vessel operations"
                 class="rounded-[24px] h-[300px] w-full object-cover shadow-md hover:scale-[1.02] transition-transform" loading="lazy" />
            <img src="{{ $img['container_port'] ?? '' }}" alt="Container port logistics"
                 class="rounded-[24px] h-[300px] w-full object-cover shadow-md hover:scale-[1.02] transition-transform" loading="lazy" />
            <img src="{{ $img['sailor_repairs'] ?? '' }}" alt="Vessel maintenance and repairs"
                 class="rounded-[24px] h-[300px] w-full object-cover shadow-md hover:scale-[1.02] transition-transform" loading="lazy" />
        </div>

        <div class="grid gap-12 md:grid-cols-2">
            <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-sm">
                <div class="inline-flex rounded-full bg-gray-100 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#073057] mb-8">Our Vision</div>
                <h3 class="text-3xl font-extrabold text-[#073057] mb-8">{{ $profile['vision']['statement'] ?? '' }}</h3>
                <div class="space-y-4">
                    @foreach(($profile['vision']['pillars'] ?? []) as $pillar)
                        <div class="inline-flex rounded-lg bg-gray-50 border border-gray-100 px-5 py-3 text-[13px] font-semibold text-gray-700">{{ $pillar }}</div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-sm">
                <div class="inline-flex rounded-full bg-gray-100 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#073057] mb-8">Our Mission</div>
                <h3 class="text-3xl font-extrabold text-[#073057] mb-8">{{ $profile['mission']['statement'] ?? '' }}</h3>
                <div class="space-y-4">
                    @foreach(($profile['mission']['actions'] ?? []) as $action)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors group">
                            <span class="text-sm font-bold text-gray-800">{{ $action }}</span>
                            <iconify-icon icon="lucide:arrow-right" class="text-[#1AAD94] group-hover:translate-x-1 transition-transform"></iconify-icon>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VALUES --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6 mb-16 text-center">
        <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Our Values</div>
        <h2 class="text-[48px] font-extrabold text-[#073057]">The Core Principles of JCL.</h2>
    </div>

    <div class="container mx-auto px-6">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach(($profile['values'] ?? []) as $value)
                <div class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                    <div class="h-14 w-14 rounded-2xl bg-[#073057]/5 flex items-center justify-center text-[#073057] mb-8 group-hover:bg-[#1AAD94] group-hover:text-white transition-all">
                        <iconify-icon icon="{{ $value['icon'] }}" class="text-2xl"></iconify-icon>
                    </div>
                    <h4 class="text-xl font-bold text-[#073057] mb-4">{{ $value['name'] }}</h4>
                    <p class="text-gray-500 leading-relaxed text-sm">{{ $value['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">
        <div class="rounded-[48px] relative overflow-hidden h-[450px] flex items-center shadow-2xl">
            <img src="{{ $img['hero_aerial_cargo'] ?? '' }}" alt="Global maritime operations"
                 class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
            <div class="absolute inset-0 bg-[#073057]/90"></div>
            <div class="relative z-10 w-full px-12 lg:px-24 flex flex-col lg:flex-row items-center justify-between gap-12 text-center lg:text-left">
                <div>
                    <h2 class="text-[42px] lg:text-[52px] font-extrabold text-white leading-tight mb-6">{{ $profile['final_cta']['headline'] ?? 'Be part of the JCL legacy.' }}</h2>
                    <p class="text-white/70 text-lg max-w-xl">{{ $profile['final_cta']['description'] ?? '' }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-5 shrink-0">
                    <a href="{{ route($profile['final_cta']['primary']['route'] ?? 'contact.index') }}" class="inline-flex items-center justify-center px-10 py-5 bg-[#1AAD94] rounded-[12px] text-white text-[16px] font-bold uppercase tracking-widest hover:scale-105 transition-all shadow-xl">
                        {{ $profile['final_cta']['primary']['label'] ?? 'Contact JCL' }}
                    </a>
                    <a href="{{ route('job.index') }}" class="inline-flex items-center justify-center px-10 py-5 border-2 border-white/20 rounded-[12px] text-white text-[16px] font-bold uppercase tracking-widest hover:bg-white/10 transition-all">
                        Explore Jobs
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
