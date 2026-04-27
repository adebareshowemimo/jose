<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- metas -->
    <meta charset="utf-8">
    <meta name="author" content="JOSEOCEANJOBS — Jose Consulting Limited">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- title -->
    <title>@yield('title', 'JOSEOCEANJOBS — Your Career at Sea Starts Here')</title>
    <meta name="description" content="@yield('meta_description', 'Connect with leading maritime employers worldwide. Find certified positions, manage your documentation, and track your global deployment journey.')">
    <meta name="keywords" content="@yield('meta_keywords', 'maritime jobs, seafarer jobs, offshore jobs, marine careers, Jose Consulting Limited')">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

    <!--
        JCL Brand Font: Gilroy (commercial — Radomir Tinkov / Fontfabric).
        Self-host by placing the files below in /public/fonts/gilroy/ and
        the @font-face rules in custom.css will resolve automatically.

        Expected files (WOFF2 + WOFF minimum):
          gilroy-bold.woff2 / gilroy-bold.woff
          gilroy-medium.woff2 / gilroy-medium.woff
          gilroy-regular.woff2 / gilroy-regular.woff

        Interim: Plus Jakarta Sans (Google Fonts) serves as a visual fallback
        until the Gilroy files are added.
    -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- plugins css -->
    <link rel="stylesheet" href="{{ asset('metary/css/plugins.css') }}">

    <!-- search css -->
    <link rel="stylesheet" href="{{ asset('metary/search/search.css') }}">

    <!-- quform css -->
    <link rel="stylesheet" href="{{ asset('metary/quform/css/base.css') }}">

    <!-- theme core css -->
    <link rel="stylesheet" href="{{ asset('metary/css/styles.css') }}">

    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('metary/css/custom.css') }}">

    @stack('styles')
</head>

<body>

    <!-- PAGE LOADING
    ================================================== -->
    <div id="preloader"></div>

    <!-- MAIN WRAPPER
    ================================================== -->
    <div class="main-wrapper">

        <!-- HEADER (desktop) -->
        <x-frontend.header />

        <!-- MOBILE NAVIGATION DRAWER -->
        <x-frontend.mobile-nav />

        <!-- CONTENT
        ================================================== -->
        @yield('content')

        <!-- FOOTER -->
        <x-frontend.footer />

    </div>

    <!-- SCROLL TO TOP
    ================================================== -->
    <div class="scroll-top-percentage"><span id="scroll-value"></span></div>

    <!-- ============== JS INCLUDES ============== -->

    <!-- jQuery -->
    <script src="{{ asset('metary/js/jquery.min.js') }}"></script>

    <!-- popper js -->
    <script src="{{ asset('metary/js/popper.min.js') }}"></script>

    <!-- bootstrap -->
    <script src="{{ asset('metary/js/bootstrap.min.js') }}"></script>

    <!-- search -->
    <script src="{{ asset('metary/search/search.js') }}"></script>

    <!-- navigation (mobile menu) -->
    <script src="{{ asset('metary/js/nav-menu.js') }}"></script>

    <!-- owl carousel -->
    <script src="{{ asset('metary/js/owl.carousel.js') }}"></script>

    <!-- animated headline -->
    <script src="{{ asset('metary/js/animated-headline.js') }}"></script>

    <!-- owl carousel thumbs -->
    <script src="{{ asset('metary/js/owl.carousel.thumbs.js') }}"></script>

    <!-- counter -->
    <script src="{{ asset('metary/js/jquery.counterup.min.js') }}"></script>

    <!-- stellar parallax -->
    <script src="{{ asset('metary/js/jquery.stellar.min.js') }}"></script>

    <!-- waypoints -->
    <script src="{{ asset('metary/js/waypoints.min.js') }}"></script>

    <!-- countdown -->
    <script src="{{ asset('metary/js/countdown.js') }}"></script>

    <!-- magnific popup -->
    <script src="{{ asset('metary/js/jquery.magnific-popup.min.js') }}"></script>

    <!-- lightgallery -->
    <script src="{{ asset('metary/js/lightgallery-all.js') }}"></script>

    <!-- mousewheel -->
    <script src="{{ asset('metary/js/jquery.mousewheel.min.js') }}"></script>

    <!-- jarallax -->
    <script src="{{ asset('metary/js/jarallax.min.js') }}"></script>

    <!-- jarallax video -->
    <script src="{{ asset('metary/js/jarallax-video.js') }}"></script>

    <!-- wow animate -->
    <script src="{{ asset('metary/js/wow.js') }}"></script>

    <!-- smooth scroll -->
    <script src="{{ asset('metary/js/smoothscroll.js') }}"></script>

    <!-- main theme scripts -->
    <script src="{{ asset('metary/js/main.js') }}"></script>

    <!-- quform plugins -->
    <script src="{{ asset('metary/quform/js/plugins.js') }}"></script>

    <!-- quform scripts -->
    <script src="{{ asset('metary/quform/js/scripts.js') }}"></script>

    @stack('scripts')

</body>

</html>
