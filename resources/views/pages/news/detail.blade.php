@extends('layouts.app')

@section('title', ($article['title'] ?? 'News Detail').' — JOSEOCEANJOBS')

@php
    $title       = $article['title'] ?? 'News Detail';
    $excerpt     = $article['excerpt'] ?? '';
    $author      = $article['author'] ?? 'JCL Editorial';
    $date        = $article['date'] ?? '';
    $category    = $article['category'] ?? 'News';
    $imageUrl    = $article['image_url'] ?? null;
    $content     = $article['content'] ?? [];
    $readMinutes = $article['read_minutes'] ?? max(1, (int) ceil(str_word_count(implode(' ', $content)) / 200));
    $shareUrl    = url()->current();
    $authorInitial = strtoupper(mb_substr(trim($author) ?: 'E', 0, 1));

    $social = $social ?? [];
    $shareTwitter  = filter_var($social['social.share.twitter_enabled']  ?? '1', FILTER_VALIDATE_BOOLEAN);
    $shareLinkedin = filter_var($social['social.share.linkedin_enabled'] ?? '1', FILTER_VALIDATE_BOOLEAN);
    $shareFacebook = filter_var($social['social.share.facebook_enabled'] ?? '1', FILTER_VALIDATE_BOOLEAN);
    $shareCopy     = filter_var($social['social.share.copy_enabled']     ?? '1', FILTER_VALIDATE_BOOLEAN);
    $twitterHandle = trim((string) ($social['social.share.twitter_handle'] ?? ''));
    $shareTwitterUrl = 'https://twitter.com/intent/tweet?text=' . rawurlencode($title)
        . '&url=' . rawurlencode($shareUrl)
        . ($twitterHandle ? '&via=' . rawurlencode($twitterHandle) : '');
@endphp

@push('styles')
<style>
    .article-prose p { font-size: 1.0625rem; line-height: 1.85; color: #2C2C2C; }
    .article-prose p + p { margin-top: 1.35rem; }
    .article-prose p:first-of-type::first-letter {
        float: left;
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        font-size: 4.25rem;
        line-height: 0.9;
        padding: 0.45rem 0.75rem 0 0;
        color: #073057;
    }
    @media (max-width: 640px) {
        .article-prose p:first-of-type::first-letter { font-size: 3rem; padding-right: 0.5rem; }
    }
    .reading-bar { transform-origin: left; transition: transform 0.1s linear; }
</style>
@endpush

@section('content')

{{-- Reading progress bar --}}
<div x-data="{ progress: 0 }"
     x-init="
        const update = () => {
            const h = document.documentElement;
            const total = h.scrollHeight - h.clientHeight;
            progress = total > 0 ? Math.min(1, Math.max(0, h.scrollTop / total)) : 0;
        };
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
     "
     class="fixed top-0 left-0 right-0 z-40 h-1 bg-transparent">
    <div class="reading-bar h-full bg-[#1AAD94]" :style="`transform: scaleX(${progress})`"></div>
</div>

{{-- Hero --}}
<section class="relative bg-[#073057] text-white overflow-hidden">
    @if ($imageUrl)
        <div class="absolute inset-0">
            <img src="{{ $imageUrl }}" alt="{{ $title }}" class="w-full h-full object-cover opacity-30" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-b from-[#073057]/70 via-[#073057]/85 to-[#073057]"></div>
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]/20"></div>
    @endif

    <div class="relative container mx-auto px-6 py-14 md:py-20 max-w-5xl">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-white/70 mb-6 flex flex-wrap items-center gap-1.5">
            <a href="{{ url('/') }}" class="hover:text-white">Home</a>
            <span>/</span>
            <a href="{{ route('news.index') }}" class="hover:text-white">News &amp; Insights</a>
            <span>/</span>
            <span class="text-white/90 truncate max-w-[40ch]">{{ $title }}</span>
        </nav>

        {{-- Category pill --}}
        <div class="mb-5">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1AAD94] text-white text-[11px] font-bold uppercase tracking-widest shadow-lg">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                {{ $category }}
            </span>
        </div>

        {{-- Title --}}
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05] tracking-tight max-w-4xl mb-6">
            {{ $title }}
        </h1>

        {{-- Excerpt --}}
        @if ($excerpt)
            <p class="text-lg md:text-xl text-white/80 leading-relaxed max-w-3xl mb-8">{{ $excerpt }}</p>
        @endif

        {{-- Meta row --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 pt-6 border-t border-white/15">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#1AAD94] to-[#073057] flex items-center justify-center font-bold text-sm shadow-lg ring-2 ring-white/10">
                    {{ $authorInitial }}
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-white">{{ $author }}</p>
                    <p class="text-xs text-white/60">JCL Editorial Team</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-sm text-white/80">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $date }}
            </div>
            <div class="flex items-center gap-1.5 text-sm text-white/80">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $readMinutes }} min read
            </div>
        </div>
    </div>
</section>

{{-- Article body --}}
<section class="bg-[#F9FAFB] py-12 md:py-16">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-[80px_minmax(0,1fr)_300px] gap-8 max-w-6xl mx-auto">

            {{-- Share rail (desktop) --}}
            <aside class="hidden lg:block {{ ($shareTwitter || $shareLinkedin || $shareFacebook || $shareCopy) ? '' : 'lg:invisible' }}">
                <div class="sticky top-24"
                     x-data="{
                        url: @js($shareUrl),
                        title: @js($title),
                        copied: false,
                        copy() {
                            navigator.clipboard.writeText(this.url).then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        }
                     }">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3 text-center">Share</p>
                    <div class="flex flex-col gap-2">
                        @if ($shareTwitter)
                            <a href="{{ $shareTwitterUrl }}" target="_blank" rel="noopener"
                               class="flex items-center justify-center w-11 h-11 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-[#1DA1F2] hover:border-[#1DA1F2] hover:shadow-md transition" title="Share on X / Twitter">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        @endif
                        @if ($shareLinkedin)
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
                               class="flex items-center justify-center w-11 h-11 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-[#0A66C2] hover:border-[#0A66C2] hover:shadow-md transition" title="Share on LinkedIn">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                        @endif
                        @if ($shareFacebook)
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
                               class="flex items-center justify-center w-11 h-11 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-[#1877F2] hover:border-[#1877F2] hover:shadow-md transition" title="Share on Facebook">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if ($shareCopy)
                            <button type="button" @click="copy()"
                                    class="flex items-center justify-center w-11 h-11 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-[#1AAD94] hover:border-[#1AAD94] hover:shadow-md transition relative" title="Copy link">
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <svg x-show="copied" x-cloak class="w-4 h-4 text-[#1AAD94]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span x-show="copied" x-cloak class="absolute left-12 px-2 py-1 rounded bg-gray-900 text-white text-xs whitespace-nowrap">Copied!</span>
                            </button>
                        @endif
                    </div>
                </div>
            </aside>

            {{-- Article --}}
            <article class="bg-white rounded-2xl border border-[#E0E0E0] shadow-sm overflow-hidden">
                <div class="px-6 sm:px-10 lg:px-14 py-10 lg:py-14">

                    {{-- Mobile share bar --}}
                    @if ($shareTwitter || $shareLinkedin || $shareFacebook)
                        <div class="lg:hidden flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
                            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400">Share article</span>
                            <div class="flex gap-2">
                                @if ($shareTwitter)
                                    <a href="{{ $shareTwitterUrl }}" target="_blank" rel="noopener"
                                       class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 hover:bg-[#1DA1F2] hover:text-white transition">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </a>
                                @endif
                                @if ($shareLinkedin)
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
                                       class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 hover:bg-[#0A66C2] hover:text-white transition">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/></svg>
                                    </a>
                                @endif
                                @if ($shareFacebook)
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
                                       class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 hover:bg-[#1877F2] hover:text-white transition">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="article-prose max-w-2xl mx-auto">
                        @forelse ($content as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @empty
                            <p class="text-gray-400 italic">This article has no content yet.</p>
                        @endforelse
                    </div>

                    {{-- End-of-article divider --}}
                    <div class="max-w-2xl mx-auto mt-12 pt-8 border-t border-gray-100">
                        <div class="flex flex-wrap items-center gap-2 mb-8">
                            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 mr-2">Tagged</span>
                            <a href="{{ route('news.index') }}?category={{ urlencode($category) }}" class="px-3 py-1 rounded-full bg-gray-100 hover:bg-[#1AAD94]/10 hover:text-[#1AAD94] text-xs font-semibold text-gray-600 transition">{{ $category }}</a>
                            <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600">Maritime</span>
                            <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600">Industry News</span>
                        </div>

                        {{-- Author byline --}}
                        <div class="bg-gradient-to-br from-[#F9FAFB] to-white border border-gray-100 rounded-xl p-5 sm:p-6 flex flex-wrap sm:flex-nowrap items-start gap-5">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#1AAD94] to-[#073057] flex items-center justify-center font-extrabold text-white text-xl shrink-0 shadow-lg">
                                {{ $authorInitial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold mb-1">Written by</p>
                                <h4 class="text-lg font-bold text-[#073057] mb-1">{{ $author }}</h4>
                                <p class="text-sm text-[#4B5563] leading-relaxed">Editorial team covering maritime hiring trends, offshore workforce policy, and certification updates relevant to JOSEOCEANJOBS candidates and employers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Right sidebar --}}
            <aside class="space-y-5">
                <div class="sticky top-24 space-y-5">
                    {{-- Job CTA --}}
                    <div class="bg-gradient-to-br from-[#073057] to-[#0a4275] text-white rounded-2xl p-6 shadow-lg overflow-hidden relative">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#1AAD94]/10 rounded-full blur-3xl"></div>
                        <div class="relative">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-[#1AAD94]/20 text-[#1AAD94] mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="font-bold text-base mb-1">Looking for your next role?</h4>
                            <p class="text-sm text-white/70 mb-4">Browse open maritime, offshore, and energy positions matched to your certifications.</p>
                            <a href="{{ route('job.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#1AAD94] hover:text-white transition">
                                Browse jobs
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Newsletter --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-6"
                         x-data="{
                            email: '',
                            loading: false,
                            success: '',
                            error: '',
                            async submit() {
                                if (!this.email) return;
                                this.loading = true; this.error = ''; this.success = '';
                                try {
                                    const res = await fetch('{{ route('newsletter.subscribe') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ email: this.email, source: 'news_detail' })
                                    });
                                    const data = await res.json().catch(() => ({}));
                                    if (!res.ok) {
                                        this.error = data?.errors?.email?.[0] || data?.message || 'Could not subscribe right now. Please try again.';
                                    } else {
                                        this.success = data.message || 'Thanks for subscribing!';
                                        this.email = '';
                                    }
                                } catch (e) {
                                    this.error = 'Network error. Please try again.';
                                } finally {
                                    this.loading = false;
                                }
                            }
                         }">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-[#1AAD94]/10 text-[#1AAD94] mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-bold text-[#073057] mb-1">Industry insights, monthly</h4>
                        <p class="text-sm text-[#6B7280] mb-4">Get the best of JCL — hiring trends, certification news, and policy updates — delivered to your inbox.</p>

                        <template x-if="success">
                            <div class="rounded-lg bg-green-50 border border-green-200 px-3 py-2.5 text-xs text-green-700 mb-3" x-text="success"></div>
                        </template>
                        <template x-if="error">
                            <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2.5 text-xs text-red-700 mb-3" x-text="error"></div>
                        </template>

                        <form @submit.prevent="submit()" class="space-y-2">
                            <input type="email" x-model="email" required placeholder="you@company.com"
                                   :disabled="loading"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent outline-none disabled:opacity-50">
                            <button type="submit" :disabled="loading || !email"
                                    class="w-full px-4 py-2.5 bg-[#073057] text-white text-sm font-bold rounded-lg hover:brightness-110 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="loading" x-cloak class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span x-text="loading ? 'Subscribing...' : 'Subscribe'"></span>
                            </button>
                        </form>
                        <p class="mt-2 text-[11px] text-gray-400">No spam. Unsubscribe anytime.</p>
                    </div>

                    {{-- Back link --}}
                    <a href="{{ route('news.index') }}"
                       class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-semibold text-[#073057] hover:border-[#1AAD94] hover:text-[#1AAD94] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        All articles
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- Related articles --}}
@if (! empty($related))
    <section class="bg-white py-14 md:py-20 border-t border-gray-100">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-[#1AAD94] mb-2">Keep reading</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#073057]">Related articles</h2>
                </div>
                <a href="{{ route('news.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-[#073057] hover:text-[#1AAD94]">
                    Browse all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($related as $rel)
                    <x-ui.news-card :article="$rel" />
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
