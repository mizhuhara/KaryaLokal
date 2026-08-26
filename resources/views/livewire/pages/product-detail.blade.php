<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public $product_id;
    public $quantity = 1;

    public function mount($product)
    {
        $this->product_id = $product->id;
    }

    public function with()
    {
        return [
            'product' => Product::with('images', 'sellerProfile', 'category')->findOrFail($this->product_id),
        ];
    }

    public function addToCart()
    {
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang');
    }

    public function toggleWishlist()
    {
        $this->dispatch('notify', message: 'Ditambahkan ke wishlist');
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <a href="{{ route('products') }}" class="text-orange-600 hover:text-orange-700 mb-6 inline-flex items-center gap-2">
                ← Kembali ke Katalog
            </a>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Image Gallery -->
                <div class="bg-white rounded-lg shadow p-6">
                    @if ($product->images->count() > 0)
                        <div class="mb-4">
                            <img
                                id="mainImage"
                                src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                alt="{{ $product->name }}"
                                class="w-full h-96 object-cover rounded-lg"
                            />
                        </div>
                        @if ($product->images->count() > 1)
                            <div class="grid grid-cols-4 gap-2">
                                @foreach ($product->images as $img)
                                    <img
                                        src="{{ asset('storage/' . $img->image_path) }}"
                                        alt="{{ $product->name }}"
                                        class="h-20 object-cover rounded-lg cursor-pointer hover:opacity-75"
                                        onclick="document.getElementById('mainImage').src = this.src"
                                    />
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                            Tidak ada gambar
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="mb-4">
                        @if ($product->category)
                            <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                        @endif
                        <h1 class="text-3xl font-bold mt-2">{{ $product->name }}</h1>
                    </div>

                    <!-- Price & Stock -->
                    <div class="border-b pb-4 mb-4">
                        <p class="text-4xl font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            @if ($product->is_ready_stock)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Stok Siap</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Pre-order</span>
                            @endif
                            @if ($product->is_customizable)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">Bisa Custom</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Stok: {{ $product->stock }} tersedia</p>
                    </div>

                    <!-- Seller Info -->
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 mb-3">Dari Toko</h3>
                        <a href="{{ route('seller-store', $product->sellerProfile->id) }}" class="flex items-center justify-between hover:bg-gray-50 p-3 rounded-lg">
                            <div>
                                <p class="font-semibold">{{ $product->sellerProfile->shop_name }}</p>
                                <p class="text-sm text-gray-600">{{ $product->sellerProfile->city }}, {{ $product->sellerProfile->province }}</p>
                            </div>
                            <span class="text-orange-600">→</span>
                        </a>
                    </div>

                    <!-- Quantity & Actions -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Jumlah</label>
                        <div class="flex items-center gap-4">
                            <button
                                wire:click="$set('quantity', Math.max(1, quantity - 1))"
                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                            >
                                −
                            </button>
                            <span class="text-xl font-semibold w-8 text-center">{{ $quantity }}</span>
                            <button
                                wire:click="$set('quantity', quantity + 1)"
                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            wire:click="addToCart"
                            class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
                        >
                            Tambah ke Keranjang
                        </button>
                        <button
                            wire:click="toggleWishlist"
                            class="px-6 py-3 border-2 border-orange-600 text-orange-600 rounded-lg hover:bg-orange-50 font-semibold"
                        >
                            ♥ Wishlist
                        </button>
                    </div>

                    <!-- Chat Button -->
                    <button class="w-full mt-3 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                        💬 Chat Penjual
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow p-6 mt-8">
                <h3 class="text-2xl font-bold mb-4">Deskripsi Produk</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Seller Info Full -->
            <div class="bg-white rounded-lg shadow p-6 mt-8">
                <h3 class="text-2xl font-bold mb-6">Informasi Penjual</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Toko</p>
                        <p class="text-xl font-semibold mb-4">{{ $product->sellerProfile->shop_name }}</p>

                        <p class="text-sm text-gray-500 mb-1">Lokasi</p>
                        <p class="text-lg mb-4">{{ $product->sellerProfile->address }}, {{ $product->sellerProfile->city }}, {{ $product->sellerProfile->province }}</p>

                        <p class="text-sm text-gray-500 mb-1">Layanan</p>
                        <div class="flex gap-2 flex-wrap">
                            @if ($product->sellerProfile->pickup_available)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Pickup</span>
                            @endif
                            @if ($product->sellerProfile->delivery_available)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Delivery</span>
                            @endif
                            @if ($product->sellerProfile->custom_order_available)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Custom Order</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col justify-between">
                        @if ($product->sellerProfile->is_verified)
                            <div class="flex items-center gap-2 p-3 bg-green-50 rounded-lg mb-4">
                                <span class="text-2xl">✅</span>
                                <div>
                                    <p class="font-semibold text-green-900">Terverifikasi</p>
                                    <p class="text-sm text-green-700">Penjual terpercaya</p>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('seller-store', $product->sellerProfile->id) }}" class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-center">
                            Kunjungi Toko
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
