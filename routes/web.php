<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

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

Route::view('admin/dashboard', 'admin.dashboard')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
