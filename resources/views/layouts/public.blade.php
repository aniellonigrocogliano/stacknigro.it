<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>@yield('title', 'Stacknigro.it')</title>

  {{-- Favicon dinamica --}}
  <link rel="icon" type="image/png"
        href="{{ $site?->favicon_path ? asset('storage/'.$site->favicon_path) : asset('themes/bootslander/img/favicon.png') }}">

  {{-- Vendor CSS (Bootslander) --}}
  <link href="{{ asset('themes/bootslander/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('themes/bootslander/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('themes/bootslander/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('themes/bootslander/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('themes/bootslander/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">

  {{-- Main CSS (Bootslander) --}}
  <link href="{{ asset('themes/bootslander/css/main.css') }}" rel="stylesheet">

  {{-- Google Analytics (GA4) - carica SOLO se consenso = all --}}
@php
  $gaId = $site?->analytics_measurement_id;
  $consent = $_COOKIE['sn_cookie_consent'] ?? null; // 'necessary' | 'all' | null
@endphp

@if(!empty($gaId) && $consent === 'all')
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', @json($gaId));
  </script>
@endif
</head>

@php $isHome = request()->is('/'); @endphp
<body class="{{ $isHome ? 'index-page' : 'starter-page' }}">

  @include('public.partials.header')

  <main id="main">
    @include('public.partials.hero', ['showContent' => $isHome])
    @yield('content')
  </main>

  @include('public.partials.footer')
{{-- Scroll top (obbligatorio per main.js del template) --}}
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>

{{-- Preloader (obbligatorio) --}}
<div id="preloader"></div>
  {{-- Vendor JS (Bootslander) --}}
  <script src="{{ asset('themes/bootslander/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('themes/bootslander/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('themes/bootslander/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('themes/bootslander/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('themes/bootslander/vendor/swiper/swiper-bundle.min.js') }}"></script>

  {{-- Main JS (Bootslander) --}}
  <script src="{{ asset('themes/bootslander/js/main.js') }}"></script>

  {{-- Cookie modal --}}
  @include('public.partials.cookie-consent-modal')
  @stack('scripts')
</body>
</html>
