<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $buyerLat = null;
    public $buyerLng = null;
    public $nearbyProducts = [];
    public $locationRequested = false;

    public function setBuyerLocation($lat, $lng)
    {
        $this->buyerLat = $lat;
        $this->buyerLng = $lng;
        $this->locationRequested = true;
        $this->loadNearbyProducts();
    }

    public function locationDenied()
    {
        $this->locationRequested = true;
    }

    public function loadNearbyProducts()
    {
        if (!$this->buyerLat || !$this->buyerLng) return;

        $lat = $this->buyerLat;
        $lng = $this->buyerLng;

        $this->nearbyProducts = Product::where('is_active', true)
            ->join('seller_profiles as sp', 'products.seller_profile_id', '=', 'sp.id')
            ->selectRaw('products.*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(sp.latitude)) *
                    cos(radians(sp.longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(sp.latitude))
                )
            ) as distance', [$lat, $lng, $lat])
            ->whereNotNull('sp.latitude')
            ->whereNotNull('sp.longitude')
            ->with('primaryImage', 'sellerProfile')
            ->orderBy('distance', 'asc')
            ->limit(6)
            ->get();
    }

    public function with()
    {
        return [
            'categories' => Category::orderBy('name')->limit(8)->get(),
            'featuredProducts' => Product::where('is_active', true)
                ->with('primaryImage')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get(),
            'trendingProducts' => RecommendationService::getTrendingProducts(6),
            'recommendedProducts' => RecommendationService::getTrendingProducts(8),
            'featuredSellers' => SellerProfile::where('is_verified', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with('products')
                ->limit(6)
                ->get(),
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-orange-50 to-white">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-400 to-red-400 text-white">
            <div class="max-w-7xl mx-auto px-6 py-16 text-center">
                <h1 class="text-5xl font-bold mb-4">KaryaLokal</h1>
                <p class="text-xl mb-2">Temukan Kerajinan Tangan Lokal Berkualitas</p>
                <p class="text-lg opacity-90 mb-8">Dukung pengrajin lokal Indonesia</p>
                <div class="flex gap-4 justify-center">
                    <a href="#search" class="px-8 py-3 bg-white text-orange-600 rounded-lg font-semibold hover:bg-orange-50">
                        Jelajahi Produk
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 border-2 border-white">
                        Masuk
                    </a>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="max-w-7xl mx-auto px-6 py-8" id="search">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Cari Produk</label>
                        <input
                            type="text"
                            placeholder="Cari nama produk..."
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Kategori</label>
                        <select class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <option>Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">&nbsp;</label>
                        <button class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <h2 class="text-3xl font-bold mb-8">Kategori Populer</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach ($categories as $category)
                    <a href="#" class="text-center p-4 bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="text-4xl mb-2">📦</div>
                        <p class="text-sm font-medium truncate">{{ $category->name }}</p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Featured Products -->
        @if ($featuredProducts->count() > 0)
            <div class="max-w-7xl mx-auto px-6 py-12">
                <h2 class="text-3xl font-bold mb-8">Produk Unggulan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredProducts as $product)
                        <a href="{{ route('product-detail', $product->id) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            @if ($product->primaryImage)
                                <img
                                    src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-48 object-cover"
                                />
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                    Tidak ada gambar
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-orange-600 font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-600 mt-2">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                                <div class="flex items-center mt-3 gap-2">
                                    @if ($product->is_customizable)
                                        <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Custom</span>
                                    @endif
                                    @if ($product->is_ready_stock)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Stok Siap</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Trending Products -->
        @if ($trendingProducts->count() > 0)
            <div class="max-w-7xl mx-auto px-6 py-12">
                <h2 class="text-3xl font-bold mb-8">🔥 Trending</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($trendingProducts as $product)
                        <a href="{{ route('product-detail', $product->id) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            @if ($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" class="w-full h-48 object-cover" />
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-orange-600 font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-600 mt-2">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Featured Sellers -->
        @if ($featuredSellers->count() > 0)
            <div class="max-w-7xl mx-auto px-6 py-12">
                <h2 class="text-3xl font-bold mb-8">⭐ Pengrajin Unggulan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredSellers as $seller)
                        <a href="{{ route('seller-store', $seller->id) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $seller->shop_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $seller->city }}</p>
                                </div>
                                <span class="text-2xl">✅</span>
                            </div>
                            <p class="text-sm text-gray-700 mb-4 line-clamp-2">{{ $seller->description ?? 'Pengrajin handmade lokal' }}</p>
                            <div class="flex gap-2 flex-wrap mb-4">
                                @if ($seller->pickup_available)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Pickup</span>
                                @endif
                                @if ($seller->delivery_available)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Delivery</span>
                                @endif
                                @if ($seller->custom_order_available)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Custom</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $seller->products->count() }} Produk</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Nearby Products -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold">📍 Produk Terdekat</h2>
                @if (!$locationRequested)
                    <button
                        onclick="navigator.geolocation.getCurrentPosition(
                            pos => {
                                @this.setBuyerLocation(pos.coords.latitude, pos.coords.longitude);
                            },
                            () => @this.locationDenied()
                        )"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-sm"
                    >
                        Aktifkan Lokasi
                    </button>
                @endif
            </div>

            @if ($locationRequested && empty($nearbyProducts))
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="text-4xl mb-4">📍</div>
                    <h3 class="text-xl font-semibold mb-2">Produk terdekat tidak ditemukan</h3>
                    <p class="text-gray-600">Coba aktifkan lokasi atau jelajahi produk lainnya</p>
                </div>
            @elseif (!empty($nearbyProducts))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($nearbyProducts as $product)
                        <a href="{{ route('product-detail', $product->id) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            @if ($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" class="w-full h-48 object-cover" />
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-orange-600 font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-sm text-gray-600">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                                    <span class="text-xs text-gray-500">{{ number_format($product->distance, 1) }} km</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="text-4xl mb-4">📍</div>
                    <h3 class="text-xl font-semibold mb-2">Temukan Produk Terdekat</h3>
                    <p class="text-gray-600 mb-4">Aktifkan lokasi untuk melihat produk di sekitar Anda</p>
                    <button
                        onclick="navigator.geolocation.getCurrentPosition(
                            pos => {
                                @this.setBuyerLocation(pos.coords.latitude, pos.coords.longitude);
                            },
                            () => @this.locationDenied()
                        )"
                        class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
                    >
                        📍 Aktifkan Lokasi
                    </button>
                </div>
            @endif
        </div>

        <!-- Recommended Products -->
        @if ($recommendedProducts->count() > 0)
            <div class="max-w-7xl mx-auto px-6 py-12">
                <h2 class="text-3xl font-bold mb-8">💡 Rekomendasi Untuk Anda</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($recommendedProducts as $product)
                        <a href="{{ route('product-detail', $product->id) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            @if ($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" class="w-full h-40 object-cover" />
                            @else
                                <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-sm mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-orange-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Info Section -->
        <div class="bg-gray-100 py-12 mt-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div>
                        <div class="text-4xl mb-4">🎨</div>
                        <h3 class="font-bold text-lg mb-2">Produk Handmade</h3>
                        <p class="text-gray-600">Semua produk dibuat dengan tangan oleh pengrajin profesional</p>
                    </div>
                    <div>
                        <div class="text-4xl mb-4">📍</div>
                        <h3 class="font-bold text-lg mb-2">Cari Terdekat</h3>
                        <p class="text-gray-600">Temukan pengrajin dan produk di sekitar lokasi Anda</p>
                    </div>
                    <div>
                        <div class="text-4xl mb-4">⭐</div>
                        <h3 class="font-bold text-lg mb-2">Terpercaya</h3>
                        <p class="text-gray-600">Rating dan review dari pembeli lain membantu Anda memilih</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="bg-gradient-to-r from-orange-400 to-red-400 text-white rounded-lg p-12 text-center">
                <h2 class="text-3xl font-bold mb-4">Adalah Pengrajin?</h2>
                <p class="text-lg mb-8 opacity-90">Jual produk handmade Anda ke seluruh Indonesia</p>
                <a href="{{ route('seller.register') }}" class="px-8 py-3 bg-white text-orange-600 rounded-lg font-semibold hover:bg-orange-50">
                    Mulai Berjualan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
