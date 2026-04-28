@extends('layouts.app')

@section('title', $program->title . ' — Jose Consulting Limited')
@section('meta_description', $program->short_description ?? $program->title)

@push('styles')
<style>
    .training-prose h1, .training-prose h2 { font-weight: 800; color: #073057; line-height: 1.2; margin: 1.6em 0 0.5em; }
    .training-prose h1 { font-size: 1.75rem; }
    .training-prose h2 { font-size: 1.4rem; }
    .training-prose h3 { font-weight: 700; color: #073057; font-size: 1.15rem; margin: 1.3em 0 0.4em; }
    .training-prose p { font-size: 1rem; line-height: 1.75; margin-bottom: 1rem; }
    .training-prose ul, .training-prose ol { padding-left: 1.5rem; margin-bottom: 1.1rem; }
    .training-prose ul { list-style: disc; }
    .training-prose ol { list-style: decimal; }
    .training-prose li { margin-bottom: 0.4rem; line-height: 1.65; }
    .training-prose a { color: #1AAD94; text-decoration: underline; }
    .training-prose a:hover { color: #0F8B75; }
    .training-prose blockquote { border-left: 4px solid #1AAD94; padding: 0.5rem 1rem; margin: 1.2rem 0; background: #F9FAFB; color: #4B5563; font-style: italic; }
    .training-prose code { background: #F3F4F6; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.9em; color: #073057; }
    .training-prose pre { background: #0A1929; color: #E5E7EB; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0; }
    .training-prose pre code { background: transparent; color: inherit; padding: 0; }
    .training-prose strong { color: #073057; }
</style>
@endpush

@section('content')
@php
    $isApprenticeship = $program->type === 'apprenticeship';
    $listingRoute = $isApprenticeship ? 'career.apprenticeship' : 'training.index';
    $listingLabel = $isApprenticeship ? 'Apprenticeships' : 'Training';
    $isFree = $program->isFree();
    $isAuthed = auth()->check();
@endphp

{{-- Hero --}}
<section class="relative bg-[#073057] text-white overflow-hidden">
    @if ($program->image_url)
        <div class="absolute inset-0">
            <img src="{{ $program->image_url }}" alt="{{ $program->title }}" class="w-full h-full object-cover opacity-30" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-b from-[#073057]/70 via-[#073057]/85 to-[#073057]"></div>
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]/20"></div>
    @endif

    <div class="relative container mx-auto px-6 py-14 md:py-20 max-w-5xl">
        <nav class="text-xs text-white/70 mb-6 flex flex-wrap items-center gap-1.5">
            <a href="{{ url('/') }}" class="hover:text-white">Home</a>
            <span>/</span>
            <a href="{{ route($listingRoute) }}" class="hover:text-white">{{ $listingLabel }}</a>
            <span>/</span>
            <span class="text-white/90 truncate max-w-[40ch]">{{ $program->title }}</span>
        </nav>

        <div class="flex flex-wrap gap-2 mb-5">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1AAD94] text-white text-[11px] font-bold uppercase tracking-widest shadow-lg">
                {{ $isApprenticeship ? 'Apprenticeship' : 'Training' }}
            </span>
            @if ($program->category)
                <span class="inline-flex px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white/90 text-[11px] font-bold uppercase tracking-wider">{{ $program->category }}</span>
            @endif
            @if ($program->level)
                <span class="inline-flex px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white/90 text-[11px] font-bold uppercase tracking-wider">{{ $program->level }}</span>
            @endif
        </div>

        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05] tracking-tight max-w-4xl mb-6">{{ $program->title }}</h1>

        @if ($program->short_description)
            <p class="text-lg md:text-xl text-white/80 leading-relaxed max-w-3xl mb-8">{{ $program->short_description }}</p>
        @endif

        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 pt-6 border-t border-white/15">
            @if ($program->duration)
                <div class="flex items-center gap-1.5 text-sm text-white/80">
                    <iconify-icon icon="lucide:clock"></iconify-icon> {{ $program->duration }}
                </div>
            @endif
            @if ($program->starts_at)
                <div class="flex items-center gap-1.5 text-sm text-white/80">
                    <iconify-icon icon="lucide:calendar"></iconify-icon> Starts {{ $program->starts_at->format('M d, Y') }}
                </div>
            @endif
            @if ($program->capacity)
                <div class="flex items-center gap-1.5 text-sm text-white/80">
                    <iconify-icon icon="lucide:users"></iconify-icon> {{ $program->capacity }} seats
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Body --}}
<section class="bg-[#F9FAFB] py-12 md:py-16">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-[minmax(0,1fr)_360px] gap-8 max-w-6xl mx-auto">

            {{-- Long description --}}
            <article class="bg-white rounded-2xl border border-[#E0E0E0] shadow-sm overflow-hidden">
                <div class="px-6 sm:px-10 py-10">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-[#1AAD94] mb-2">Programme overview</h2>
                    <h3 class="text-2xl font-extrabold text-[#073057] mb-6">What you'll learn</h3>
                    <div class="training-prose text-[#2C2C2C] leading-relaxed">
                        {!! $program->long_description !!}
                    </div>
                </div>
            </article>

            {{-- Sticky enrol card --}}
            <aside>
                <div class="sticky top-24 space-y-5">
                    <div class="bg-white border border-[#E0E0E0] rounded-2xl p-6 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Tuition</p>
                        <p class="text-4xl font-extrabold text-[#073057]">
                            @if ($isFree)
                                <span class="text-[#1AAD94]">Free</span>
                            @else
                                {{ $program->currency }} {{ number_format((float) $program->price, 2) }}
                            @endif
                        </p>
                        <p class="text-xs text-[#6B7280] mt-1">One-time enrolment fee</p>

                        @if ($program->enrol_deadline)
                            <p class="mt-4 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 flex items-center gap-1.5">
                                <iconify-icon icon="lucide:clock"></iconify-icon>
                                Enrol by {{ $program->enrol_deadline->format('M d, Y') }}
                            </p>
                        @endif

                        @if ($isAuthed)
                            <form method="POST" action="{{ route('training.enrol', $program) }}" class="mt-5">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow-lg">
                                    <iconify-icon icon="lucide:graduation-cap"></iconify-icon>
                                    {{ $isFree ? 'Enrol now' : 'Enrol & pay' }}
                                </button>
                            </form>
                            @if (! $isFree)
                                <p class="mt-2 text-[11px] text-gray-400 text-center">Secure checkout · Paystack or bank transfer</p>
                            @endif
                        @else
                            <a href="{{ route('auth.login', ['redirect' => url()->current()]) }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-[#073057] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow-lg">
                                Sign in to enrol
                                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                            </a>
                            <p class="mt-2 text-[11px] text-gray-400 text-center">No account? <a href="{{ route('auth.register') }}" class="text-[#1AAD94] font-semibold">Create one in 30 seconds</a></p>
                        @endif

                        <ul class="mt-6 pt-5 border-t border-gray-100 space-y-2.5 text-sm text-[#4B5563]">
                            @if ($program->duration)
                                <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> {{ $program->duration }}</li>
                            @endif
                            @if ($program->level)
                                <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> {{ $program->level }}</li>
                            @endif
                            <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> Certificate on completion</li>
                            <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> Access to JCL employer network</li>
                        </ul>
                    </div>

                    <a href="{{ route($listingRoute) }}" class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-semibold text-[#073057] hover:border-[#1AAD94] hover:text-[#1AAD94] transition">
                        <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                        All {{ strtolower($listingLabel) }}
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
