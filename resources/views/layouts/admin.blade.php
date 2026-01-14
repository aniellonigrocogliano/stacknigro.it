<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin | Stacknigro.it')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Material Dashboard CSS --}}
<link href="{{ asset('themes/admin/css/nucleo-icons.css') }}" rel="stylesheet" />
<link href="{{ asset('themes/admin/css/nucleo-svg.css') }}" rel="stylesheet" />
<link href="{{ asset('themes/admin/css/material-dashboard.min.css') }}" rel="stylesheet" />


</head>
<body class="g-sidenav-show bg-gray-200">

    @yield('content')

    {{-- Material Dashboard JS --}}
    <script src="{{ asset('themes/admin/js/material-dashboard.min.js') }}"></script>
</body>
<script src="{{ asset('themes/admin/js/core/popper.min.js') }}"></script>
<script src="{{ asset('themes/admin/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('themes/admin/js/material-dashboard.min.js') }}"></script>

</html>
