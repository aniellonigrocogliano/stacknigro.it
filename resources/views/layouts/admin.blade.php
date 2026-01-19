<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'Admin')</title>

  <link href="{{ asset('themes/admin/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/material-dashboard.min.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
</head>

<body class="bg-gray-200 g-sidenav-show">

  {{-- SIDEBAR --}}
  @include('admin.partials.sidebar')

  {{-- CONTENUTO --}}
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @yield('content')
  </main>

  {{-- CORE JS (SOLO QUI, NON NEL LOGIN) --}}
  <script src="{{ asset('themes/admin/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/material-dashboard.min.js') }}"></script>
@stack('scripts')
</body>
</html>
