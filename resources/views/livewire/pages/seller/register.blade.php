<?php

use App\Enums\UserRole;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public string $shop_name = '';
    public string $description = '';

    public function mount()
    {
        if (Auth::user()->role === UserRole::Seller) {
            $this->redirect(route('seller.dashboard', absolute: false), navigate: true);
        }
    }

    public function register()
    {
        $this->validate([
            'shop_name' => ['required', 'string', 'min:3', 'max:100', 'unique:seller_profiles,shop_name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();

        SellerProfile::create([
            'user_id' => $user->id,
            'shop_name' => $this->shop_name,
            'description' => $this->description,
        ]);

        $user->update([
            'role' => UserRole::Seller->value,
        ]);

        $this->redirect(route('seller.dashboard', absolute: false), navigate: true);
    }
}; ?>

<x-slot name="header">
    <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
        Buka Toko
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
            <div class="p-6 text-neutral-900">
                <form wire:submit="register">
                    <!-- Shop Name -->
                    <div>
                        <x-input-label for="shop_name" :value="__('Nama Toko')" />
                        <x-text-input wire:model="shop_name" id="shop_name" class="block mt-1 w-full" type="text" name="shop_name" required autofocus autocomplete="off" />
                        <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Deskripsi Singkat')" />
                        <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" name="description"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Daftar Sekarang') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
