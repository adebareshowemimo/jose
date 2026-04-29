@php
/**
 * Frontend Header Component
 *
 * Desktop navigation bar for the Metary Bootstrap-based frontend layout.
 * The mobile hamburger toggles the off-canvas drawer (mobile-nav component).
 * The top search bar is toggled by nav-menu.js on click of the .search li.
 */
$navItems = [
    [
        'label'    => 'Home',
        'route'    => 'home',
        'children' => [],
    ],
    [
        'label'    => 'About',
        'route'    => 'about.index',
        'children' => [
            ['label' => 'About JCL',   'route' => 'about.index',        'children' => []],
            ['label' => 'Leadership',  'route' => 'leadership.index',   'children' => []],
            ['label' => 'Partnership', 'route' => 'partnerships.index', 'children' => []],
        ],
    ],
    [
        'label'    => 'Jobs',
        'route'    => 'job.index',
        'children' => [],
    ],
    [
        'label'    => 'Career',
        'route'    => 'career.index',
        'children' => [
            ['label' => 'Apprenticeship', 'route' => 'career.apprenticeship', 'children' => []],
            ['label' => 'Internship',     'route' => 'career.internship',     'children' => []],
        ],
    ],
    [
        'label'    => 'Services',
        'route'    => 'services.index',
        'children' => [
            ['label' => 'Training', 'route' => 'services.training', 'children' => [
                ['label' => 'Soft Skills',                    'route' => 'services.training.soft'],
                ['label' => 'Technical & Non Technical Skills', 'route' => 'services.training.technical'],
            ]],
            ['label' => 'Crew Management',               'route' => 'services.crew-management',   'children' => []],
            ['label' => 'Ship Chandelling',               'route' => 'services.ship-chandelling',  'children' => []],
            ['label' => 'Crew Abandonment Support',   'route' => 'services.crew-abandonment',  'children' => []],
            ['label' => 'Marine Procurement',             'route' => 'services.marine-procurement','children' => []],
            ['label' => 'Marine Insurance',               'route' => 'services.marine-insurance',  'children' => []],
            ['label' => 'Travel Management Service',      'route' => 'services.travel-management', 'children' => []],
        ],
    ],
    [
        'label'    => 'Events',
        'route'    => 'events.index',
        'children' => [],
    ],
    [
        'label'    => 'News',
        'route'    => 'news.index',
        'children' => [],
    ],
    [
        'label'    => 'Contact',
        'route'    => 'contact.index',
        'children' => [],
    ],
];
@endphp

<header class="header-style1 menu_area-light">

    <div class="navbar-default border-bottom border-color-light-white">

        <!-- TOP SEARCH BAR (toggled by search icon click via nav-menu.js)
        ================================================== -->
        <div class="top-search bg-primary">
            <div class="container-fluid px-lg-1-6 px-xl-2-5 px-xxl-2-9">
                <form class="search-form" action="{{ route('job.index') }}" method="GET" accept-charset="utf-8">
                    <div class="input-group">
                        <span class="input-group-addon cursor-pointer">
                            <button class="search-form_submit fas fa-search text-white" type="submit"></button>
                        </span>
                        <input type="text"
                               class="search-form_input form-control"
                               name="s"
                               autocomplete="off"
                               placeholder="Search jobs, skills, companies…">
                        <span class="input-group-addon close-search mt-1"><i class="fas fa-times"></i></span>
                    </div>
                </form>
            </div>
        </div>
        <!-- end top search -->

        <div class="container-fluid px-lg-1-6 px-xl-2-5 px-xxl-2-9">
            <div class="row align-items-center">
                <div class="col-12 col-lg-12">
                    <div class="menu_area alt-font">
                        <nav class="navbar navbar-expand-lg navbar-light p-0">

                            <!-- LOGO
                            ================================================== -->
                            <div class="navbar-header navbar-header-custom">
                                <a href="{{ route('home') }}" class="navbar-brand">
                                    <img id="logo"
                                         src="{{ asset('images/dark_logo.png') }}"
                                         alt="Jose Consulting Limited" />
                                </a>
                            </div>

                            <!-- MOBILE HAMBURGER
                            Triggers the off-canvas mobile drawer (#mobileNavDrawer).
                            data-bs-* attributes are added for Bootstrap 5 offcanvas.
                            nav-menu.js will also pick this up for its own mobile handling. -->
                            <div class="navbar-toggler bg-primary"
                                 data-bs-toggle="offcanvas"
                                 data-bs-target="#mobileNavDrawer"
                                 aria-controls="mobileNavDrawer"
                                 role="button"
                                 aria-label="Open navigation"></div>

                            <!-- DESKTOP NAV MENU
                            (hidden by default; nav-menu.js + CSS make it visible on ≥ lg)
                            ================================================== -->
                            <ul class="navbar-nav ms-auto" id="nav" style="display: none;">

                                @foreach ($navItems as $item)
                                    @if (count($item['children']) > 0)
                                        <li>
                                            <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                                            <ul>
                                                @foreach ($item['children'] as $child)
                                                    @if (!empty($child['children']))
                                                        <li>
                                                            <a href="{{ route($child['route']) }}">{{ $child['label'] }}</a>
                                                            <ul>
                                                                @foreach ($child['children'] as $grandchild)
                                                                    <li><a href="{{ route($grandchild['route']) }}">{{ $grandchild['label'] }}</a></li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="{{ route($child['route']) }}">{{ $child['label'] }}</a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                                        </li>
                                    @endif
                                @endforeach

                            </ul>
                            <!-- end nav menu -->

                            <!-- ATTRIBUTE NAV (search icon + CTA)
                            ================================================== -->
                            <div class="attr-nav align-items-xl-center ms-xl-auto main-font">
                                <ul>
                                    <li class="search">
                                        <a href="#" aria-label="Toggle search">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </li>

                                    @auth
                                        <li class="d-none d-xl-inline-block">
                                            <a href="{{ route('user.dashboard') }}" class="btn-style1 white-hover small">
                                                <span>My Dashboard</span>
                                            </a>
                                        </li>
                                    @else
                                        <li class="d-none d-xl-inline-block">
                                            <a href="{{ route('auth.login') }}" class="btn-style1 white-hover small">
                                                <span>Sign In</span>
                                            </a>
                                        </li>
                                        <li class="d-none d-xl-inline-block ms-2">
                                            <a href="{{ route('auth.register') }}" class="btn-style1 small">
                                                <span>Register</span>
                                            </a>
                                        </li>
                                    @endauth
                                </ul>
                            </div>
                            <!-- end attribute nav -->

                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>

</header>
