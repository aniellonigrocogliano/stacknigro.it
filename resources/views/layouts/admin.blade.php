<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin | Stacknigro.it')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Material Dashboard CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/admin/css/material-dashboard.min.css') }}">
</head>
<body class="g-sidenav-show bg-gray-200">

    @yield('content')

    {{-- Material Dashboard JS --}}
    <script src="{{ asset('themes/admin/js/material-dashboard.min.js') }}"></script>
</body>
</html>
