<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Stacknigro.it')</title>

    {{-- 1. Google Analytics (GA4) con Consent Mode v2 --}}
    @php
        $gaId = $site?->analytics_measurement_id;
        $consent = $_COOKIE['sn_cookie_consent'] ?? null;
    @endphp

    @if(!empty($gaId))
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        @if($consent === 'all')
            gtag('consent', 'default', {
                'ad_storage': 'granted',
                'analytics_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted'
            });
        @else
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'analytics_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied'
            });
        @endif
    </script>

    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}');
    </script>
    @endif

    {{-- 2. Resto degli stili e favicon --}}
    <link rel="icon" type="image/png" href="{{ $site?->favicon_path ? asset('storage/'.$site->favicon_path) : asset('themes/bootslander/img/favicon.png') }}">
    <link href="{{ asset('themes/bootslander/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/bootslander/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/bootslander/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/bootslander/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/bootslander/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link href="{{ asset('themes/bootslander/css/main.css') }}" rel="stylesheet">
</head>

@php $isHome = request()->is('/'); @endphp
<body class="{{ $isHome ? 'index-page' : 'starter-page' }}">
    @include('public.partials.header')
    <main id="main">
        @include('public.partials.hero', ['showContent' => $isHome])
        @yield('content')
    </main>
    @include('public.partials.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <script src="{{ asset('themes/bootslander/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="/themes/bootslander/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="{{ asset('themes/bootslander/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('themes/bootslander/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('themes/bootslander/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('themes/bootslander/js/main.js') }}"></script>

    @include('public.partials.cookie-consent-modal')
    @stack('scripts')
</body>
</html>
