<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication — JOSEOCEANJOBS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#F9FAFB] min-h-screen antialiased">
    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-lg bg-white rounded-[12px] border border-[#E0E0E0] p-8 shadow-sm">
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
