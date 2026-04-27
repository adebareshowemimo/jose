<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seo_meta = $seo_meta ?? [];
        $site_title = setting_item('site_title', 'JOSEOCEANJOBS');
    @endphp

    <title>{{ $seo_meta['title'] ?? $page_title ?? $site_title }}</title>

    @if(!empty($seo_meta['description']))
        <meta name="description" content="{{ $seo_meta['description'] }}">
    @endif
    @if(!empty($seo_meta['keywords']))
        <meta name="keywords" content="{{ $seo_meta['keywords'] }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $seo_meta['title'] ?? $page_title ?? $site_title }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seo_meta['full_url'] ?? request()->url() }}">
    @if(!empty($seo_meta['image']))
        <meta property="og:image" content="{{ $seo_meta['image'] }}">
    @endif

    {{-- Favicon --}}
    @php $favicon = setting_item('site_favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ get_file_url($favicon, 'full') }}">
    @endif

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @yield('head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-white antialiased" x-data="{ mobileMenuOpen: false }">

    {{-- Skip to content --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] btn btn-primary">
        {{ __('Skip to content') }}
    </a>

    {{-- Header --}}
    @include('Layout::parts.header')

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="toast-container">
            <div class="toast toast-success">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="text-color-muted hover:text-color-dark cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="toast-container">
            <div class="toast toast-danger">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="text-color-muted hover:text-color-dark cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('Layout::parts.footer')

    {{-- Login/Register Modal (for guests) --}}
    @if(!Auth::check())
        @include('Layout::parts.login-register-modal')
    @endif

    @stack('scripts')

    {{-- Custom site JS from admin settings --}}
    @php $custom_js = setting_item('custom_js'); @endphp
    @if($custom_js)
        {!! $custom_js !!}
    @endif
</body>
</html>
