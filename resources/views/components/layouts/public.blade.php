@props([
    'title' => null,
    'navItems' => [],
    'footerColumns' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-font-size="normal" class="light" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Force light mode --}}
    <script>
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
    </script>

    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-white text-gewo-grey-800 antialiased">
    <x-public.header :nav-items="$navItems" />

    <main>
        {{ $slot }}
    </main>

    <x-public.footer :columns="$footerColumns" />

    @fluxScripts
</body>
</html>
