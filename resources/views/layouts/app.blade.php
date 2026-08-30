<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="KaryaLokal — Temukan kerajinan tangan lokal berkualitas dari pengrajin Indonesia. Dukung UMKM handmade di sekitar Anda.">

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="16x16 32x32 48x48">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

        <title>{{ config('app.name', 'KaryaLokal') }} — Temukan Kerajinan Tangan Lokal</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js cloak -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-kl-warm">
        <div class="min-h-screen">
            <livewire:layout.navigation />

            <!-- Page Heading (optional) -->
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-kl">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <div
                x-data="{ toast: '', show: false }"
                x-on:notify.window="toast = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition
                x-cloak
                class="fixed top-4 inset-x-0 flex justify-center z-[100] pointer-events-none"
            >
                <p class="px-5 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold shadow-xl" x-text="toast"></p>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
