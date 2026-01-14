<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Login | Admin')</title>

  <link href="{{ asset('themes/admin/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/material-dashboard.min.css') }}" rel="stylesheet" />
</head>

<body class="bg-gray-200">
  @yield('content')

  {{-- JS minimi per form/layout --}}
  <script src="{{ asset('themes/admin/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/core/bootstrap.min.js') }}"></script>
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</body>
</html>

