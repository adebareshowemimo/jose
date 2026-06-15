@extends('layouts.app')

@section('title', ($pageTitle ?? 'Definition of Terms').' — Jose Consulting Limited')
@section('meta_description', $pageDescription ?? '')

@section('content')
@php
    use Illuminate\Support\Str;
    $intro = $glossary['intro'] ?? '';
    $glossaryTerms = $glossary['terms'] ?? [];
@endphp

{{-- HERO --}}
<section class="relative hero-gradient py-16 lg:py-24 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-72 h-72 bg-[#1AAD94]/10 blur-[90px] rounded-full" aria-hidden="true"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[40px] md:text-[56px] font-extrabold leading-tight">{{ $pageTitle ?? 'Definition of Terms' }}</h1>
        <p class="mt-4 text-lg text-white/80 max-w-2xl">{{ $pageDescription ?? '' }}</p>
    </div>
</section>

{{-- DOCUMENT BODY --}}
<section class="bg-[#F9FAFB] py-16">
    <div class="container mx-auto px-6">
        <div class="grid gap-12 lg:grid-cols-[260px_1fr]">

            {{-- Sticky term navigation --}}
            <aside class="hidden lg:block">
                <nav class="lg:sticky lg:top-24 self-start bg-white rounded-[20px] border border-[#E0E0E0] p-6 max-h-[calc(100vh-8rem)] overflow-y-auto hide-scrollbar" aria-label="Defined terms">
                    <h2 class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-4">Defined terms</h2>
                    <ol class="space-y-1">
                        @foreach($glossaryTerms as $t)
                            <li>
                                <a href="#{{ Str::slug($t['term']) }}" class="block py-1.5 text-sm text-[#6B7280] hover:text-[#1AAD94] transition-colors">{{ $t['term'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </aside>

            {{-- Article --}}
            <article class="bg-white rounded-[24px] border border-[#E0E0E0] p-6 sm:p-8 lg:p-12 shadow-sm">
                <p class="text-[15px] sm:text-base leading-relaxed text-[#2C2C2C] mb-10">{{ $intro }}</p>

                <dl class="space-y-7">
                    @foreach($glossaryTerms as $t)
                        <div id="{{ Str::slug($t['term']) }}" class="scroll-mt-28 border-l-2 border-[#1AAD94] pl-5">
                            <dt class="text-lg font-bold text-[#073057]">“{{ $t['term'] }}”</dt>
                            <dd class="mt-1 text-[15px] sm:text-base leading-relaxed text-[#2C2C2C]">means {{ $t['definition'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                {{-- Back to Terms --}}
                <div class="mt-12 pt-8 border-t border-[#E0E0E0]">
                    <a href="{{ route('legal.terms') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#073057] hover:text-[#1AAD94] transition-colors">
                        <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                        Back to Terms of Service
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
