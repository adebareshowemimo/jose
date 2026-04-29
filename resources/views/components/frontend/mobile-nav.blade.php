@php
/**
 * Mobile Navigation Drawer Component
 *
 * Bootstrap 5 Offcanvas drawer that slides in from the left on mobile.
 * Triggered by the .navbar-toggler in the header component via
 * data-bs-toggle="offcanvas" data-bs-target="#mobileNavDrawer".
 *
 * This component is intentionally separate from the desktop header so it
 * can be styled and iterated independently.
 */
$mobileNavItems = [
    [
        'label'    => 'Home',
        'route'    => 'home',
        'icon'     => 'fas fa-home',
        'children' => [],
    ],
    [
        'label'    => 'About',
        'route'    => 'about.index',
        'icon'     => 'fas fa-info-circle',
        'children' => [
            ['label' => 'Leadership',  'route' => 'leadership.index',  'children' => []],
            ['label' => 'Partnership', 'route' => 'partnerships.index', 'children' => []],
        ],
    ],
    [
        'label'    => 'Jobs',
        'route'    => 'job.index',
        'icon'     => 'fas fa-briefcase',
        'children' => [],
    ],
    [
        'label'    => 'Services',
        'route'    => 'services.index',
        'icon'     => 'fas fa-cogs',
        'children' => [
            ['label' => 'Training', 'route' => 'services.training', 'children' => [
                ['label' => 'Soft Skills',                     'route' => 'services.training.soft'],
                ['label' => 'Technical & Non Technical Skills', 'route' => 'services.training.technical'],
            ]],
            ['label' => 'Crew Management',              'route' => 'services.crew-management',   'children' => []],
            ['label' => 'Ship Chandelling',              'route' => 'services.ship-chandelling',  'children' => []],
            ['label' => 'Crew Abandonment Support',  'route' => 'services.crew-abandonment',  'children' => []],
            ['label' => 'Marine Procurement',            'route' => 'services.marine-procurement', 'children' => []],
            ['label' => 'Marine Insurance',              'route' => 'services.marine-insurance',  'children' => []],
            ['label' => 'Travel Management Service',     'route' => 'services.travel-management', 'children' => []],
        ],
    ],
    [
        'label'    => 'Career',
        'route'    => 'career.index',
        'icon'     => 'fas fa-user-graduate',
        'children' => [
            ['label' => 'Apprenticeship', 'route' => 'career.apprenticeship', 'children' => []],
            ['label' => 'Internship',     'route' => 'career.internship',     'children' => []],
        ],
    ],
    [
        'label'    => 'Events',
        'route'    => 'events.index',
        'icon'     => 'fas fa-calendar-alt',
        'children' => [],
    ],
    [
        'label'    => 'News',
        'route'    => 'news.index',
        'icon'     => 'fas fa-newspaper',
        'children' => [],
    ],
    [
        'label'    => 'Contact',
        'route'    => 'contact.index',
        'icon'     => 'fas fa-envelope',
        'children' => [],
    ],
];
@endphp

{{-- =====================================================
     MOBILE NAVIGATION DRAWER
     Bootstrap 5 Offcanvas — slides in from the left
     ===================================================== --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNavDrawer" aria-labelledby="mobileNavDrawerLabel">

    {{-- DRAWER HEADER --}}
    <div class="offcanvas-header bg-secondary py-3 px-4">
        <a href="{{ route('home') }}" class="navbar-brand p-0 jcl-logo-on-dark">
            <img src="{{ asset('images/dark_logo.png') }}"
                 alt="Jose Consulting Limited"
                 style="height: 38px; width: auto;">
        </a>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas"
                aria-label="Close navigation"></button>
    </div>

    {{-- DRAWER BODY --}}
    <div class="offcanvas-body p-0 d-flex flex-column">

        {{-- USER CTA ROW (shown only when authenticated or as guest) --}}
        <div class="px-4 py-3 bg-light border-bottom">
            @auth
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                         style="width:40px;height:40px;flex-shrink:0;">
                        <span class="text-white fw-bold" style="font-size:14px;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold" style="font-size:14px;line-height:1.2;">
                            {{ auth()->user()->name ?? 'User' }}
                        </p>
                        <a href="{{ route('user.dashboard') }}"
                           class="text-primary text-decoration-none"
                           style="font-size:12px;">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            @else
                <div class="d-flex gap-2">
                    <a href="{{ route('auth.login') }}"
                       class="btn btn-sm btn-outline-secondary flex-fill text-center fw-semibold"
                       style="font-size:13px; letter-spacing:.04em; text-transform:uppercase;">
                        Sign In
                    </a>
                    <a href="{{ route('auth.register') }}"
                       class="btn btn-sm btn-primary flex-fill text-center fw-semibold"
                       style="font-size:13px; letter-spacing:.04em; text-transform:uppercase;">
                        Register
                    </a>
                </div>
            @endauth
        </div>

        {{-- NAV ITEMS --}}
        <nav class="flex-grow-1 overflow-auto">
            <ul class="list-unstyled mb-0">
                @foreach ($mobileNavItems as $index => $item)
                    @if (count($item['children']) > 0)
                        {{-- Accordion parent --}}
                        <li class="border-bottom">
                            <a class="d-flex align-items-center justify-content-between px-4 py-3 text-dark text-decoration-none fw-semibold collapsed"
                               style="font-size:14px; letter-spacing:.05em; text-transform:uppercase;"
                               data-bs-toggle="collapse"
                               data-bs-target="#mobileSubMenu{{ $index }}"
                               aria-expanded="false"
                               aria-controls="mobileSubMenu{{ $index }}">
                                <span>
                                    <i class="{{ $item['icon'] }} me-2 text-primary" style="width:16px;"></i>
                                    {{ $item['label'] }}
                                </span>
                                <i class="fas fa-chevron-down text-muted" style="font-size:11px; transition:.2s;"></i>
                            </a>
                            <div id="mobileSubMenu{{ $index }}" class="collapse">
                                <ul class="list-unstyled mb-0 bg-light">
                                    @foreach ($item['children'] as $cIdx => $child)
                                        @if (!empty($child['children']))
                                            <li class="border-bottom border-light">
                                                <a class="d-flex align-items-center justify-content-between px-5 py-2 text-muted text-decoration-none fw-semibold collapsed"
                                                   style="font-size:13px;"
                                                   data-bs-toggle="collapse"
                                                   data-bs-target="#mobileSubSub{{ $index }}_{{ $cIdx }}"
                                                   aria-expanded="false">
                                                    <span><i class="fas fa-angle-right me-2"></i>{{ $child['label'] }}</span>
                                                    <i class="fas fa-caret-down text-muted" style="font-size:10px;"></i>
                                                </a>
                                                <div id="mobileSubSub{{ $index }}_{{ $cIdx }}" class="collapse">
                                                    <ul class="list-unstyled mb-0" style="background:#efefef;">
                                                        @foreach ($child['children'] as $grandchild)
                                                            <li class="border-bottom border-light">
                                                                <a href="{{ route($grandchild['route']) }}"
                                                                   class="d-block py-2 text-muted text-decoration-none"
                                                                   style="font-size:12px; padding-left:3.5rem;">
                                                                    {{ $grandchild['label'] }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                        @else
                                            <li class="border-bottom border-light">
                                                <a href="{{ route($child['route']) }}"
                                                   class="d-block px-5 py-2 text-muted text-decoration-none"
                                                   style="font-size:13px;">
                                                    <i class="fas fa-angle-right me-2"></i>{{ $child['label'] }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @else
                        {{-- Single link --}}
                        <li class="border-bottom">
                            <a href="{{ route($item['route']) }}"
                               class="d-flex align-items-center px-4 py-3 text-dark text-decoration-none fw-semibold"
                               style="font-size:14px; letter-spacing:.05em; text-transform:uppercase;">
                                <i class="{{ $item['icon'] }} me-2 text-primary" style="width:16px;"></i>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

        {{-- DRAWER FOOTER: social + copyright --}}
        <div class="px-4 py-3 bg-secondary mt-auto">
            <div class="d-flex justify-content-center gap-3 mb-2">
                <a href="#" class="text-white opacity-75 text-decoration-none" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="text-white opacity-75 text-decoration-none" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="text-white opacity-75 text-decoration-none" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" class="text-white opacity-75 text-decoration-none" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
            <p class="text-center text-white mb-0 opacity-50" style="font-size:11px;">
                &copy; {{ date('Y') }} Jose Consulting Limited
            </p>
        </div>

    </div>

</div>
{{-- /offcanvas --}}
