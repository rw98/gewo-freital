@props([
    'title' => null,
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
</head>
<body class="min-h-screen bg-white text-gewo-grey-800 antialiased">
    {{-- Minimal Header --}}
    <header class="sticky top-0 z-50 bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center" wire:navigate>
                    <x-public.logo />
                </a>

                <div class="flex items-center gap-2">
                    @if (Route::currentRouteName() !== 'login')
                        <flux:button href="{{ route('login') }}" variant="ghost" size="sm" wire:navigate>
                            {{ __('pages.layout.auth.login') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Minimal Footer --}}
    <footer class="bg-gewo-grey-50 border-t border-gewo-grey-200 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 40 40" class="text-accent" aria-hidden="true">
                        <circle cx="5" cy="5" r="3" fill="currentColor"/>
                        <circle cx="13" cy="5" r="3" fill="currentColor"/>
                        <circle cx="21" cy="5" r="3" fill="currentColor"/>
                        <circle cx="29" cy="5" r="3" fill="currentColor"/>
                        <circle cx="5" cy="13" r="3" fill="currentColor"/>
                        <circle cx="13" cy="13" r="3" fill="currentColor"/>
                        <circle cx="21" cy="13" r="3" fill="currentColor"/>
                        <circle cx="5" cy="21" r="3" fill="currentColor"/>
                        <circle cx="13" cy="21" r="3" fill="currentColor"/>
                        <circle cx="5" cy="29" r="3" fill="currentColor"/>
                        <rect x="24" y="16" width="12" height="6" rx="3" fill="#c8102e"/>
                    </svg>
                    <flux:text size="sm" class="text-gewo-grey-500">
                        {{ __('pages.layout.footer.copyright', ['year' => date('Y')]) }}
                    </flux:text>
                </div>
                <div class="flex items-center gap-4 text-sm text-gewo-grey-500">
                    <a href="#" class="hover:text-gewo-grey-800">{{ __('Privacy') }}</a>
                    <a href="#" class="hover:text-gewo-grey-800">{{ __('Terms') }}</a>
                    <a href="#" class="hover:text-gewo-grey-800">{{ __('Contact') }}</a>
                </div>
            </div>
        </div>
    </footer>

    @fluxScripts
</body>
</html>
