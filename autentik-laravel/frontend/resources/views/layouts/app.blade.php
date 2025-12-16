<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Autentik' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <!-- Alpine.js CDN -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Lucide icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  <style>body{font-family:var(--font-sans);}</style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><text x='0' y='14'>🔒</text></svg>">
</head>
<body class="min-h-screen bg-white text-gray-900">
  @include('partials.header')
  <main class="min-h-screen">
    @yield('content')
  </main>
  @include('partials.footer')
</body>
</html>
