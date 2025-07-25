<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Stacknigro.it</title>

    @include('backend.layouts.includes.assets')
</head>
<body class="g-sidenav-show bg-gray-200">

    @include('backend.layouts.includes.sidebar')

    <main class="main-content border-radius-lg">
        @include('backend.layouts.includes.navbar')

        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </main>

</body>
</html>