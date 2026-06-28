@extends('layouts.app')

@section('title', ($pageTitle ?? 'Legal').' — Jose Consulting Limited')
@section('meta_description', $pageDescription ?? '')

@section('content')
@php
    $meta = $document['meta'] ?? [];
    $sections = $document['sections'] ?? [];
@endphp

{{-- HERO --}}
<section class="relative hero-gradient py-16 lg:py-24 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-72 h-72 bg-[#1AAD94]/10 blur-[90px] rounded-full" aria-hidden="true"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[40px] md:text-[56px] font-extrabold leading-tight">{{ $pageTitle ?? 'Legal' }}</h1>
        <p class="mt-4 text-lg text-white/80 max-w-2xl">{{ $pageDescription ?? '' }}</p>
        <div class="mt-8 flex flex-wrap items-center gap-3 text-[12px] font-bold uppercase tracking-[0.12em]">
            @foreach(array_filter([$meta['effective'] ?? null, $meta['version'] ?? null, $meta['site'] ?? null]) as $chip)
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-[#7DE1D1] backdrop-blur-sm">{{ $chip }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- DOCUMENT BODY --}}
<section class="bg-[#F9FAFB] py-16">
    <div class="container mx-auto px-6">
        <div class="grid gap-12 lg:grid-cols-[260px_1fr]">

            {{-- Sticky section navigation --}}
            <aside class="hidden lg:block">
                <nav class="lg:sticky lg:top-24 self-start bg-white rounded-[20px] border border-[#E0E0E0] p-6 max-h-[calc(100vh-8rem)] overflow-y-auto hide-scrollbar" aria-label="Table of contents">
                    <h2 class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-4">On this page</h2>
                    <ol class="space-y-1">
                        @foreach($sections as $s)
                            <li>
                                <a href="#{{ $s['id'] }}" class="block py-1.5 text-sm text-[#6B7280] hover:text-[#1AAD94] transition-colors">
                                    <span class="font-semibold text-[#073057]">{{ $s['number'] }}.</span> {{ $s['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </aside>

            {{-- Article --}}
            <article class="bg-white rounded-[24px] border border-[#E0E0E0] p-6 sm:p-8 lg:p-12 shadow-sm">

                @isset($crossReference)
                    <div class="mb-10 flex items-start gap-3 rounded-[14px] border border-[#1AAD94]/20 bg-[#1AAD94]/5 p-5">
                        <iconify-icon icon="lucide:info" class="text-xl text-[#1AAD94] mt-0.5"></iconify-icon>
                        <p class="text-sm leading-relaxed text-[#2C2C2C]">{!! $crossReference !!}</p>
                    </div>
                @endisset

                @foreach($sections as $s)
                    <section id="{{ $s['id'] }}" class="scroll-mt-28 mb-12 last:mb-0">
                        <h2 class="text-2xl font-extrabold text-[#073057] mb-5">{{ $s['number'] }}. {{ $s['title'] }}</h2>

                        @foreach($s['blocks'] as $b)
                            @switch($b['type'])
                                @case('p')
                                    <p class="text-[15px] sm:text-base leading-relaxed text-[#2C2C2C] mb-4">{{ $b['text'] }}</p>
                                    @break

                                @case('h3')
                                    <h3 class="text-lg font-bold text-[#073057] mt-7 mb-3">{{ $b['text'] }}</h3>
                                    @break

                                @case('ul')
                                    <ul class="list-disc pl-6 space-y-2 text-[15px] sm:text-base leading-relaxed text-[#2C2C2C] mb-5 marker:text-[#1AAD94]">
                                        @foreach($b['items'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                    @break

                                @case('table')
                                    <div class="mb-6 overflow-x-auto rounded-[14px] border border-[#E0E0E0]">
                                        <table class="w-full text-left text-[14px] text-[#2C2C2C]">
                                            <thead class="bg-[#F3F4F6] text-[#073057]">
                                                <tr>
                                                    @foreach($b['table']['head'] as $heading)
                                                        <th class="px-4 py-3 font-bold whitespace-nowrap">{{ $heading }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-[#E0E0E0]">
                                                @foreach($b['table']['rows'] as $row)
                                                    <tr class="align-top">
                                                        @foreach($row as $i => $cell)
                                                            <td class="px-4 py-3 leading-relaxed {{ $i === 0 ? 'font-mono text-[13px] text-[#073057] whitespace-nowrap' : '' }}">{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @break

                                @case('contact')
                                    @php $c = $b['contact']; @endphp
                                    <div class="rounded-[16px] border border-[#E0E0E0] bg-[#F9FAFB] p-6 space-y-2 text-[15px] text-[#2C2C2C]">
                                        <p class="text-lg font-bold text-[#073057]">{{ $c['org'] }}</p>
                                        <p>Trading as: <span class="font-semibold">{{ $c['trading'] }}</span></p>
                                        <p>Website:
                                            <a href="https://{{ $c['website'] }}" class="font-semibold text-[#1AAD94] hover:underline">{{ $c['website'] }}</a>
                                        </p>
                                        <p>Email:
                                            <a href="mailto:{{ $c['email'] }}" class="font-semibold text-[#1AAD94] hover:underline">{{ $c['email'] }}</a>
                                        </p>
                                        <p>WhatsApp:
                                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $c['whatsapp']) }}" target="_blank" rel="noopener" class="font-semibold text-[#1AAD94] hover:underline">{{ $c['whatsapp'] }}</a>
                                        </p>
                                        <p>{{ $c['location'] }}</p>
                                    </div>
                                    @break
                            @endswitch
                        @endforeach
                    </section>
                @endforeach

                {{-- Document footer note --}}
                <div class="mt-12 pt-8 border-t border-[#E0E0E0] text-sm text-[#6B7280] space-y-1">
                    <p>This document is the property of Jose Consulting Limited. JoseOceanJobs is a trading name of JCL.</p>
                    <p>&copy; 2026 Jose Consulting Limited. All rights reserved.</p>
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
