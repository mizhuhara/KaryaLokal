<?php

use App\Models\SellerProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public ?SellerProfile $profile;

    public string $shop_name = '';
    public string $description = '';
    public string $address = '';
    public string $province = '';
    public string $city = '';
    public string $district = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public string $operating_hours = '';
    public bool $pickup_available = false;
    public bool $delivery_available = false;
    public bool $custom_order_available = false;

    public function mount()
    {
        $this->profile = Auth::user()->sellerProfile;

        if ($this->profile) {
            $this->shop_name = $this->profile->shop_name;
            $this->description = $this->profile->description ?? '';
            $this->address = $this->profile->address ?? '';
            $this->province = $this->profile->province ?? '';
            $this->city = $this->profile->city ?? '';
            $this->district = $this->profile->district ?? '';
            $this->latitude = $this->profile->latitude ? (float) $this->profile->latitude : null;
            $this->longitude = $this->profile->longitude ? (float) $this->profile->longitude : null;
            $this->operating_hours = $this->profile->operating_hours ?? '';
            $this->pickup_available = $this->profile->pickup_available;
            $this->delivery_available = $this->profile->delivery_available;
            $this->custom_order_available = $this->profile->custom_order_available;
        }
    }

    public function updateProfile()
    {
        $validated = $this->validate([
            'shop_name' => ['required', 'string', 'min:3', 'max:100', 'unique:seller_profiles,shop_name,' . $this->profile->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'operating_hours' => ['nullable', 'string', 'max:255'],
            'pickup_available' => ['boolean'],
            'delivery_available' => ['boolean'],
            'custom_order_available' => ['boolean'],
        ]);

        $this->profile->update($validated);

        $this->dispatch('profile-updated');
    }
}; ?>

<x-slot name="header">
    <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
        Pengaturan Toko
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <div class="p-4 sm:p-8 bg-white shadow-card sm:rounded-lg">
            <header>
                <h2 class="text-lg font-medium text-neutral-900">
                    Informasi Dasar Toko
                </h2>
                <p class="mt-1 text-sm text-neutral-500">
                    Perbarui nama, deskripsi, dan preferensi layanan toko Anda.
                </p>
            </header>

            <form wire:submit="updateProfile" class="mt-6 space-y-6 max-w-xl">
                <div>
                    <x-input-label for="shop_name" :value="__('Nama Toko')" />
                    <x-text-input wire:model="shop_name" id="shop_name" class="block mt-1 w-full" type="text" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('shop_name')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Deskripsi Toko')" />
                    <textarea wire:model="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="operating_hours" :value="__('Jam Operasional')" />
                    <x-text-input wire:model="operating_hours" id="operating_hours" class="block mt-1 w-full" type="text" placeholder="Cth: Senin-Sabtu, 08:00 - 17:00" />
                    <x-input-error class="mt-2" :messages="$errors->get('operating_hours')" />
                </div>

                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-neutral-700">Layanan yang Tersedia</h3>
                    <div class="flex items-center">
                        <input wire:model="pickup_available" id="pickup_available" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <label for="pickup_available" class="ml-2 text-sm text-neutral-600">Ambil di Tempat (Pickup)</label>
                    </div>
                    <div class="flex items-center">
                        <input wire:model="delivery_available" id="delivery_available" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <label for="delivery_available" class="ml-2 text-sm text-neutral-600">Pengiriman (Delivery)</label>
                    </div>
                    <div class="flex items-center">
                        <input wire:model="custom_order_available" id="custom_order_available" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <label for="custom_order_available" class="ml-2 text-sm text-neutral-600">Terima Pesanan Kustom (Custom Order)</label>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-card sm:rounded-lg">
            <header class="mb-6">
                <h2 class="text-lg font-medium text-neutral-900">
                    Lokasi Toko
                </h2>
                <p class="mt-1 text-sm text-neutral-500">
                    Atur alamat dan titik koordinat toko agar pembeli bisa menemukan Anda di peta.
                </p>
            </header>

            <form wire:submit="updateProfile" class="mt-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="address" :value="__('Alamat Lengkap')" />
                            <textarea wire:model="address" id="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="province" :value="__('Provinsi')" />
                                <x-text-input wire:model="province" id="province" class="block mt-1 w-full" type="text" />
                            </div>
                            <div>
                                <x-input-label for="city" :value="__('Kota / Kabupaten')" />
                                <x-text-input wire:model="city" id="city" class="block mt-1 w-full" type="text" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="district" :value="__('Kecamatan')" />
                            <x-text-input wire:model="district" id="district" class="block mt-1 w-full" type="text" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <x-input-label :value="__('Titik Koordinat (Peta)')" />
                        <x-map-picker lat="latitude" lng="longitude" />
                        <div class="flex space-x-4 mt-2 text-xs text-neutral-500">
                            <div>Lat: <span x-data x-text="$wire.latitude"></span></div>
                            <div>Lng: <span x-data x-text="$wire.longitude"></span></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <x-primary-button>{{ __('Simpan Lokasi') }}</x-primary-button>
                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>

    </div>
</div>

