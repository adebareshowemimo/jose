@extends('layouts.app')

@section('title', 'Unsubscribe — JOSEOCEANJOBS')

@section('content')
<section class="bg-[#F9FAFB] py-16 md:py-24 min-h-[70vh]">
    <div class="container mx-auto px-6 max-w-xl">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        @if (session('newsletter_success'))
            <div class="bg-white border border-[#E0E0E0] rounded-2xl p-8 md:p-10 text-center shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-700 mb-5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#073057] mb-3">You've been unsubscribed</h1>
                <p class="text-[#4B5563] leading-relaxed mb-8">{{ session('newsletter_success') }}</p>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#073057] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg transition">
                    Browse our news
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @elseif ($invalid)
            <div class="bg-white border border-[#E0E0E0] rounded-2xl p-8 md:p-10 text-center shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-700 mb-5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#073057] mb-3">Link not recognised</h1>
                <p class="text-[#4B5563] leading-relaxed mb-8">This unsubscribe link isn't valid or has expired. If you'd like to manage your subscription, contact us at <a href="mailto:info@joseoceanjobs.com" class="text-[#1AAD94] font-semibold">info@joseoceanjobs.com</a> and we'll handle it for you.</p>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 hover:border-[#1AAD94] hover:text-[#1AAD94] text-[#073057] text-sm font-bold uppercase tracking-widest rounded-lg transition">
                    Go to homepage
                </a>
            </div>
        @elseif ($alreadyUnsubscribed)
            <div class="bg-white border border-[#E0E0E0] rounded-2xl p-8 md:p-10 text-center shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-500 mb-5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#073057] mb-3">Already unsubscribed</h1>
                <p class="text-[#4B5563] leading-relaxed mb-2">The email <span class="font-semibold text-[#073057]">{{ $subscriber->email }}</span> was already unsubscribed{{ $subscriber->unsubscribed_at ? ' on '.$subscriber->unsubscribed_at->format('M d, Y') : '' }}.</p>
                <p class="text-sm text-[#6B7280] mb-8">You won't receive any newsletter emails from us.</p>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 hover:border-[#1AAD94] hover:text-[#1AAD94] text-[#073057] text-sm font-bold uppercase tracking-widest rounded-lg transition">
                    Browse our news
                </a>
            </div>
        @else
            <div class="bg-white border border-[#E0E0E0] rounded-2xl p-8 md:p-10 shadow-sm">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 text-yellow-700 mb-5">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-[#073057] mb-3">Unsubscribe from our newsletter?</h1>
                    <p class="text-[#4B5563] leading-relaxed">You're about to unsubscribe <span class="font-semibold text-[#073057]">{{ $subscriber->email }}</span> from JOSEOCEANJOBS newsletter emails.</p>
                </div>

                <div class="bg-[#F9FAFB] border border-gray-100 rounded-xl p-5 mb-6">
                    <p class="text-xs uppercase tracking-widest font-semibold text-gray-400 mb-2">You'll stop receiving:</p>
                    <ul class="text-sm text-[#4B5563] space-y-1.5">
                        <li class="flex items-start gap-2"><span class="text-[#1AAD94] mt-0.5">·</span> Monthly maritime hiring trend updates</li>
                        <li class="flex items-start gap-2"><span class="text-[#1AAD94] mt-0.5">·</span> Certification and compliance insights</li>
                        <li class="flex items-start gap-2"><span class="text-[#1AAD94] mt-0.5">·</span> Featured job alerts curated by our editorial team</li>
                    </ul>
                    <p class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-500">
                        Transactional emails (account activity, order receipts, application updates) are <strong>not affected</strong>.
                    </p>
                </div>

                <form method="POST" action="{{ route('newsletter.unsubscribe.confirm', $subscriber->token) }}" class="flex flex-col sm:flex-row-reverse gap-3">
                    @csrf
                    <button type="submit" class="flex-1 px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-bold uppercase tracking-widest rounded-lg transition">
                        Confirm Unsubscribe
                    </button>
                    <a href="{{ route('news.index') }}" class="flex-1 px-5 py-3 border border-gray-300 hover:border-gray-400 text-[#073057] text-sm font-bold uppercase tracking-widest rounded-lg text-center transition">
                        Stay subscribed
                    </a>
                </form>
            </div>
        @endif
    </div>
</section>
@endsection
