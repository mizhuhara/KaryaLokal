<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="KaryaLokal — Masuk atau daftar untuk menemukan dan mendukung pengrajin handmade lokal Indonesia.">

        <title>{{ config('app.name', 'KaryaLokal') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
            <!-- Left: Branding Panel -->
            <div class="hidden lg:flex flex-col justify-between p-12 relative overflow-hidden kl-hero">
                <!-- Decorative circles -->
                <div class="absolute top-20 right-20 w-64 h-64 rounded-full opacity-10"
                     style="background: radial-gradient(circle, #F4A261, transparent)"></div>
                <div class="absolute bottom-20 left-10 w-40 h-40 rounded-full opacity-10"
                     style="background: radial-gradient(circle, #E8531D, transparent)"></div>

                <!-- Logo -->
                <div class="relative z-10">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('storage/images/logo/logo-karyalokal.png') }}" alt="KaryaLokal" class="h-12 w-auto brightness-0 invert">
                    </a>
                </div>

                <!-- Hero Content -->
                <div class="relative z-10">
                    <!-- Pattern / Illustration -->
                    <div class="mb-10 grid grid-cols-3 gap-3 opacity-80">
                        @foreach(['🌺', '🧶', '🪡', '🎋', '🧵', '🌸', '🪴', '🎍', '🌿'] as $icon)
                        <div class="aspect-square rounded-2xl flex items-center justify-center text-2xl"
                             style="background: rgba(255,255,255,0.08)">{{ $icon }}</div>
                        @endforeach
                    </div>

                    <h1 class="text-4xl font-bold text-white mb-4 leading-tight font-jakarta">
                        Temukan. Pesan.<br>
                        <span class="kl-gradient-text">Dukung Pengrajin Lokal.</span>
                    </h1>
                    <p class="text-white/70 text-lg leading-relaxed">
                        Platform marketplace kerajinan tangan Indonesia yang menghubungkan pembeli dengan pengrajin terdekat di sekitar Anda.
                    </p>

                    <!-- Stats -->
                    <div class="mt-10 grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white font-jakarta">500+</p>
                            <p class="text-white/60 text-sm">Pengrajin</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white font-jakarta">2.000+</p>
                            <p class="text-white/60 text-sm">Produk</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white font-jakarta">34</p>
                            <p class="text-white/60 text-sm">Provinsi</p>
                        </div>
                    </div>
                </div>

                <!-- Tagline -->
                <div class="relative z-10">
                    <p class="text-white/40 text-sm">© 2026 KaryaLokal. Bangga Produk Lokal.</p>
                </div>
            </div>

            <!-- Right: Form Panel -->
            <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 bg-kl-warm">
                <!-- Mobile logo -->
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                        <img src="{{ asset('storage/images/logo/logo-karyalokal.png') }}" alt="KaryaLokal" class="h-10 w-auto">
                    </a>
                </div>

                <div class="w-full max-w-md mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
