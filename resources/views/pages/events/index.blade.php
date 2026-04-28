@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@push('styles')
<style>
    .event-prose p { margin-bottom: 0.85rem; }
    .event-prose p:last-child { margin-bottom: 0; }
    .event-prose h2, .event-prose h3 { font-weight: 700; color: #073057; margin: 1.1rem 0 0.4rem; }
    .event-prose h2 { font-size: 1.2rem; }
    .event-prose h3 { font-size: 1.05rem; }
    .event-prose ul, .event-prose ol { padding-left: 1.4rem; margin-bottom: 0.8rem; }
    .event-prose ul { list-style: disc; }
    .event-prose ol { list-style: decimal; }
    .event-prose li { margin-bottom: 0.3rem; }
    .event-prose a { color: #1AAD94; text-decoration: underline; }
    .event-prose strong { color: #073057; }
</style>
@endpush

@section('content')
@php
    $img = $jclImages ?? [];

    // Split events for layout: featured first, then upcoming, then past
    $allHosted = collect($events ?? []);
    $featured = $allHosted->firstWhere('is_featured', true) ?? $allHosted->first();
    $otherHosted = $allHosted->reject(fn ($e) => $e === $featured)->values();

    $hostedUpcoming = $otherHosted->filter(fn ($e) => in_array($e['status'] ?? null, ['upcoming', 'active']))->values();
    $hostedPast     = $otherHosted->filter(fn ($e) => ($e['status'] ?? null) === 'completed')->values();

    $monthShort = function ($event) {
        if (! empty($event['starts_at'])) {
            try { return strtoupper((new \DateTime((string) $event['starts_at']))->format('M')); } catch (\Throwable $e) {}
        }
        // fallback: pull from display_date string e.g. "June 18 - 20, 2026"
        $tokens = preg_split('/\s+/', (string) ($event['date'] ?? ''));
        return strtoupper(mb_substr($tokens[0] ?? 'TBD', 0, 3));
    };
    $dayShort = function ($event) {
        if (! empty($event['starts_at'])) {
            try { return (new \DateTime((string) $event['starts_at']))->format('j'); } catch (\Throwable $e) {}
        }
        if (preg_match('/(\d{1,2})/', (string) ($event['date'] ?? ''), $m)) return $m[1];
        return '—';
    };
@endphp

{{-- ─── Hero ──────────────────────────────────────────────────── --}}
<section class="relative h-[480px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ $img['aerial_container'] ?? '' }}"
         alt="Events"
         class="absolute inset-0 w-full h-full object-cover opacity-30" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/90 to-[#073057]/40"></div>

    {{-- Subtle pattern overlay --}}
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1AAD94]/15 border border-[#1AAD94]/30 text-[#7DE1D1] text-[11px] font-bold uppercase tracking-[0.15em] mb-5">
            <iconify-icon icon="lucide:sparkles"></iconify-icon>
            What's on
        </span>
        <h1 class="text-[44px] md:text-[64px] font-extrabold text-white leading-[1.05] max-w-3xl">Events that move the maritime industry forward</h1>
        <p class="mt-5 max-w-2xl text-lg text-white/75 leading-relaxed">JCL-hosted gatherings, plus the global conferences, summits, and trade shows we follow closely on behalf of our employers and candidates.</p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="#hosted" class="inline-flex items-center gap-2 px-5 py-3 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold rounded-xl transition shadow-lg">
                <iconify-icon icon="lucide:calendar-check"></iconify-icon>
                JCL Events
            </a>
            <a href="#industry" class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/15 backdrop-blur border border-white/20 text-white text-sm font-bold rounded-xl transition">
                <iconify-icon icon="lucide:globe-2"></iconify-icon>
                Industry Calendar
            </a>
        </div>
    </div>
</section>

{{-- ─── Featured Event Spotlight ──────────────────────────────── --}}
@if ($featured)
    <section class="bg-[#F9FAFB] py-12 md:py-16">
        <div class="container mx-auto px-6">
            <div class="bg-white rounded-3xl border border-[#E0E0E0] shadow-xl overflow-hidden grid lg:grid-cols-[1.2fr_1fr]">
                {{-- Image side --}}
                <div class="relative aspect-[16/10] lg:aspect-auto bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] overflow-hidden">
                    @if (! empty($featured['image_url']))
                        <img src="{{ $featured['image_url'] }}" alt="{{ $featured['title'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-white px-8">
                            <span class="text-3xl md:text-4xl font-extrabold text-center text-white/90 leading-tight">{{ $featured['title'] }}</span>
                        </div>
                    @endif
                    <div class="absolute top-5 left-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400 text-amber-900 text-[11px] font-bold uppercase tracking-wider shadow">
                            <iconify-icon icon="lucide:star" class="text-xs"></iconify-icon>
                            Featured
                        </span>
                        @if (($featured['status'] ?? '') === 'upcoming')
                            <span class="inline-flex px-3 py-1 rounded-full bg-white/95 text-[#1AAD94] text-[11px] font-bold uppercase tracking-wider shadow">Upcoming</span>
                        @endif
                    </div>
                </div>

                {{-- Content side --}}
                <div class="p-8 md:p-10 lg:p-12 flex flex-col justify-center">
                    <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-3">{{ $featured['type'] ?? 'Event' }}</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#073057] leading-tight mb-4">{{ $featured['title'] }}</h2>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-[#4B5563] mb-5">
                        <span class="inline-flex items-center gap-1.5">
                            <iconify-icon icon="lucide:calendar" class="text-[#1AAD94]"></iconify-icon>
                            {{ $featured['date'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <iconify-icon icon="lucide:map-pin" class="text-[#1AAD94]"></iconify-icon>
                            {{ $featured['location'] }}
                        </span>
                    </div>

                    @php $featuredDesc = (string) ($featured['description'] ?? ''); @endphp
                    @if (str_contains($featuredDesc, '<'))
                        <div class="event-prose text-[#4B5563] leading-relaxed mb-7">{!! $featuredDesc !!}</div>
                    @else
                        <p class="text-[#4B5563] leading-relaxed mb-7">{{ $featuredDesc }}</p>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        @if (! empty($featured['register_url']))
                            <a href="{{ $featured['register_url'] }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow">
                                <iconify-icon icon="lucide:external-link"></iconify-icon>
                                Register on partner site
                            </a>
                        @elseif (! empty($featured['is_sold_out']))
                            <span class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-500 text-sm font-bold uppercase tracking-widest rounded-xl">
                                Sold out
                            </span>
                        @elseif (! empty($featured['is_paid']))
                            <a href="{{ $featured['register_show_url'] ?? '#' }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow">
                                <iconify-icon icon="lucide:ticket"></iconify-icon>
                                Register & pay · {{ $featured['currency'] }} {{ number_format((float) $featured['price'], 2) }}
                            </a>
                        @elseif (! empty($featured['is_free_internal']))
                            <a href="{{ $featured['register_show_url'] ?? '#' }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow">
                                <iconify-icon icon="lucide:check"></iconify-icon>
                                Reserve your spot
                            </a>
                        @else
                            <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow">
                                <iconify-icon icon="lucide:mail"></iconify-icon>
                                Contact us
                            </a>
                        @endif
                        <a href="#hosted" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-[#073057] hover:bg-[#073057] hover:text-white text-[#073057] text-sm font-bold uppercase tracking-widest rounded-xl transition">
                            More events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

{{-- ─── JCL Hosted Events ─────────────────────────────────────── --}}
<section id="hosted" class="py-20 md:py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-12">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                    <iconify-icon icon="lucide:calendar-days"></iconify-icon>
                    JCL Events
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-[#073057]">Hosted &amp; organised by JCL</h2>
                <p class="text-[#6B7280] mt-2 max-w-2xl">Roundtables, training intensives, and recruitment-day events bringing employers and qualified maritime professionals together.</p>
            </div>
            <a href="{{ route('contact.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-[#073057] hover:text-[#1AAD94] transition">
                Host with us
                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
        </div>

        @if ($hostedUpcoming->isEmpty() && $hostedPast->isEmpty() && ! $featured)
            <div class="bg-[#F9FAFB] rounded-2xl border border-dashed border-gray-300 p-16 text-center">
                <iconify-icon icon="lucide:calendar-x" class="text-5xl text-gray-300"></iconify-icon>
                <p class="mt-3 text-[#6B7280]">No events scheduled yet — check back soon, or <a href="{{ route('contact.index') }}" class="text-[#1AAD94] font-semibold">get in touch</a> to be notified.</p>
            </div>
        @else
            {{-- Upcoming --}}
            @if ($hostedUpcoming->isNotEmpty())
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($hostedUpcoming as $event)
                        <article class="group flex flex-col bg-white rounded-2xl border border-[#E0E0E0] shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                            {{-- Image with date chip --}}
                            <div class="relative aspect-[16/10] {{ ! empty($event['image_url']) ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }} overflow-hidden">
                                @if (! empty($event['image_url']))
                                    <img src="{{ $event['image_url'] }}" alt="{{ $event['title'] }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center px-6">
                                        <span class="text-center font-extrabold text-white/90 text-2xl leading-tight">{{ $event['title'] }}</span>
                                    </div>
                                @endif

                                {{-- Date badge --}}
                                <div class="absolute top-4 left-4 bg-white rounded-xl shadow-lg overflow-hidden text-center w-14">
                                    <div class="bg-[#1AAD94] text-white text-[10px] font-bold uppercase py-0.5 tracking-wider">{{ $monthShort($event) }}</div>
                                    <div class="text-[#073057] font-extrabold text-xl py-1.5 leading-none">{{ $dayShort($event) }}</div>
                                </div>

                                {{-- Status pill --}}
                                <span class="absolute top-4 right-4 inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-wider">
                                    Upcoming
                                </span>
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <span class="text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-2">{{ $event['type'] }}</span>
                                <h3 class="text-lg font-extrabold text-[#073057] mb-3 group-hover:text-[#1AAD94] transition-colors leading-snug">{{ $event['title'] }}</h3>

                                <div class="space-y-1.5 mb-4 text-sm text-[#6B7280]">
                                    <p class="flex items-center gap-1.5">
                                        <iconify-icon icon="lucide:calendar" class="shrink-0 text-[#1AAD94]"></iconify-icon>
                                        {{ $event['date'] }}
                                    </p>
                                    <p class="flex items-center gap-1.5">
                                        <iconify-icon icon="lucide:map-pin" class="shrink-0 text-[#1AAD94]"></iconify-icon>
                                        {{ $event['location'] }}
                                    </p>
                                </div>

                                <p class="text-[#4B5563] text-sm leading-relaxed mb-5 line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags((string) $event['description']), 220) }}</p>

                                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                                    @if (! empty($event['register_url']))
                                        <a href="{{ $event['register_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#073057] hover:text-[#1AAD94] transition group/btn">
                                            Register on partner site
                                            <iconify-icon icon="lucide:external-link"></iconify-icon>
                                        </a>
                                    @elseif (! empty($event['is_sold_out']))
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-gray-200 text-gray-500 text-xs font-bold uppercase tracking-wider">Sold out</span>
                                    @elseif (! empty($event['is_paid']))
                                        <span class="text-sm font-extrabold text-[#073057]">{{ $event['currency'] }} {{ number_format((float) $event['price'], 2) }}</span>
                                        <a href="{{ $event['register_show_url'] ?? '#' }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#1AAD94] hover:brightness-110 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition">
                                            Register
                                            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                                        </a>
                                    @elseif (! empty($event['is_free_internal']))
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase tracking-wider">Free</span>
                                        <a href="{{ $event['register_show_url'] ?? '#' }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#073057] hover:brightness-110 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition">
                                            Reserve
                                            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                                        </a>
                                    @else
                                        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#073057] hover:text-[#1AAD94] transition group/btn">
                                            Contact us
                                            <iconify-icon icon="lucide:arrow-right" class="transition-transform group-hover/btn:translate-x-1"></iconify-icon>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            {{-- Past events (collapsed) --}}
            @if ($hostedPast->isNotEmpty())
                <div class="mt-14 pt-10 border-t border-gray-100" x-data="{ open: false }">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">Past events</p>
                            <h3 class="text-xl md:text-2xl font-extrabold text-[#073057] mt-1">From the JCL archive</h3>
                        </div>
                        <button @click="open = !open" class="text-sm font-semibold text-[#073057] hover:text-[#1AAD94] inline-flex items-center gap-1">
                            <span x-text="open ? 'Hide' : 'Show all'"></span>
                            <iconify-icon icon="lucide:chevron-down" x-bind:class="open && 'rotate-180'" class="transition-transform"></iconify-icon>
                        </button>
                    </div>
                    <div x-show="open" x-collapse x-cloak class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($hostedPast as $event)
                            <div class="bg-[#F9FAFB] rounded-xl p-5 border border-gray-100 opacity-80 hover:opacity-100 transition">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 block">Past · {{ $event['type'] }}</span>
                                <h4 class="font-bold text-[#073057] mb-2">{{ $event['title'] }}</h4>
                                <p class="text-xs text-[#6B7280] flex items-center gap-1.5">
                                    <iconify-icon icon="lucide:calendar"></iconify-icon> {{ $event['date'] }}
                                </p>
                                <p class="text-xs text-[#6B7280] flex items-center gap-1.5 mt-1">
                                    <iconify-icon icon="lucide:map-pin"></iconify-icon> {{ $event['location'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</section>

{{-- ─── Industry Calendar ─────────────────────────────────────── --}}
<section id="industry" class="py-20 md:py-24 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14 max-w-2xl mx-auto">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#073057]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#073057] mb-4">
                <iconify-icon icon="lucide:globe"></iconify-icon>
                Industry Calendar
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-[#073057]">Key global maritime events</h2>
            <p class="text-[#6B7280] mt-3">Conferences, summits, and trade shows we keep on the radar for our employers and seafaring professionals.</p>
        </div>

        @if (empty($industry_events))
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center max-w-xl mx-auto">
                <iconify-icon icon="lucide:globe" class="text-4xl text-gray-300"></iconify-icon>
                <p class="mt-2 text-sm text-[#6B7280]">No industry events tracked yet.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                @foreach ($industry_events as $ie)
                    <div class="group bg-white rounded-2xl border border-[#E0E0E0] overflow-hidden shadow-sm hover:shadow-lg transition flex flex-col">
                        {{-- Cover --}}
                        <div class="relative h-32 {{ ! empty($ie['image_url']) ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] to-[#1AAD94]' }} overflow-hidden">
                            @if (! empty($ie['image_url']))
                                <img src="{{ $ie['image_url'] }}" alt="{{ $ie['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="absolute inset-0 opacity-25 mix-blend-overlay" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.5), transparent 40%);"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <iconify-icon icon="lucide:globe-2" class="text-5xl text-white/40"></iconify-icon>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full bg-white/95 backdrop-blur text-[#073057] text-[10px] font-bold uppercase tracking-wider shadow">{{ $ie['type'] ?? 'Industry' }}</span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <h4 class="text-[#073057] font-extrabold text-base mb-3 leading-snug group-hover:text-[#1AAD94] transition-colors">{{ $ie['title'] }}</h4>
                            <div class="space-y-1 text-sm text-[#6B7280] mb-3">
                                <p class="flex items-center gap-1.5">
                                    <iconify-icon icon="lucide:calendar" class="shrink-0 text-[#1AAD94]"></iconify-icon>
                                    {{ $ie['date'] }}
                                </p>
                                <p class="flex items-center gap-1.5">
                                    <iconify-icon icon="lucide:map-pin" class="shrink-0 text-[#1AAD94]"></iconify-icon>
                                    {{ $ie['location'] }}
                                </p>
                            </div>
                            <p class="text-[#4B5563] text-sm leading-relaxed line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags((string) $ie['description']), 220) }}</p>

                            @if (! empty($ie['register_url']))
                                <a href="{{ $ie['register_url'] }}" target="_blank" rel="noopener" class="mt-4 pt-3 border-t border-gray-100 inline-flex items-center gap-1.5 text-sm font-bold text-[#073057] hover:text-[#1AAD94] transition group/btn">
                                    Visit website
                                    <iconify-icon icon="lucide:external-link" class="text-xs"></iconify-icon>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ─── CTA Banner ────────────────────────────────────────────── --}}
<section class="relative py-20 md:py-24 bg-[#073057] overflow-hidden">
    {{-- Decorative blobs --}}
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#1AAD94]/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#1AAD94]/10 rounded-full blur-3xl"></div>

    <div class="container mx-auto px-6 relative">
        <div class="max-w-3xl mx-auto text-center">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-white/80 text-[11px] font-bold uppercase tracking-[0.15em] mb-6">
                <iconify-icon icon="lucide:handshake"></iconify-icon>
                Partner with JCL
            </span>
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-5 leading-tight">Want to host an event with us?</h2>
            <p class="text-white/75 text-lg mb-8 max-w-xl mx-auto">We co-host with industry bodies, employers, and training institutions to run impactful maritime, offshore, and energy gatherings.</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:brightness-110 text-white font-bold uppercase tracking-widest text-sm rounded-xl transition shadow-lg">
                    Get in Touch
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-white/10 hover:bg-white/15 backdrop-blur border border-white/20 text-white font-bold uppercase tracking-widest text-sm rounded-xl transition">
                    Read our news
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
