<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:components.save-location />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            @if (!auth()->user()->isSeller())
            <div class="p-6 sm:p-8 bg-gradient-to-r from-orange-50 to-amber-50 shadow sm:rounded-lg border-l-4" style="border-color: #E8531D">
                <div class="max-w-xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 font-jakarta">Ingin Menjadi Penjual?</h3>
                    <p class="text-gray-600 text-sm mb-4">Daftarkan diri Anda sebagai penjual dan mulai berjualan produk handmade Anda kepada ribuan pembeli di KaryaLokal.</p>
                    <a href="{{ route('seller.register') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-white transition-all duration-200 hover:scale-105"
                       style="background: linear-gradient(135deg, #E8531D, #FF7043); box-shadow: 0 4px 15px rgba(232,83,29,0.3)">
                        🏪 Daftar Sebagai Penjual
                    </a>
                </div>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
