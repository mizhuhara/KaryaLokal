<?php

use Illuminate\Support\Facades\Route;

\Livewire\Volt\Volt::route('/', 'pages.home')
    ->name('home');

\Livewire\Volt\Volt::route('products', 'pages.products')
    ->name('products');

\Livewire\Volt\Volt::route('products/{product}', 'pages.product-detail')
    ->name('product-detail');

\Livewire\Volt\Volt::route('seller/{seller}', 'pages.seller-store')
    ->name('seller-store');

\Livewire\Volt\Volt::route('wishlist', 'pages.wishlist')
    ->middleware(['auth', 'verified'])
    ->name('wishlist');

\Livewire\Volt\Volt::route('nearby', 'pages.nearby')
    ->name('nearby');

\Livewire\Volt\Volt::route('nearby/map', 'pages.nearby-map')
    ->name('nearby-map');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('seller/dashboard', 'seller.dashboard')
    ->middleware(['auth', 'verified', 'role:seller'])
    ->name('seller.dashboard');

\Livewire\Volt\Volt::route('seller/register', 'pages.seller.register')
    ->middleware(['auth', 'verified'])
    ->name('seller.register');

\Livewire\Volt\Volt::route('seller/profile', 'pages.seller.profile')
    ->middleware(['auth', 'verified', 'role:seller'])
    ->name('seller.profile');

\Livewire\Volt\Volt::route('seller/products', 'pages.seller.products')
    ->middleware(['auth', 'verified', 'role:seller'])
    ->name('seller.products');

\Livewire\Volt\Volt::route('seller/products/{product}/images', 'pages.seller.product-images')
    ->middleware(['auth', 'verified', 'role:seller'])
    ->name('seller.product-images');

Route::view('admin/dashboard', 'admin.dashboard')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
