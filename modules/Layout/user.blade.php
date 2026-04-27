<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard — JOSEOCEANJOBS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#F9FAFB] min-h-screen antialiased">
    @include('Layout::parts.header')

    <main class="container-site section-spacing">
        <div class="grid lg:grid-cols-[18rem_1fr] gap-6">
            @include('Layout::parts.sidebar')
            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6">
                @yield('content')
            </div>
        </div>
    </main>

    @stack('scripts')
</body>
</html>
