<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — JOSEOCEANJOBS Admin</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('images/favicon-16x16.png') }}" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        button:not(:disabled), [type="button"]:not(:disabled), [type="submit"]:not(:disabled), [type="reset"]:not(:disabled), [role="button"], a[href], label[for], label:has(input[type="checkbox"]), label:has(input[type="radio"]), summary, select, input[type="checkbox"], input[type="radio"], input[type="file"] { cursor: pointer; }
        button:disabled, [type="button"]:disabled, [type="submit"]:disabled, [type="reset"]:disabled { cursor: not-allowed; }
        .sidebar-backdrop { display: none; }
        @media (max-width: 1023px) {
            .sidebar-backdrop.active { display: block; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 h-16 bg-[#073057] z-50 lg:pl-64">
        <div class="flex items-center justify-between h-full px-4 lg:px-6">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-white/70 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="lg:hidden">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/dark_logo.png') }}" alt="JCL Logo" class="h-8 w-auto"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                    <span class="hidden text-lg font-bold text-white">JOSE<span class="text-[#1AAD94]">OCEAN</span>JOBS</span>
                </a>
            </div>
            <div class="hidden lg:block">
                <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Admin Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-sm text-white/70 hover:text-white flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Site
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 hover:bg-white/10 rounded-lg transition">
                        <div class="w-8 h-8 bg-[#1AAD94] rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="hidden sm:block text-sm text-white/90">{{ auth()->user()->name ?? 'Admin' }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Sidebar Backdrop --}}
    <div class="sidebar-backdrop" :class="{ 'active': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 w-64 h-full bg-[#0A1929] z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : ''">

        <div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 min-w-0">
                <img src="{{ asset('images/dark_logo.png') }}" alt="JCL Logo" class="h-8 w-auto shrink-0"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span class="hidden text-base font-bold text-white truncate">
                    JOSE<span class="text-[#1AAD94]">OCEAN</span>JOBS
                    <span class="block text-[10px] tracking-widest text-white/40 uppercase">Administration</span>
                </span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden p-1 text-white/50 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="p-4 space-y-1 overflow-y-auto" style="height: calc(100vh - 4rem);">

            @php $current = request()->route()->getName(); @endphp

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ $current === 'admin.dashboard' ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/30 uppercase tracking-wider">People</p>

            {{-- Users --}}
            <a href="{{ route('admin.users') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.users') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Users
            </a>

            {{-- Companies --}}
            <a href="{{ route('admin.companies') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.companies') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Companies
            </a>

            {{-- Applications --}}
            <a href="{{ route('admin.applications') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.applications') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Applications
            </a>

            {{-- Chat --}}
            <a href="{{ route('admin.chat.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.chat') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.82L3 20l1.3-3.25A7.33 7.33 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
            </a>

            {{-- Recruitment Requests --}}
            <a href="{{ route('admin.recruitment-requests.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.recruitment-requests') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Recruitment Requests
            </a>

            {{-- Contact Submissions --}}
            <a href="{{ route('admin.contacts.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.contacts') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.82L3 20l1.3-3.25A7.33 7.33 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Contacts
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/30 uppercase tracking-wider">Jobs</p>

            {{-- Job Listings --}}
            <a href="{{ route('admin.jobs') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.jobs') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Listings
            </a>

            {{-- Job Categories --}}
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.categories') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 11l8.5 8.5a2.121 2.121 0 003 0L21 13V3h-10L3 11z"/></svg>
                Job Categories
            </a>

            {{-- Job Types --}}
            <a href="{{ route('admin.job-types.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.job-types') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7"/></svg>
                Job Types
            </a>

            {{-- Countries --}}
            <a href="{{ route('admin.locations.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.locations') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/></svg>
                Countries
            </a>

            {{-- Events --}}
            <a href="{{ route('admin.events.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.events') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Events
            </a>

            {{-- News --}}
            <a href="{{ route('admin.news.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.news') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2zM14 4v6h6M8 13h8M8 17h8M8 9h2"/></svg>
                News
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/30 uppercase tracking-wider">Finance</p>

            {{-- Orders --}}
            <a href="{{ route('admin.orders') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.orders') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Orders
            </a>

            {{-- Payments --}}
            <a href="{{ route('admin.payments') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.payments') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Payments
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/30 uppercase tracking-wider">Monetization</p>

            {{-- Training --}}
            <a href="{{ route('admin.training.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.training') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                Training
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/30 uppercase tracking-wider">System</p>

            {{-- Email Templates --}}
            <a href="{{ route('admin.email-templates.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.email-templates') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Email Templates
            </a>

            {{-- Newsletter --}}
            <a href="{{ route('admin.newsletter.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.newsletter') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Newsletter
            </a>

            {{-- Social Media --}}
            <a href="{{ route('admin.social.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.social') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 010-2.684m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm-9.032-4.026a9.001 9.001 0 000 2.684m0 0a3 3 0 11-5.368 2.684 3 3 0 015.368-2.684z"/></svg>
                Social Media
            </a>

            {{-- Settings --}}
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
               {{ str_starts_with($current, 'admin.settings') ? 'bg-[#1AAD94] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="lg:pl-64 pt-16 min-h-screen">
        <div class="p-4 lg:p-6">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 flex items-center gap-2 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm" x-data="{ show: true }" x-show="show">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 flex items-center gap-2 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm" x-data="{ show: true }" x-show="show">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                    <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
