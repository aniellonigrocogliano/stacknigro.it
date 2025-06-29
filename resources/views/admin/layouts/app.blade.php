
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <link href="{{ asset('assets-admin/assets/css/material-dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
