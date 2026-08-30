<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="KaryaLokal — Platform marketplace kerajinan handmade Indonesia.">

        <title>{{ config('app.name', 'KaryaLokal') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-kl-warm">
        <div class="min-h-screen">
            <livewire:layout.navigation />

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