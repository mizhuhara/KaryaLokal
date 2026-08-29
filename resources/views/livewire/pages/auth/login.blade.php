<?php

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

        $this->redirect(route('home'), navigate: false);
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
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm">{{ session('status') }}</span>
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <!-- Email -->
        <div>
            <label for="login-email" class="kl-label">Alamat Email</label>
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
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
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input wire:model="form.password" id="login-password"
                       :type="showPwd ? 'text' : 'password'"
                       name="password" required autocomplete="current-password"
                       placeholder="Masukkan kata sandi"
                       class="kl-input pl-10 pr-10 @error('form.password') border-red-400 @enderror" />
                <button type="button" @click="showPwd = !showPwd"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg x-show="!showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="!showPwd" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
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
