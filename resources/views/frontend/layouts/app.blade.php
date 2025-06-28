<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Stacknigro')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  @yield('content')
</body>
</html>
