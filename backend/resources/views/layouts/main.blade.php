<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LetUsDonate')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&family=Annie+Use+Your+Telescope&family=Iansui&family=Indie+Flower&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Frontend CSS -->
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    @stack('styles')

    <style>
        /* Ensuring layout classes match frontend expectation */
        .tt-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
        }
    </style>
</head>
<body>
    @unless($hideHeaderFooter ?? false)
        @if($useAltHeader ?? false)
            <x-header-alt />
        @else
            <x-header />
        @endif
    @endunless

    <main>
        @yield('content')
    </main>

    @unless($hideHeaderFooter ?? false)
        <x-footer />
    @endunless

    @stack('scripts')
</body>
</html>
