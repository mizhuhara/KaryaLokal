<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = \App\Enums\UserRole::Buyer->value;
        $validated['email_verified_at'] = now();

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(auth()->user()->role->homeRoute(), navigate: true);
    }
}; ?>

<div>
    <!-- Heading -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 font-jakarta">Buat Akun</h2>
        <p class="text-gray-500 mt-1">Bergabung dan temukan kerajinan lokal terbaik</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Full Name -->
        <div>
            <label for="reg-name" class="kl-label">Nama Lengkap</label>
            <div class="relative">
                <x-icon name="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model="name" id="reg-name" type="text" name="name"
                       required autofocus autocomplete="name"
                       placeholder="Nama lengkap Anda"
                       class="kl-input pl-10 @error('name') border-red-400 @enderror" />
            </div>
            @error('name')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="reg-email" class="kl-label">Alamat Email</label>
            <div class="relative">
                <x-icon name="envelope" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model="email" id="reg-email" type="email" name="email"
                       required autocomplete="username"
                       placeholder="nama@email.com"
                       class="kl-input pl-10 @error('email') border-red-400 @enderror" />
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="reg-password" class="kl-label">Kata Sandi</label>
            <div class="relative" x-data="{ showPwd: false }">
                <x-icon name="lock-closed" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model="password" id="reg-password"
                       :type="showPwd ? 'text' : 'password'"
                       name="password" required autocomplete="new-password"
                       placeholder="Minimal 8 karakter"
                       class="kl-input pl-10 pr-10 @error('password') border-red-400 @enderror" />
                <button type="button" @click="showPwd = !showPwd"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <x-icon x-show="!showPwd" name="eye" class="w-4 h-4" />
                    <x-icon x-show="showPwd" x-cloak name="eye-slash" class="w-4 h-4" />
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="reg-confirm-password" class="kl-label">Konfirmasi Kata Sandi</label>
            <div class="relative" x-data="{ showPwd2: false }">
                <x-icon name="check-badge" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input wire:model="password_confirmation" id="reg-confirm-password"
                       :type="showPwd2 ? 'text' : 'password'"
                       name="password_confirmation" required autocomplete="new-password"
                       placeholder="Ulangi kata sandi"
                       class="kl-input pl-10 pr-10" />
                <button type="button" @click="showPwd2 = !showPwd2"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <x-icon x-show="!showPwd2" name="eye" class="w-4 h-4" />
                    <x-icon x-show="showPwd2" x-cloak name="eye-slash" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Terms -->
        <p class="text-xs text-gray-400 leading-relaxed">
            Dengan mendaftar, Anda menyetujui <span class="text-gray-600 underline cursor-pointer">Syarat & Ketentuan</span> dan
            <span class="text-gray-600 underline cursor-pointer">Kebijakan Privasi</span> KaryaLokal.
        </p>

        <!-- Submit -->
        <button type="submit" id="register-submit-btn"
                class="kl-btn-primary w-full justify-center py-3.5 text-base">
            <span wire:loading.remove wire:target="register"><x-icon name="user-plus" class="w-5 h-5" /> Buat Akun Gratis</span>
            <span wire:loading wire:target="register" class="flex items-center gap-2">
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
        <p class="text-sm text-gray-400">Sudah punya akun?</p>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <!-- Login Link -->
    <a href="{{ route('login') }}" wire:navigate
       class="kl-btn-secondary w-full justify-center py-3 text-sm">
        Masuk ke Akun Saya
    </a>
</div>
