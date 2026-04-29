<nav
    class="sticky top-0 z-50 border-b border-white/10 bg-[#073057]/95 backdrop-blur supports-[backdrop-filter]:bg-[#073057]/90"
    x-data="{ mobileMenuOpen: false, aboutOpen: false, jobsOpen: false, careerOpen: false, serviceOpen: false, serviceTrainingOpen: false, mAboutOpen: false, mJobsOpen: false, mCareerOpen: false, mServiceOpen: false, mServiceTrainingOpen: false }"
    @click.outside="aboutOpen = false; jobsOpen = false; careerOpen = false; serviceOpen = false"
>
    <div class="container mx-auto px-6">
        <div class="flex min-h-[84px] items-center justify-between gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline shrink-0">
                <img src="{{ asset('images/dark_logo.png') }}" alt="JCL Logo"
                     class="h-12 w-auto"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                <div class="hidden h-12 w-12 items-center justify-center rounded-full bg-[#1AAD94] shrink-0 shadow-[0_8px_24px_rgba(26,173,148,0.25)]">
                    <span class="text-base font-bold tracking-widest text-white">JCL</span>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-2xl font-extrabold tracking-tighter text-white uppercase">JOSEOCEANJOBS</span>
                    <span class="text-[11px] font-medium uppercase tracking-[0.18em] text-white/50">Powered by Jose Consulting Limited</span>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden xl:flex items-center gap-6">

                {{-- Home --}}
                <a href="{{ route('home') }}" class="text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">Home</a>

                {{-- About dropdown --}}
                <div class="relative" x-data @mouseenter="aboutOpen = true" @mouseleave="aboutOpen = false">
                    <a href="{{ route('about.index') }}"
                       class="flex items-center gap-1 text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">
                        About
                        <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="aboutOpen ? 'rotate-180' : ''"></iconify-icon>
                    </a>
                    <div x-show="aboutOpen" x-transition.origin.top.duration.150ms
                         class="absolute top-full left-0 mt-2 w-52 rounded-[14px] border border-white/10 bg-[#073057] shadow-2xl py-2 z-50" x-cloak>
                        <a href="{{ route('about.index') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">About JCL</a>
                        <a href="{{ route('leadership.index') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Leadership</a>
                        <a href="{{ route('partnerships.index') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Partnership</a>
                    </div>
                </div>

                {{-- Career dropdown --}}
                <div class="relative" @mouseenter="careerOpen = true" @mouseleave="careerOpen = false">
                    <a href="{{ route('career.index') }}"
                       class="flex items-center gap-1 text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">
                        Career
                        <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="careerOpen ? 'rotate-180' : ''"></iconify-icon>
                    </a>
                    <div x-show="careerOpen" x-transition.origin.top.duration.150ms
                         class="absolute top-full left-0 mt-2 w-52 rounded-[14px] border border-white/10 bg-[#073057] shadow-2xl py-2 z-50" x-cloak>
                        <a href="{{ route('career.internship') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Internship</a>
                        <a href="{{ route('career.apprenticeship') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Apprenticeship</a>
                    </div>
                </div>

                {{-- Services dropdown --}}
                <div class="relative" @mouseenter="serviceOpen = true" @mouseleave="serviceOpen = false; serviceTrainingOpen = false">
                    <a href="{{ route('services.index') }}"
                       class="flex items-center gap-1 text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">
                        Services
                        <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="serviceOpen ? 'rotate-180' : ''"></iconify-icon>
                    </a>
                    <div x-show="serviceOpen" x-transition.origin.top.duration.150ms
                         class="absolute top-full left-0 mt-2 w-64 rounded-[14px] border border-white/10 bg-[#073057] shadow-2xl py-2 z-50" x-cloak>
                        <a href="{{ route('job.index') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Jobs</a>
                        {{-- Training nested --}}
                        <div class="relative group/training"
                             @mouseenter="serviceTrainingOpen = true"
                             @mouseleave="serviceTrainingOpen = false">
                            <a href="{{ route('services.training') }}"
                               class="flex items-center justify-between px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">
                                Training
                                <iconify-icon icon="lucide:chevron-right" class="text-xs"></iconify-icon>
                            </a>
                            <div x-show="serviceTrainingOpen" x-transition.origin.left.duration.150ms
                                 class="absolute top-0 left-full ml-1 w-64 rounded-[14px] border border-white/10 bg-[#073057] shadow-2xl py-2 z-50" x-cloak>
                                <a href="{{ route('services.training.soft') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Soft Skills</a>
                                <a href="{{ route('services.training.technical') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Technical &amp; Non-Technical Skills</a>
                            </div>
                        </div>
                        <a href="{{ route('services.crew-management') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Crew Management</a>
                        <a href="{{ route('services.ship-chandelling') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Ship Chandelling</a>
                        <a href="{{ route('services.crew-abandonment') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Crew Abandonment Support</a>
                        <a href="{{ route('services.marine-procurement') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Marine Procurement</a>
                        <a href="{{ route('services.marine-insurance') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Marine Insurance</a>
                        <a href="{{ route('services.travel-management') }}" class="block px-5 py-3 text-[12px] font-semibold uppercase tracking-[0.08em] text-white/75 hover:text-white hover:bg-white/10 transition-colors">Travel Management Service</a>
                    </div>
                </div>

                {{-- Events --}}
                <a href="{{ route('events.index') }}" class="text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">Events</a>

                {{-- News --}}
                <a href="{{ route('news.index') }}" class="text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">News</a>

                {{-- Contact --}}
                <a href="{{ route('contact.index') }}" class="text-[13px] font-semibold uppercase tracking-[0.08em] text-white/80 transition-colors hover:text-[#1AAD94]">Contact</a>
            </div>

            {{-- CTA buttons --}}
            @php
                $dashboardRoute = null;
                if (auth()->check()) {
                    $u = auth()->user();
                    $dashboardRoute = $u->role?->name === 'admin'
                        ? route('admin.dashboard')
                        : (((int) $u->role_id === 2) ? route('employer.dashboard') : ($u->role_id ? route('user.dashboard') : route('auth.complete-signup')));
                }
            @endphp
            <div class="hidden md:flex items-center gap-3 shrink-0">
                @auth
                    <a href="{{ $dashboardRoute }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-white/20 rounded-[8px] text-white text-[12px] font-semibold uppercase tracking-[0.08em] hover:bg-white/10 transition-all">
                        <iconify-icon icon="lucide:layout-dashboard" class="text-base"></iconify-icon>
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('auth.logout') }}" class="inline-flex">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] rounded-[8px] text-white text-[12px] font-bold uppercase tracking-[0.08em] hover:brightness-110 transition-all">
                            <iconify-icon icon="lucide:log-out" class="text-base"></iconify-icon>
                            Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}" class="inline-flex px-5 py-2.5 border border-white/20 rounded-[8px] text-white text-[12px] font-semibold uppercase tracking-[0.08em] hover:bg-white/10 transition-all">Sign In</a>
                    <a href="{{ route('auth.register') }}" class="inline-flex px-5 py-2.5 bg-[#1AAD94] rounded-[8px] text-white text-[12px] font-bold uppercase tracking-[0.08em] hover:brightness-110 transition-all">Register</a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button
                type="button"
                class="inline-flex xl:hidden items-center justify-center w-11 h-11 p-2.5 rounded-full border border-white/15 text-white hover:bg-white/10 transition-colors"
                @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen.toString()"
                aria-label="Toggle navigation"
            >
                <iconify-icon icon="lucide:menu" width="20" x-show="!mobileMenuOpen"></iconify-icon>
                <iconify-icon icon="lucide:x" width="20" x-show="mobileMenuOpen" x-cloak></iconify-icon>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-transition.origin.top.duration.200ms class="xl:hidden pb-5" x-cloak>
            <div class="rounded-[18px] border border-white/10 bg-white/5 p-4 shadow-2xl">
                <div class="space-y-1">

                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false"
                       class="block rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">Home</a>

                    {{-- About --}}
                    <div>
                        <button @click="mAboutOpen = !mAboutOpen"
                                class="w-full flex items-center justify-between rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">
                            About
                            <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="mAboutOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <div x-show="mAboutOpen" x-transition class="pl-4 space-y-1 mt-1" x-cloak>
                            <a href="{{ route('about.index') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">About JCL</a>
                            <a href="{{ route('leadership.index') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Leadership</a>
                            <a href="{{ route('partnerships.index') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Partnership</a>
                        </div>
                    </div>

                    {{-- Career --}}
                    <div>
                        <button @click="mCareerOpen = !mCareerOpen"
                                class="w-full flex items-center justify-between rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">
                            Career
                            <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="mCareerOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <div x-show="mCareerOpen" x-transition class="pl-4 space-y-1 mt-1" x-cloak>
                            <a href="{{ route('career.internship') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Internship</a>
                            <a href="{{ route('career.apprenticeship') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Apprenticeship</a>
                        </div>
                    </div>

                    {{-- Services --}}
                    <div>
                        <button @click="mServiceOpen = !mServiceOpen"
                                class="w-full flex items-center justify-between rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">
                            Services
                            <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="mServiceOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <div x-show="mServiceOpen" x-transition class="pl-4 space-y-1 mt-1" x-cloak>
                            <a href="{{ route('job.index') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Jobs</a>
                            {{-- Training nested --}}
                            <div>
                                <button @click="mServiceTrainingOpen = !mServiceTrainingOpen"
                                        class="w-full flex items-center justify-between rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                                    Training
                                    <iconify-icon icon="lucide:chevron-down" class="text-xs transition-transform duration-150" :class="mServiceTrainingOpen ? 'rotate-180' : ''"></iconify-icon>
                                </button>
                                <div x-show="mServiceTrainingOpen" x-transition class="pl-4 space-y-1 mt-1" x-cloak>
                                    <a href="{{ route('services.training.soft') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2 text-sm text-white/60 hover:bg-white/10 hover:text-white transition-colors">Soft Skills</a>
                                    <a href="{{ route('services.training.technical') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2 text-sm text-white/60 hover:bg-white/10 hover:text-white transition-colors">Technical &amp; Non-Technical Skills</a>
                                </div>
                            </div>
                            <a href="{{ route('services.crew-management') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Crew Management</a>
                            <a href="{{ route('services.ship-chandelling') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Ship Chandelling</a>
                            <a href="{{ route('services.crew-abandonment') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Crew Abandonment Support</a>
                            <a href="{{ route('services.marine-procurement') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Marine Procurement</a>
                            <a href="{{ route('services.marine-insurance') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Marine Insurance</a>
                            <a href="{{ route('services.travel-management') }}" @click="mobileMenuOpen = false" class="block rounded-[10px] px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.08em] text-white/70 hover:bg-white/10 hover:text-white transition-colors">Travel Management Service</a>
                        </div>
                    </div>

                    <a href="{{ route('events.index') }}" @click="mobileMenuOpen = false"
                       class="block rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">Events</a>

                    <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false"
                       class="block rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">News</a>

                    <a href="{{ route('contact.index') }}" @click="mobileMenuOpen = false"
                       class="block rounded-[10px] px-4 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-white/85 hover:bg-white/10 hover:text-white transition-colors">Contact</a>
                </div>

                <div class="mt-4 grid gap-3">
                    @auth
                        <a href="{{ $dashboardRoute }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-white/20 rounded-[10px] text-white text-[12px] font-semibold uppercase tracking-[0.08em] hover:bg-white/10 transition-all">Dashboard</a>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 bg-[#1AAD94] rounded-[10px] text-white text-[12px] font-bold uppercase tracking-[0.08em] hover:brightness-110 transition-all">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-white/20 rounded-[10px] text-white text-[12px] font-semibold uppercase tracking-[0.08em] hover:bg-white/10 transition-all">Sign In</a>
                        <a href="{{ route('auth.register') }}" class="inline-flex items-center justify-center px-5 py-3 bg-[#1AAD94] rounded-[10px] text-white text-[12px] font-bold uppercase tracking-[0.08em] hover:brightness-110 transition-all">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
