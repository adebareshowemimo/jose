@extends('admin.layouts.app')

@section('title', 'Currency')
@section('page-title', 'Currency')

@php
    $defaultCurrency = strtoupper((string) ($currency['currency.default'] ?? \App\Support\Currency::FALLBACK_DEFAULT));
    $usdToNgnRate    = (string) ($currency['currency.usd_to_ngn_rate'] ?? number_format(\App\Support\Currency::FALLBACK_USD_TO_NGN, 2, '.', ''));
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1929]">Currency</h1>
        <p class="text-sm text-gray-500 mt-1">Set the site's default currency and the USD&harr;NGN conversion rate. Prices stored in another currency are converted on display.</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.currency.update') }}" class="space-y-6 max-w-3xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-br from-[#073057] to-[#0a4275] text-white">
                <h2 class="text-base font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Default currency
                </h2>
                <p class="text-xs text-white/70 mt-0.5">All prices on the site are displayed in this currency. Amounts stored in a different currency are converted using the rate below.</p>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Default currency</label>
                    <select name="default" class="w-full sm:w-64 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        @foreach ($allowed as $code)
                            <option value="{{ $code }}" {{ old('default', $defaultCurrency) === $code ? 'selected' : '' }}>
                                {{ $code }} — {{ \App\Support\Currency::symbol($code) }}{{ $code === 'NGN' ? ' Naira' : ' US Dollar' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-gray-400">NGN is the recommended default. Switch to USD only if your audience pays mainly in dollars.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-[#0A1929]">Conversion rate</h2>
                <p class="text-xs text-gray-500 mt-0.5">How many Naira are equivalent to one US Dollar. The reverse (NGN&rarr;USD) is derived automatically.</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-[120px_1fr_120px] items-end gap-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">From</label>
                        <div class="px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm font-bold text-[#073057]">1 USD</div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Rate</label>
                        <input type="number" name="usd_to_ngn_rate" step="0.0001" min="0.0001"
                               value="{{ old('usd_to_ngn_rate', $usdToNgnRate) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Equals</label>
                        <div class="px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm font-bold text-[#073057]">NGN</div>
                    </div>
                </div>

                <div class="rounded-lg bg-[#F9FAFB] border border-gray-100 px-4 py-3 text-xs text-gray-600 leading-relaxed">
                    <p><span class="font-semibold text-[#073057]">Heads up.</span> Conversion happens at display time using the rate currently saved here. New payments completed in a non-default currency also stamp the rate-of-the-moment on the payment record so historical reports stay accurate even if you change this rate later.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#073057] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Save changes
            </button>
        </div>
    </form>
@endsection
