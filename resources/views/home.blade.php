@extends('layouts.app')

@section('title', 'Jose Consulting Limited (JCL) — Workforce Transformation for Maritime & Energy')

@section('content')
@php
    $profile = $jclProfile ?? [];
    $hero = $profile['hero'] ?? [];
    $img = $jclImages ?? [];
@endphp

<section
    class="relative overflow-hidden bg-[#073057] py-24 lg:py-32"
    x-data="{
        slides: [
            { src: '{{ $img['deck_officer'] ?? '' }}', alt: 'Deck officer at work' },
            { src: '{{ $img['offshore_vessel'] ?? '' }}', alt: 'Offshore vessel operations' },
            { src: '{{ $img['container_port'] ?? '' }}', alt: 'Container port operations' },
            { src: '{{ $img['home_1'] ?? '' }}', alt: 'Maritime professional' },
            { src: '{{ $img['home_2'] ?? '' }}', alt: 'Seafarer operations' },
            { src: '{{ $img['home_3'] ?? '' }}', alt: 'Maritime industry' },
        ],
        current: 0,
        interval: null,
        init() {
            this.interval = setInterval(() => {
                this.current = (this.current + 1) % this.slides.length;
            }, 5000);
        }
    }"
>
    <div class="absolute inset-0">
        <template x-for="(slide, index) in slides" :key="index">
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-cover bg-center transition-all duration-700"
                    :class="current === index ? 'opacity-100 scale-100' : 'opacity-0 scale-105'"
                    :style="`background-image: linear-gradient(135deg, rgba(7, 48, 87, 0.88), rgba(4, 29, 54, 0.8)), url('${slide.src}')`"
                ></div>
            </div>
        </template>
        <div class="absolute inset-0 hero-dot-grid opacity-35"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(26,173,148,0.18),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(26,173,148,0.14),transparent_28%)]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-5xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-8 backdrop-blur-sm">
                <iconify-icon icon="lucide:globe-2" class="text-sm"></iconify-icon>
                <span>{{ $hero['eyebrow'] ?? 'Jose Consulting Limited' }}</span>
            </div>

            <h1 class="max-w-4xl text-[56px] font-extrabold leading-[1.05] tracking-tight text-white md:text-[72px]">
                {{ $hero['headline'] ?? 'World-class training, consulting, and career pathways.' }}
            </h1>

            @if(!empty($hero['description']))
            <p class="mt-8 max-w-2xl text-xl leading-relaxed text-white/70">
                {{ $hero['description'] }}
            </p>
            @endif

            <div class="mt-12 flex flex-wrap gap-4">
                <a href="{{ route($hero['primary_cta']['route'] ?? 'auth.register') }}" class="inline-flex px-8 py-4 bg-[#1AAD94] rounded-[8px] text-white text-[14px] font-bold uppercase tracking-[0.1em] hover:brightness-110 shadow-lg transition-all">
                    {{ $hero['primary_cta']['label'] ?? 'Start your pathway' }}
                </a>
                <a href="{{ route($hero['secondary_cta']['route'] ?? 'contact.index') }}" class="inline-flex px-8 py-4 border-2 border-white/20 rounded-[8px] text-white text-[14px] font-bold uppercase tracking-[0.1em] hover:bg-white/10 transition-all backdrop-blur-sm">
                    {{ $hero['secondary_cta']['label'] ?? 'Contact JCL' }}
                </a>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-6">
                @foreach(($profile['stats'] ?? []) as $stat)
                    <div class="md:col-span-3 rounded-[18px] border border-white/10 bg-white/5 p-6 backdrop-blur-sm shadow-[0_16px_40px_rgba(0,0,0,0.18)]">
                        <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#7DE1D1]">{{ $stat['label'] }}</div>
                        <div class="mt-2 text-sm text-white/70 leading-snug">{{ $stat['description'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2">
        <template x-for="(slide, index) in slides" :key="`hero-dot-${index}`">
            <button
                type="button"
                @click="current = index"
                class="h-2 rounded-full transition-all duration-300"
                :class="current === index ? 'w-8 bg-[#1AAD94]' : 'w-2 bg-white/40 hover:bg-white/70'"
                :aria-label="`Show hero slide ${index + 1}`"
            ></button>
        </template>
    </div>
</section>

{{-- WORKFORCE TRANSFORMATION --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">
        <div class="grid gap-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Workforce transformation</div>
                <h2 class="text-[48px] font-extrabold leading-tight text-[#073057]">Helping individuals and organizations move into global opportunity.</h2>
                <p class="mt-8 text-xl leading-relaxed text-[#6B7280]">We help individuals and organizations strengthen employability, workforce retention, and international readiness across the maritime and energy sectors.</p>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <div class="rounded-[24px] border border-[#E0E0E0] bg-[#F9FAFB] p-8 transition-all hover:border-[#1AAD94] hover:shadow-xl">
                        <div class="text-4xl font-extrabold text-[#073057] mb-2">{{ number_format($job_count ?? 0) }}</div>
                        <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94]">Open opportunities</div>
                    </div>
                    <div class="rounded-[24px] border border-[#E0E0E0] bg-[#F9FAFB] p-8 transition-all hover:border-[#1AAD94] hover:shadow-xl">
                        <div class="text-4xl font-extrabold text-[#073057] mb-2">{{ number_format($company_count ?? 0) }}</div>
                        <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94]">Organizations</div>
                    </div>
                    <div class="rounded-[24px] border border-[#E0E0E0] bg-[#F9FAFB] p-8 transition-all hover:border-[#1AAD94] hover:shadow-xl">
                        <div class="text-4xl font-extrabold text-[#073057] mb-2">{{ number_format($candidate_count ?? 0) }}</div>
                        <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94]">Talent profiles</div>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] overflow-hidden shadow-2xl bg-[#073057] p-10 text-white">
                <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">The Pathway</div>
                <div class="space-y-8">
                    @foreach(($profile['journey'] ?? []) as $index => $step)
                        <div class="flex items-start gap-6">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $index === 1 ? 'bg-[#1AAD94]' : 'bg-white/10' }} text-sm font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <div>
                                <h3 class="text-lg font-bold">{{ $step['title'] }}</h3>
                                <p class="mt-1 text-sm text-white/60">{{ $step['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VISION / MISSION --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="rounded-[32px] bg-white border border-[#E0E0E0] p-10 shadow-sm">
                <div class="inline-flex rounded-full bg-[#073057]/5 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#073057] mb-6">Our vision</div>
                <h2 class="text-[36px] font-extrabold text-[#073057] leading-tight mb-8">{{ $profile['vision']['statement'] ?? '' }}</h2>
                <ul class="space-y-4">
                    @foreach(($profile['vision']['pillars'] ?? []) as $pillar)
                        <li class="flex items-center gap-4 text-[#2C2C2C]">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#1AAD94]/10 text-[#1AAD94]">
                                <iconify-icon icon="lucide:check-circle-2" class="text-lg"></iconify-icon>
                            </div>
                            <span class="font-medium leading-relaxed">{{ $pillar }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-[32px] bg-[#073057] p-10 text-white shadow-sm relative overflow-hidden">
                <div class="absolute right-0 top-0 h-40 w-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex rounded-full bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-6">Our mission</div>
                    <h2 class="text-[36px] font-extrabold leading-tight mb-8">{{ $profile['mission']['statement'] ?? '' }}</h2>
                    <ul class="space-y-4">
                        @foreach(($profile['mission']['actions'] ?? []) as $action)
                            <li class="flex items-center gap-4 text-white/80">
                                <iconify-icon icon="lucide:arrow-right" class="text-[#1AAD94]"></iconify-icon>
                                <span class="font-medium leading-relaxed">{{ $action }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VALUES --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6 text-center mb-16">
        <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-6">Values</div>
        <h2 class="text-[48px] font-extrabold text-[#073057]">The principles that shape how JCL delivers.</h2>
        <p class="mt-6 max-w-2xl mx-auto text-xl text-[#6B7280]">Our values drive everything from training design to global partnership selection.</p>
    </div>

    <div class="container mx-auto px-6">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach(($profile['values'] ?? []) as $value)
                <div class="group rounded-[28px] border border-[#E0E0E0] bg-white p-8 transition-all hover:shadow-2xl hover:-translate-y-2">
                    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1AAD94]/10 text-[#1AAD94] transition-colors group-hover:bg-[#1AAD94] group-hover:text-white">
                        <iconify-icon icon="{{ $value['icon'] }}" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-bold text-[#073057]">{{ $value['name'] }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-[#6B7280]">{{ $value['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="bg-white py-24">
    <div class="container mx-auto px-6">
        <div class="rounded-[48px] bg-[#073057] relative overflow-hidden p-12 lg:p-20 shadow-2xl">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            <div class="relative z-10 grid gap-12 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <div>
                    <div class="inline-flex rounded-full bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-8">Join the Fleet</div>
                    <h2 class="text-[48px] lg:text-[60px] font-extrabold text-white leading-none mb-8">{{ $profile['final_cta']['headline'] ?? 'Ready to build globally competitive pathways?' }}</h2>
                    <p class="text-xl text-white/70 max-w-2xl">{{ $profile['final_cta']['description'] ?? 'Connect with JCL today to explore career advancement, organizational training, or consulting solutions for the modern maritime world.' }}</p>
                </div>
                <div class="flex flex-col sm:flex-row lg:flex-col gap-5">
                    <a href="{{ route($profile['final_cta']['primary']['route'] ?? 'contact.index') }}" class="inline-flex items-center justify-center px-10 py-5 bg-[#1AAD94] rounded-[12px] text-white text-[16px] font-extrabold uppercase tracking-widest hover:scale-105 transition-all shadow-xl">
                        {{ $profile['final_cta']['primary']['label'] ?? 'Contact JCL Now' }}
                    </a>
                    <a href="{{ route($profile['final_cta']['secondary']['route'] ?? 'job.index') }}" class="inline-flex items-center justify-center px-10 py-5 border-2 border-white/20 rounded-[12px] text-white text-[16px] font-extrabold uppercase tracking-widest hover:bg-white/10 transition-all">
                        {{ $profile['final_cta']['secondary']['label'] ?? 'Browse Open Jobs' }}
                    </a>
                </div>
            </div>
            <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-[#1AAD94]/20 blur-[100px]"></div>
        </div>
    </div>
</section>
@endsection
