@extends('layouts.app')

@section('title', 'Register · ' . $event->title)

@section('content')
@php
    $isPaid = $event->isPaid();
    $isFree = $event->isFreeInternal();
    $maxTickets = min($event->seatsRemaining() ?? 10, 10);
    $maxTickets = max(1, $maxTickets);
@endphp

<section class="bg-[#F9FAFB] py-12 md:py-16 min-h-[70vh]">
    <div class="container mx-auto px-6 max-w-5xl">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#6B7280]" />

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest text-[#1AAD94] mb-2">Event registration</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#073057]">{{ $event->title }}</h1>
            <p class="text-[#6B7280] mt-2">{{ $event->display_date }} · {{ $event->location }}</p>
        </div>

        @if (session('error'))
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[minmax(0,1fr)_320px] gap-6">

            {{-- Form --}}
            <form method="POST" action="{{ route('events.register.submit', $event) }}"
                  class="bg-white rounded-2xl border border-[#E0E0E0] shadow-sm p-6 md:p-8 space-y-5"
                  x-data="{ tickets: {{ (int) old('ticket_count', 1) }}, unit: {{ (float) ($event->price ?? 0) }} }">
                @csrf

                <div>
                    <h2 class="text-base font-extrabold text-[#073057] mb-3 pb-2 border-b border-gray-100">Your details</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Full name <span class="text-red-500">*</span></label>
                            <input type="text" name="buyer_name" value="{{ $prefill['buyer_name'] }}" required
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="buyer_email" value="{{ $prefill['buyer_email'] }}" required
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Phone <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                            <input type="tel" name="buyer_phone" value="{{ $prefill['buyer_phone'] }}"
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-base font-extrabold text-[#073057] mb-3 pb-2 border-b border-gray-100">Tickets</h2>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="if (tickets > 1) tickets--" class="w-9 h-9 rounded-lg border border-gray-300 hover:border-[#1AAD94] text-lg font-bold text-[#073057] disabled:opacity-50" :disabled="tickets <= 1">−</button>
                        <input type="number" name="ticket_count" x-model.number="tickets" min="1" max="{{ $maxTickets }}" required
                               class="w-20 text-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-bold focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        <button type="button" @click="if (tickets < {{ $maxTickets }}) tickets++" class="w-9 h-9 rounded-lg border border-gray-300 hover:border-[#1AAD94] text-lg font-bold text-[#073057] disabled:opacity-50" :disabled="tickets >= {{ $maxTickets }}">+</button>
                        @if ($event->seatsRemaining() !== null)
                            <span class="text-xs text-[#6B7280] ml-2">{{ $event->seatsRemaining() }} seats remaining</span>
                        @endif
                    </div>
                </div>

                @if (! empty($event->questions))
                    <div>
                        <h2 class="text-base font-extrabold text-[#073057] mb-3 pb-2 border-b border-gray-100">A few extra details</h2>
                        <div class="space-y-4">
                            @foreach ($event->questions as $q)
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">
                                        {{ $q['label'] }}
                                        @if ($q['required'] ?? false)<span class="text-red-500">*</span>@endif
                                    </label>
                                    @if (($q['type'] ?? 'text') === 'textarea')
                                        <textarea name="answers[{{ $q['id'] }}]" rows="3" {{ ($q['required'] ?? false) ? 'required' : '' }}
                                                  class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old("answers.{$q['id']}") }}</textarea>
                                    @elseif (($q['type'] ?? 'text') === 'select')
                                        <select name="answers[{{ $q['id'] }}]" {{ ($q['required'] ?? false) ? 'required' : '' }}
                                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                                            <option value="">— Select —</option>
                                            @foreach (($q['options'] ?? []) as $opt)
                                                <option value="{{ $opt }}" {{ old("answers.{$q['id']}") === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="answers[{{ $q['id'] }}]" value="{{ old("answers.{$q['id']}") }}" {{ ($q['required'] ?? false) ? 'required' : '' }}
                                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-3 border-t border-gray-100">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-xl transition shadow-lg">
                        @if ($isPaid)
                            <iconify-icon icon="lucide:credit-card"></iconify-icon>
                            Continue to payment · <span x-text="(unit * tickets).toFixed(2)"></span> {{ $event->currency ?? 'USD' }}
                        @else
                            <iconify-icon icon="lucide:check"></iconify-icon>
                            Reserve my spot
                        @endif
                    </button>
                    @if ($isPaid)
                        <p class="mt-2 text-xs text-gray-400 text-center">Secure checkout · Paystack or bank transfer</p>
                    @endif
                </div>
            </form>

            {{-- Sticky summary --}}
            <aside>
                <div class="sticky top-24 space-y-4">
                    <div class="bg-white rounded-2xl border border-[#E0E0E0] shadow-sm overflow-hidden"
                         x-data="{ tickets: {{ (int) old('ticket_count', 1) }}, unit: {{ (float) ($event->price ?? 0) }} }">
                        @if ($event->image_url)
                            <img src="{{ $event->image_url }}" alt="" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gradient-to-br from-[#073057] to-[#1AAD94] flex items-center justify-center text-white px-4 text-center text-sm font-bold leading-tight">{{ $event->title }}</div>
                        @endif
                        <div class="p-5 space-y-3">
                            <h3 class="font-bold text-[#073057] leading-snug">{{ $event->title }}</h3>
                            <div class="text-xs text-[#6B7280] space-y-1">
                                <p class="flex items-center gap-1.5"><iconify-icon icon="lucide:calendar" class="text-[#1AAD94]"></iconify-icon> {{ $event->display_date }}</p>
                                <p class="flex items-center gap-1.5"><iconify-icon icon="lucide:map-pin" class="text-[#1AAD94]"></iconify-icon> {{ $event->location }}</p>
                            </div>

                            @if ($isPaid)
                                <div class="pt-3 border-t border-gray-100 space-y-1.5 text-sm">
                                    <div class="flex justify-between text-[#6B7280]">
                                        <span><span x-text="tickets"></span> × {{ $event->currency }} {{ number_format((float) $event->price, 2) }}</span>
                                        <span class="font-medium text-[#073057]" x-text="(unit * tickets).toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between font-extrabold text-[#073057] pt-2 border-t border-gray-100">
                                        <span>Total</span>
                                        <span>{{ $event->currency }} <span x-text="(unit * tickets).toFixed(2)"></span></span>
                                    </div>
                                </div>
                            @else
                                <div class="pt-3 border-t border-gray-100">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase tracking-wider">Free RSVP</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('events.index') }}" class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-semibold text-[#073057] hover:border-[#1AAD94] hover:text-[#1AAD94] transition">
                        <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                        Back to events
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
