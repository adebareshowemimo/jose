@extends('layouts.dashboard')

@section('title', 'Boost my profile')
@section('page-title', 'Boost my profile')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
@php
    $isFeatured = $candidate->isFeatured();
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#073057]">Get more eyes on your profile</h2>
    <p class="text-sm text-[#6B7280] mt-1">Boosted profiles appear at the top of the candidate listing employers browse — typically 3–5× more profile views.</p>
</div>

@if (session('error'))
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
@endif

@if ($isFeatured)
    <div class="mb-6 bg-gradient-to-br from-[#1AAD94] to-[#0F8B75] text-white rounded-2xl p-6 shadow-lg">
        <div class="flex items-center gap-3 mb-2">
            <iconify-icon icon="lucide:sparkles" class="text-xl"></iconify-icon>
            <p class="text-xs font-bold uppercase tracking-widest">Currently boosted</p>
        </div>
        <h3 class="text-2xl font-extrabold mb-1">Your profile is featured</h3>
        <p class="text-sm text-white/85">Active until <strong>{{ $candidate->featured_until->format('M d, Y · H:i') }}</strong> ({{ now()->diffInDays($candidate->featured_until) }} days remaining). You can stack additional boosts to extend.</p>
    </div>
@endif

<div class="grid md:grid-cols-3 gap-5">
    @foreach ($packages as $pkg)
        @php $isBest = $pkg['days'] === 30; @endphp
        <form method="POST" action="{{ route('candidate.boost.purchase') }}" class="bg-white border-2 {{ $isBest ? 'border-[#1AAD94] shadow-xl' : 'border-[#E0E0E0]' }} rounded-2xl p-6 flex flex-col relative">
            @csrf
            <input type="hidden" name="days" value="{{ $pkg['days'] }}">

            @if ($isBest)
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex px-3 py-1 rounded-full bg-[#1AAD94] text-white text-[10px] font-bold uppercase tracking-widest shadow">Most popular</span>
            @endif

            <div class="text-center pt-2">
                <p class="text-xs font-bold uppercase tracking-widest text-[#1AAD94] mb-1">{{ $pkg['label'] }}</p>
                <p class="text-4xl font-extrabold text-[#073057] mb-1">{{ $pkg['days'] }}<span class="text-base font-normal text-[#6B7280]"> days</span></p>
                <p class="text-xs text-[#6B7280] mb-5">{{ $pkg['tagline'] }}</p>

                <div class="my-5 py-4 border-y border-gray-100">
                    <p class="text-3xl font-extrabold text-[#073057]">{{ $currency }} {{ number_format($pkg['price'], 2) }}</p>
                    <p class="text-xs text-[#6B7280] mt-1">One-time payment</p>
                </div>

                <ul class="space-y-2 text-sm text-[#4B5563] text-left mb-6">
                    <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> Top of search results for {{ $pkg['days'] }} days</li>
                    <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> "Featured" badge on your profile</li>
                    <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> Stacks with active boosts</li>
                    @if ($pkg['days'] >= 30)
                        <li class="flex items-start gap-2"><iconify-icon icon="lucide:check" class="text-[#1AAD94] mt-0.5"></iconify-icon> Priority slot in homepage carousel</li>
                    @endif
                </ul>
            </div>

            <button type="submit" class="mt-auto w-full px-5 py-3 {{ $isBest ? 'bg-[#1AAD94]' : 'bg-[#073057]' }} hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow">
                Boost for {{ $pkg['days'] }} days
            </button>
        </form>
    @endforeach
</div>

<div class="mt-8 bg-[#F9FAFB] border border-gray-200 rounded-xl p-5 text-sm text-[#4B5563]">
    <p class="font-semibold text-[#073057] mb-2 flex items-center gap-2">
        <iconify-icon icon="lucide:info"></iconify-icon>
        How boosts work
    </p>
    <ul class="space-y-1 ml-6 list-disc text-xs">
        <li>Pay once via Paystack or bank transfer — your boost activates as soon as the payment is confirmed.</li>
        <li>Boosted profiles always sort above non-boosted ones in the candidate listing.</li>
        <li>You can buy additional boosts while one is active — they stack on top of your remaining time.</li>
        <li>Want always-featured forever? Consider <a href="#" class="text-[#1AAD94] font-semibold">Premium membership</a> instead.</li>
    </ul>
</div>
@endsection
