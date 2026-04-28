@extends('admin.layouts.app')

@section('title', 'Social Media')
@section('page-title', 'Social Media')

@php
    $shareTwitter  = filter_var($social['social.share.twitter_enabled']  ?? '1', FILTER_VALIDATE_BOOLEAN);
    $shareLinkedin = filter_var($social['social.share.linkedin_enabled'] ?? '1', FILTER_VALIDATE_BOOLEAN);
    $shareFacebook = filter_var($social['social.share.facebook_enabled'] ?? '1', FILTER_VALIDATE_BOOLEAN);
    $shareCopy     = filter_var($social['social.share.copy_enabled']     ?? '1', FILTER_VALIDATE_BOOLEAN);
    $twitterHandle = $social['social.share.twitter_handle'] ?? '';
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1929]">Social Media</h1>
        <p class="text-sm text-gray-500 mt-1">Control share buttons on news articles and your company's social profile links.</p>
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

    <form method="POST" action="{{ route('admin.social.update') }}" class="space-y-6 max-w-4xl">
        @csrf

        {{-- Share buttons section --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-br from-[#073057] to-[#0a4275] text-white">
                <h2 class="text-base font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 010-2.684m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm-9.032-4.026a9.001 9.001 0 000 2.684m0 0a3 3 0 11-5.368 2.684 3 3 0 015.368-2.684z"/></svg>
                    Article share buttons
                </h2>
                <p class="text-xs text-white/70 mt-0.5">Control which buttons appear on news articles. The "via @handle" attribution applies to Twitter / X only.</p>
            </div>
            <div class="p-6 space-y-5">

                {{-- Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php
                        $toggles = [
                            ['key' => 'share_twitter_enabled',  'label' => 'Twitter / X', 'enabled' => $shareTwitter,  'color' => '#1DA1F2', 'icon' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'],
                            ['key' => 'share_linkedin_enabled', 'label' => 'LinkedIn',   'enabled' => $shareLinkedin, 'color' => '#0A66C2', 'icon' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/></svg>'],
                            ['key' => 'share_facebook_enabled', 'label' => 'Facebook',   'enabled' => $shareFacebook, 'color' => '#1877F2', 'icon' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'],
                            ['key' => 'share_copy_enabled',     'label' => 'Copy link',   'enabled' => $shareCopy,     'color' => '#1AAD94', 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/></svg>'],
                        ];
                    @endphp

                    @foreach ($toggles as $t)
                        <label class="flex items-center justify-between gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 has-[:checked]:border-[#1AAD94] has-[:checked]:bg-[#1AAD94]/5 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-9 h-9 rounded-lg flex items-center justify-center text-white shrink-0" style="background-color: {{ $t['color'] }}">
                                    {!! $t['icon'] !!}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#0A1929] text-sm">{{ $t['label'] }}</p>
                                    <p class="text-xs text-gray-500">Show this share button</p>
                                </div>
                            </div>
                            <span class="relative inline-block w-11 h-6 shrink-0">
                                <input type="hidden" name="{{ $t['key'] }}" value="0">
                                <input type="checkbox" name="{{ $t['key'] }}" value="1" {{ $t['enabled'] ? 'checked' : '' }} class="peer sr-only">
                                <span class="block w-full h-full rounded-full bg-gray-300 peer-checked:bg-[#1AAD94] transition"></span>
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition"></span>
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- Twitter handle --}}
                <div class="pt-2 border-t border-gray-100">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Twitter / X handle <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50 text-gray-500 text-sm font-mono">@</span>
                        <input type="text" name="share_twitter_handle" value="{{ old('share_twitter_handle', $twitterHandle) }}" placeholder="joseoceanjobs"
                               class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-r-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">When someone shares an article on Twitter, the tweet will include "via @{{ $twitterHandle ?: 'handle' }}".</p>
                </div>
            </div>
        </div>

        {{-- Company social profiles --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-[#0A1929]">Company social profile links</h2>
                <p class="text-xs text-gray-500 mt-0.5">URLs to your company's official social media accounts. Used in footer, contact page, and elsewhere when displayed.</p>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $profiles = [
                        ['key' => 'profile_twitter_url',   'label' => 'Twitter / X',  'value' => $social['social.profile.twitter_url']   ?? '', 'placeholder' => 'https://x.com/yourcompany'],
                        ['key' => 'profile_linkedin_url',  'label' => 'LinkedIn',     'value' => $social['social.profile.linkedin_url']  ?? '', 'placeholder' => 'https://linkedin.com/company/yourcompany'],
                        ['key' => 'profile_facebook_url',  'label' => 'Facebook',     'value' => $social['social.profile.facebook_url']  ?? '', 'placeholder' => 'https://facebook.com/yourcompany'],
                        ['key' => 'profile_instagram_url', 'label' => 'Instagram',    'value' => $social['social.profile.instagram_url'] ?? '', 'placeholder' => 'https://instagram.com/yourcompany'],
                        ['key' => 'profile_youtube_url',   'label' => 'YouTube',      'value' => $social['social.profile.youtube_url']   ?? '', 'placeholder' => 'https://youtube.com/@yourcompany'],
                    ];
                @endphp
                @foreach ($profiles as $p)
                    <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] items-center gap-3">
                        <label class="text-sm font-semibold text-[#0A1929]">{{ $p['label'] }}</label>
                        <input type="url" name="{{ $p['key'] }}" value="{{ old($p['key'], $p['value']) }}"
                               placeholder="{{ $p['placeholder'] }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                    </div>
                @endforeach
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
