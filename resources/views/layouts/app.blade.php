<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="view-transition" content="same-origin">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JOSEOCEANJOBS — Your Career at Sea Starts Here')</title>
    <meta name="description" content="@yield('meta_description', 'Connect with leading maritime employers worldwide. Find certified positions, manage your documentation, and track your global deployment journey.')">
    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @view-transition { navigation: auto; }
        :root { --color-primary:#073057; --color-accent:#1AAD94; --color-dark:#2C2C2C; --color-light:#F5F5F5; --color-border:#E0E0E0; --color-muted:#6B7280; }
        body { font-family:'Inter',sans-serif; color:var(--color-dark); background-color:#fff; }
        .font-mono { font-family:'JetBrains Mono',monospace; }
        .hero-dot-grid { background-image:radial-gradient(rgba(26,173,148,.15) 1.5px,transparent 1.5px); background-size:32px 32px; }
        .compass-spin { animation:spin 60s linear infinite; }
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        .pipeline-pulse { animation:pulse-teal 2s cubic-bezier(.4,0,.6,1) infinite; }
        @keyframes pulse-teal { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.1);opacity:.8} }
        .hero-overlay { background:linear-gradient(to top,rgba(7,48,87,.95),rgba(7,48,87,.6)); }
        .hero-gradient { background:linear-gradient(135deg,#073057 0%,#041d36 100%); }
        .dot-pattern { background-image:radial-gradient(rgba(26,173,148,.08) 1.5px,transparent 1.5px); background-size:32px 32px; }
        .card-hover-lift { transition:transform .3s ease,box-shadow .3s ease; }
        .card-hover-lift:hover { transform:translateY(-8px); box-shadow:0 20px 40px rgba(0,0,0,.1); }
        .glow-teal { box-shadow:0 0 20px rgba(26,173,148,.4); }
        h1,h2,h3,h4 { letter-spacing:-.02em; }
        .hide-scrollbar::-webkit-scrollbar{display:none} .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
        [x-cloak] { display: none !important; }
        button:not(:disabled), [type="button"]:not(:disabled), [type="submit"]:not(:disabled), [type="reset"]:not(:disabled), [role="button"], a[href], label[for], label:has(input[type="checkbox"]), label:has(input[type="radio"]), summary, select, input[type="checkbox"], input[type="radio"], input[type="file"] { cursor: pointer; }
        button:disabled, [type="button"]:disabled, [type="submit"]:disabled, [type="reset"]:disabled { cursor: not-allowed; }
    </style>
    @stack('styles')
</head>
<body>
<div class="min-h-screen flex flex-col">

    @include('Layout::parts.header')

    @yield('content')

    @include('Layout::parts.footer')

</div>
@stack('scripts')
</body>
</html>
