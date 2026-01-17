<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Stacknigro.it')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootslander CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/bootslander/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
</head>
<body>

    @yield('content')

    {{-- Bootslander JS --}}
    <script src="{{ asset('themes/bootslander/js/main.js') }}"></script>
</body>
</html>
