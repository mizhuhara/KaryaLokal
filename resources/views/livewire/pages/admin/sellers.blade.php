<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\SellerProfile;

new class extends Component {
    use WithPagination;

    public $filter = 'all';

    public function verifySeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->update(['is_verified' => true, 'verified_at' => now()]);
        $seller->user->update(['role' => 'seller']);
        $this->dispatch('notify', message: 'Seller diverifikasi');
    }

    public function unverifySeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->update(['is_verified' => false, 'verified_at' => null]);
        $this->dispatch('notify', message: 'Verifikasi dicabut');
    }

    public function deleteSeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->user->update(['role' => 'buyer']);
        $seller->delete();
        $this->dispatch('notify', message: 'Seller dihapus');
    }

    public function with()
    {
        $query = SellerProfile::with('user');

        if ($this->filter === 'pending') {
            $query->where('is_verified', false);
        } elseif ($this->filter === 'verified') {
            $query->where('is_verified', true);
        }

        return [
            'sellers' => $query->orderBy('created_at', 'desc')->paginate(20),
        ];
    }
};

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Manajemen Sellers
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6 mb-6">
                <div class="flex gap-4">
                    @foreach (['all' => 'Semua', 'pending' => 'Menunggu Verifikasi', 'verified' => 'Sudah Verifikasi'] as $value => $label)
                        <button
                            wire:click="$set('filter', '{{ $value }}')"
                            class="px-4 py-2 rounded-lg font-semibold {{ $filter === $value ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Sellers List -->
            <div class="space-y-4">
                @foreach ($sellers as $seller)
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
                        <div class="p-6 flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold">{{ $seller->shop_name }}</h3>
                                    @if ($seller->is_verified)
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Terverifikasi</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Menunggu</span>
                                    @endif
                                </div>
                                <p class="text-gray-600 mb-2">{{ $seller->address }}, {{ $seller->city }}, {{ $seller->province }}</p>
                                <p class="text-sm text-gray-500">
                                    User: {{ $seller->user->name }} ({{ $seller->user->email }})
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Produk: {{ $seller->products()->count() }} | Dibuat: {{ $seller->created_at->format('d M Y') }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('seller-store', $seller->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    Lihat
                                </a>
                                @if (!$seller->is_verified)
                                    <button
                                        wire:click="verifySeller({{ $seller->id }})"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold"
                                    >
                                        ✓ Verifikasi
                                    </button>
                                @else
                                    <button
                                        wire:click="unverifySeller({{ $seller->id }})"
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm"
                                    >
                                        Cabut
                                    </button>
                                @endif
                                <button
                                    wire:click="deleteSeller({{ $seller->id }})"
                                    wire:confirm="Hapus seller ini? Semua produk juga akan dihapus."
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $sellers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
