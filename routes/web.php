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

\Livewire\Volt\Volt::route('cart', 'pages.cart')
    ->name('cart');

\Livewire\Volt\Volt::route('checkout', 'pages.checkout')
    ->middleware(['auth', 'verified'])
    ->name('checkout');

\Livewire\Volt\Volt::route('orders', 'pages.buyer-orders')
    ->middleware(['auth', 'verified'])
    ->name('buyer.orders');

\Livewire\Volt\Volt::route('notifications', 'pages.notifications')
    ->middleware(['auth', 'verified'])
    ->name('notifications');

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

\Livewire\Volt\Volt::route('seller/orders', 'pages.seller.orders')
    ->middleware(['auth', 'verified', 'role:seller'])
    ->name('seller.orders');

\Livewire\Volt\Volt::route('chat', 'pages.chat')
    ->middleware(['auth', 'verified'])
    ->name('chat');

\Livewire\Volt\Volt::route('chat/{user}', 'pages.chat')
    ->middleware(['auth', 'verified'])
    ->name('chat.user');

\Livewire\Volt\Volt::route('admin/dashboard', 'pages.admin.dashboard')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

\Livewire\Volt\Volt::route('admin/users', 'pages.admin.users')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.users');

\Livewire\Volt\Volt::route('admin/sellers', 'pages.admin.sellers')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.sellers');

\Livewire\Volt\Volt::route('admin/products', 'pages.admin.products')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.products');

\Livewire\Volt\Volt::route('admin/categories', 'pages.admin.categories')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.categories');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
