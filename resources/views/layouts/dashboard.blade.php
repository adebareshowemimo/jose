<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — JOSEOCEANJOBS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('images/favicon-16x16.png') }}" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
<body class="bg-[#F3F4F6] min-h-screen" x-data="{ sidebarOpen: false }">
    
    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 h-16 bg-[#073057] border-b border-white/10 z-50 lg:pl-64">
        <div class="flex items-center justify-between h-full px-4 lg:px-6">
            {{-- Mobile menu toggle --}}
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-white/80 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Mobile logo --}}
            <div class="lg:hidden">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/dark_logo.png') }}" alt="JCL Logo" class="h-8 w-auto"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="hidden text-base font-extrabold tracking-tight text-white uppercase">JOSEOCEANJOBS</span>
                </a>
            </div>

            {{-- Page title (desktop) --}}
            <div class="hidden lg:block">
                <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
            </div>

            {{-- Header actions --}}
            @php
                $authUser = auth()->user();
                $authRole = $authUser?->role?->name;

                $notificationsRoute = match ($authRole) {
                    'employer' => 'employer.notifications',
                    'admin' => null,
                    default => 'user.notifications',
                };
                $unreadNotifications = $authUser ? $authUser->unreadNotifications()->count() : 0;

                $chatRoute = match ($authRole) {
                    'employer' => 'employer.chat',
                    'admin' => 'admin.chat.index',
                    'candidate' => 'user.chat',
                    default => null,
                };

                $unreadMessages = 0;
                if ($authUser) {
                    $unreadMessages = \App\Models\ChatMessage::query()
                        ->whereNull('read_at')
                        ->where('sender_user_id', '!=', $authUser->id)
                        ->whereHas('conversation', function ($q) use ($authUser, $authRole) {
                            if ($authRole === 'employer' && $authUser->company) {
                                $q->where('type', \App\Models\ChatConversation::TYPE_EMPLOYER_CANDIDATE)
                                  ->where('company_id', $authUser->company->id);
                            } elseif ($authRole === 'candidate' && $authUser->candidate) {
                                $q->where('candidate_id', $authUser->candidate->id);
                            } elseif ($authRole === 'admin') {
                                $q->where('type', \App\Models\ChatConversation::TYPE_ADMIN_CANDIDATE);
                            } else {
                                $q->whereRaw('1 = 0');
                            }
                        })
                        ->count();
                }
            @endphp
            <div class="flex items-center gap-2">
                {{-- Notifications --}}
                @if($notificationsRoute)
                    <a href="{{ route($notificationsRoute) }}" title="Notifications" class="relative p-2 text-white/75 hover:text-white hover:bg-white/10 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadNotifications > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full ring-2 ring-[#073057] flex items-center justify-center">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
                        @endif
                    </a>
                @endif

                {{-- Messages --}}
                @if($chatRoute)
                    <a href="{{ route($chatRoute) }}" title="Messages" class="relative p-2 text-white/75 hover:text-white hover:bg-white/10 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        @if($unreadMessages > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-[#1AAD94] text-white text-[10px] font-bold rounded-full ring-2 ring-[#073057] flex items-center justify-center">{{ $unreadMessages > 9 ? '9+' : $unreadMessages }}</span>
                        @endif
                    </a>
                @endif

                {{-- User dropdown --}}
                <div class="relative ml-2" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 hover:bg-white/10 rounded-lg transition">
                        <div class="w-8 h-8 bg-[#1AAD94] rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <svg class="w-4 h-4 text-white/75 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-[#E5E7EB] py-2 z-50">
                        <div class="px-4 py-2 border-b border-[#E5E7EB] min-w-0">
                            <p class="text-sm font-semibold text-[#073057] truncate" title="{{ auth()->user()->name ?? '' }}">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-[#6B7280] truncate" title="{{ auth()->user()->email ?? '' }}">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        </div>
                        <a href="{{ url('/') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[#374151] hover:bg-[#F3F4F6]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            View Site
                        </a>
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-[#374151] hover:bg-[#F3F4F6]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Settings
                        </a>
                        <hr class="my-2 border-[#E5E7EB]">
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

    {{-- Sidebar Backdrop (mobile) --}}
    <div class="sidebar-backdrop" :class="{ 'active': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 w-64 h-full bg-white border-r border-[#E5E7EB] z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : ''">
        
        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 bg-[#073057] border-b border-white/10">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/dark_logo.png') }}" alt="JCL Logo" class="h-8 w-auto"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span class="hidden text-base font-extrabold tracking-tight text-white uppercase">JOSEOCEANJOBS</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden p-1 text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="p-4 space-y-1 overflow-y-auto" style="height: calc(100vh - 4rem);">
            @yield('sidebar-nav')
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="lg:pl-64 pt-16 min-h-screen">
        <div class="p-4 lg:p-6">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
