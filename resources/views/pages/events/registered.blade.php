@extends('layouts.app')

@section('title', 'Registered · ' . $event->title)

@section('content')
<section class="bg-[#F9FAFB] py-16 md:py-24 min-h-[70vh]">
    <div class="container mx-auto px-6 max-w-2xl">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#6B7280]" />

        <div class="bg-white border border-[#E0E0E0] rounded-2xl p-8 md:p-10 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-700 mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#073057] mb-3">You're registered 🎟️</h1>
            <p class="text-[#4B5563] leading-relaxed mb-2">Your spot for <strong class="text-[#073057]">{{ $event->title }}</strong> is confirmed.</p>
            <p class="text-sm text-[#6B7280] mb-8">A confirmation email is on its way. We'll send a reminder closer to the event date.</p>

            <div class="bg-[#F9FAFB] border border-gray-100 rounded-xl p-5 text-left mb-8">
                <div class="text-xs space-y-2 text-[#4B5563]">
                    <p class="flex items-start gap-2"><iconify-icon icon="lucide:calendar" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span><strong class="text-[#073057]">{{ $event->display_date }}</strong></span></p>
                    <p class="flex items-start gap-2"><iconify-icon icon="lucide:map-pin" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span>{{ $event->location }}</span></p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#073057] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg transition">
                    More events
                </a>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 hover:border-[#1AAD94] hover:text-[#1AAD94] text-[#073057] text-sm font-bold uppercase tracking-widest rounded-lg transition">
                    Home
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
