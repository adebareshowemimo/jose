@extends('layouts.dashboard')

@section('title', 'Candidate Premium')
@section('page-title', 'Premium Membership')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#073057]">Stand out with Premium</h2>
    <p class="text-sm text-[#6B7280] mt-1">Always-featured profile, priority support, and the perks serious candidates use to land their next role faster.</p>
</div>

@if (session('error'))
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
@endif

@if ($currentSubscription)
    <div class="mb-6 bg-gradient-to-br from-[#1AAD94] to-[#0F8B75] text-white rounded-2xl p-6 shadow-lg">
        <div class="flex items-center gap-3 mb-2">
            <iconify-icon icon="lucide:crown" class="text-xl"></iconify-icon>
            <p class="text-xs font-bold uppercase tracking-widest">Active Premium</p>
        </div>
        <h3 class="text-2xl font-extrabold mb-1">{{ $currentSubscription->plan->name ?? 'Premium' }}</h3>
        <p class="text-sm text-white/85">
            Billing: <strong>{{ ucfirst($currentSubscription->billing_cycle ?? 'monthly') }}</strong>
            · Renews: <strong>{{ $currentSubscription->ends_at?->format('M d, Y') ?? '—' }}</strong>
        </p>
    </div>
@endif

@if ($plans->isEmpty())
    <div class="bg-[#F9FAFB] border border-dashed border-gray-300 rounded-xl p-12 text-center">
        <iconify-icon icon="lucide:crown" class="text-5xl text-gray-300"></iconify-icon>
        <p class="mt-3 text-[#6B7280]">No premium plans are available right now.</p>
    </div>
@else
    @foreach ($plans as $plan)
        <div class="grid md:grid-cols-2 gap-5 mb-6"
             x-data="{ cycle: 'monthly' }">
            {{-- Plan summary --}}
            <div class="bg-white border border-[#E0E0E0] rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-widest">
                        <iconify-icon icon="lucide:crown" class="text-xs"></iconify-icon>
                        Premium
                    </span>
                </div>
                <h3 class="text-2xl font-extrabold text-[#073057]">{{ $plan->name }}</h3>
                @if ($plan->description)
                    <p class="text-sm text-[#6B7280] mt-2 mb-5">{{ $plan->description }}</p>
                @endif

                <ul class="space-y-2.5 text-sm text-[#4B5563] mt-5">
                    @if ($plan->hasBenefit('always_featured'))
                        <li class="flex items-start gap-2"><iconify-icon icon="lucide:check-circle" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span><strong>Always-featured</strong> profile placement</span></li>
                    @endif
                    @if ($plan->hasBenefit('priority_support'))
                        <li class="flex items-start gap-2"><iconify-icon icon="lucide:check-circle" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span>Priority support from our team</span></li>
                    @endif
                    @if ($plan->hasBenefit('profile_analytics'))
                        <li class="flex items-start gap-2"><iconify-icon icon="lucide:check-circle" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span>Profile analytics — see who's viewed you</span></li>
                    @endif
                    <li class="flex items-start gap-2"><iconify-icon icon="lucide:check-circle" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span>Premium badge on your public profile</span></li>
                    <li class="flex items-start gap-2"><iconify-icon icon="lucide:check-circle" class="text-[#1AAD94] mt-0.5"></iconify-icon> <span>Cancel anytime</span></li>
                </ul>
            </div>

            {{-- Pricing & subscribe --}}
            <form method="POST" action="{{ route('candidate.upgrade.subscribe', $plan) }}" class="bg-gradient-to-br from-[#073057] to-[#0a4275] rounded-2xl p-6 text-white shadow-lg flex flex-col">
                @csrf
                <input type="hidden" name="billing_cycle" :value="cycle">

                <div class="inline-flex bg-white/10 rounded-lg p-1 mb-5 self-start">
                    <button type="button" @click="cycle = 'monthly'" :class="cycle === 'monthly' ? 'bg-white text-[#073057]' : 'text-white/70'" class="px-3 py-1.5 rounded-md text-xs font-bold uppercase tracking-widest transition">Monthly</button>
                    <button type="button" @click="cycle = 'annual'" :class="cycle === 'annual' ? 'bg-white text-[#073057]' : 'text-white/70'" class="px-3 py-1.5 rounded-md text-xs font-bold uppercase tracking-widest transition">Annual</button>
                </div>

                @php
                    $monthlyDisplay = money($plan->monthly_price, 'USD');
                    $annualDisplay  = money($plan->annual_price, 'USD');
                    $monthlyEquivDisplay = money(($plan->annual_price ?? 0) / 12, 'USD');
                    $savingsDisplay = money(max(0, ($plan->monthly_price * 12) - $plan->annual_price), 'USD');
                @endphp
                <div x-show="cycle === 'monthly'">
                    <p class="text-5xl font-extrabold">{{ $monthlyDisplay }}<span class="text-lg font-normal text-white/60">/mo</span></p>
                    <p class="text-xs text-white/60 mt-1">Billed monthly · cancel anytime</p>
                </div>
                <div x-show="cycle === 'annual'" x-cloak>
                    <p class="text-5xl font-extrabold">{{ $annualDisplay }}<span class="text-lg font-normal text-white/60">/yr</span></p>
                    <p class="text-xs text-white/60 mt-1">{{ $monthlyEquivDisplay }}/mo equivalent · <span class="text-[#7DE1D1] font-semibold">save {{ $savingsDisplay }}</span></p>
                </div>

                <div class="flex-1"></div>

                <button type="submit" class="mt-6 w-full px-6 py-3.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow-lg inline-flex items-center justify-center gap-2">
                    <iconify-icon icon="lucide:credit-card"></iconify-icon>
                    Subscribe & pay
                </button>
                <p class="mt-3 text-[11px] text-white/50 text-center">Secure checkout · Paystack or bank transfer</p>
            </form>
        </div>
    @endforeach

    <div class="mt-6 bg-[#F9FAFB] border border-gray-200 rounded-xl p-5 text-sm text-[#4B5563]">
        <p class="font-semibold text-[#073057] mb-2 flex items-center gap-2">
            <iconify-icon icon="lucide:info"></iconify-icon>
            Premium vs one-off boost
        </p>
        <p class="text-xs">
            <strong>Premium</strong> keeps you always-featured for as long as you're subscribed, plus extra perks. A
            <a href="{{ route('candidate.boost.index') }}" class="text-[#1AAD94] font-semibold">one-off boost</a>
            features you for a fixed number of days — perfect if you're not ready to commit to a subscription.
        </p>
    </div>
@endif
@endsection
