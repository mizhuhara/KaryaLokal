<?php

use App\Enums\UserRole;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();
        $redirect = match ($user->role) {
            UserRole::Seller => route('seller.dashboard', absolute: false),
            UserRole::Admin => route('admin.dashboard', absolute: false),
            default => route('home', absolute: false),
        };

        $this->redirect($redirect, navigate: false);
    }
}; ?>

<div>
    <!-- Heading -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 font-jakarta">Selamat Datang!</h2>
        <p class="text-gray-500 mt-1">Masuk ke akun KaryaLokal Anda</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="kl-alert-success mb-6">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0" style="color: #065F46" />
            <span class="text-sm">{{ session('status') }}</span>
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <!-- Email -->
        <div>
            <label for="login-email" class="kl-label">Alamat Email</label>
            <div class="relative">
                <x-icon name="envelope" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model="form.email" id="login-email" type="email" name="email"
                       required autofocus autocomplete="username"
                       placeholder="nama@email.com"
                       class="kl-input pl-10 @error('form.email') border-red-400 @enderror" />
            </div>
            @error('form.email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="login-password" class="kl-label !mb-0">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                       class="text-xs font-medium hover:underline" style="color: #E8531D">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="relative" x-data="{ showPwd: false }">
                <x-icon name="lock-closed" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model="form.password" id="login-password"
                       :type="showPwd ? 'text' : 'password'"
                       name="password" required autocomplete="current-password"
                       placeholder="Masukkan kata sandi"
                       class="kl-input pl-10 pr-10 @error('form.password') border-red-400 @enderror" />
                <button type="button" @click="showPwd = !showPwd"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <x-icon x-show="!showPwd" name="eye" class="w-4 h-4" />
                    <x-icon x-show="showPwd" x-cloak name="eye-slash" class="w-4 h-4" />
                </button>
            </div>
            @error('form.password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2">
            <input wire:model="form.remember" id="login-remember" type="checkbox"
                   class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
            <label for="login-remember" class="text-sm text-gray-600 select-none cursor-pointer">
                Ingat saya selama 30 hari
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" id="login-submit-btn"
                class="kl-btn-primary w-full justify-center py-3.5 text-base mt-2">
            <span wire:loading.remove wire:target="login">Masuk</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Memproses...
            </span>
        </button>
    </form>

    <!-- Divider -->
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px bg-gray-200"></div>
        <p class="text-sm text-gray-400">Belum punya akun?</p>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <!-- Register Link -->
    <a href="{{ route('register') }}" wire:navigate
       class="kl-btn-secondary w-full justify-center py-3 text-sm">
        Daftar Akun Baru
    </a>
</div>
