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

    public function mount()
    {
        $user = auth()->user();
        if ($user && $user->latitude && $user->longitude) {
            $this->buyerLat = $user->latitude;
            $this->buyerLng = $user->longitude;
            $this->locationRequested = true;
            $this->loadNearbyProducts();
        }
    }

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

    public function toggleWishlist($productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $exists = $user->wishlists()->where('product_id', $productId)->exists();
        if ($exists) {
            $user->wishlists()->where('product_id', $productId)->delete();
        } else {
            $user->wishlists()->create(['product_id' => $productId]);
        }
        $this->dispatch('notify', message: $exists ? 'Dihapus dari wishlist' : 'Ditambahkan ke wishlist');
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

    <div>
<!-- ══════════════════════════════════════════════════
         HERO SECTION
    ══════════════════════════════════════════════════ -->
    <div class="kl-hero py-20 md:py-28" id="hero">
        <!-- Decorative bg elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-10 right-0 w-[500px] h-[500px] rounded-full opacity-10"
                 style="background: radial-gradient(circle, #F4A261, transparent)"></div>
            <div class="absolute bottom-0 left-1/4 w-[300px] h-[300px] rounded-full opacity-8"
                 style="background: radial-gradient(circle, #E8531D, transparent)"></div>
            <!-- Batik-like dot pattern -->
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 30px 30px;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium mb-6"
                         style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.15)">
                        <x-icon name="sparkles" class="w-4 h-4" /> Marketplace Kerajinan Tangan Indonesia #1
                    </div>

                <!-- Heading -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 leading-tight font-jakarta animate-fade-in-up">
                    Temukan. Pesan.<br>
                    <span class="kl-gradient-text">Dukung Pengrajin Lokal.</span>
                </h1>
                <p class="text-lg md:text-xl text-white/70 mb-10 leading-relaxed animate-fade-in-up"
                   style="animation-delay: 0.1s">
                    Platform terpercaya yang menghubungkan Anda dengan ribuan pengrajin handmade di seluruh Indonesia — berdasarkan lokasi terdekat Anda.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-12 animate-fade-in-up"
                     style="animation-delay: 0.2s">
                    <a href="{{ route('products') }}" wire:navigate
                       class="kl-btn-primary text-base py-3 px-8 justify-center shadow-lg">
                        <x-icon name="shopping-bag" class="w-5 h-5" /> Jelajahi Produk
                    </a>
                    <a href="{{ route('nearby') }}" wire:navigate
                       class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-semibold text-base transition-all duration-200"
                       style="background: rgba(255,255,255,0.12); color: white; border: 2px solid rgba(255,255,255,0.25)">
                        <x-icon name="map-pin" class="w-5 h-5" /> Cari Terdekat
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-3 gap-4 max-w-sm mx-auto animate-fade-in-up"
                     style="animation-delay: 0.3s">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white font-jakarta">500+</p>
                        <p class="text-white/50 text-xs mt-0.5">Pengrajin</p>
                    </div>
                    <div class="text-center" style="border-left: 1px solid rgba(255,255,255,0.15); border-right: 1px solid rgba(255,255,255,0.15)">
                        <p class="text-2xl font-bold text-white font-jakarta">2K+</p>
                        <p class="text-white/50 text-xs mt-0.5">Produk</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white font-jakarta">34</p>
                        <p class="text-white/50 text-xs mt-0.5">Provinsi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         SEARCH BAR (Floating above hero bottom)
    ══════════════════════════════════════════════════ -->
    <div class="max-w-4xl mx-auto px-6 -mt-6 relative z-20" id="search">
        <form action="{{ route('products') }}" method="GET"
              class="bg-white rounded-2xl shadow-xl p-4 flex flex-col sm:flex-row gap-3"
              style="border: 1px solid #F0E8E0; box-shadow: 0 12px 48px rgba(232,83,29,0.12)">
            <div class="flex-1 relative">
                <x-icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input type="text" name="q" placeholder="Cari produk handmade, pengrajin..."
                       class="kl-input pl-10 border-0 bg-gray-50 focus:bg-white text-sm" />
            </div>
            <select name="category" class="kl-input border-0 bg-gray-50 text-sm sm:w-44">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="kl-btn-primary py-3 px-6 text-sm justify-center whitespace-nowrap">
                Cari
            </button>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════
         CATEGORIES
    ══════════════════════════════════════════════════ -->
    @if ($categories->count() > 0)
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="text-center mb-10">
            <h2 class="kl-section-title">Kategori Populer</h2>
            <p class="kl-section-sub">Jelajahi berbagai kategori kerajinan handmade Indonesia</p>
        </div>

        @php
        $categoryIcons = [
            'Bucket Bunga' => '💐', 'Bouquet' => '🌸', 'Bucket Uang' => '💰',
            'Bucket Snack' => '🍫', 'Boneka' => '🧸', 'Hampers' => '🎁',
            'Gift Box' => '📦', 'Crochet' => '🧶', 'Macrame' => '🪡',
            'Resin' => '✨', 'Clay' => '🏺', 'Kayu' => '🪵',
            'Dekorasi' => '🎋', 'Souvenir' => '🎀', 'Wedding' => '💍',
            'Custom' => '🎨',
        ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 kl-stagger">
            @foreach ($categories as $category)
                @php
                    $icon = '🎨';
                    foreach ($categoryIcons as $key => $emoji) {
                        if (str_contains(strtolower($category->name), strtolower($key))) {
                            $icon = $emoji;
                            break;
                        }
                    }
                @endphp
                <a href="{{ route('products', ['category' => $category->id]) }}" wire:navigate
                   class="kl-category-card animate-fade-in-up">
                    <div class="kl-category-icon">{{ $icon }}</div>
                    <p class="text-xs font-semibold text-center text-gray-700 leading-tight">{{ $category->name }}</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ══════════════════════════════════════════════════
         HOW IT WORKS
    ══════════════════════════════════════════════════ -->
    <div class="py-16 bg-white border-b" style="border-color: #F0E8E0">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="kl-section-title">Cara Kerjanya</h2>
                <p class="kl-section-sub">Belanja handmade jadi lebih mudah dalam 3 langkah</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Connector line (desktop only) -->
                <div class="hidden md:block absolute top-12 left-[20%] right-[20%] h-0.5 bg-gradient-to-r from-orange-200 via-orange-400 to-orange-200 z-0"></div>

                <div class="text-center relative z-10">
                    <div class="w-24 h-24 mx-auto mb-5 rounded-3xl flex items-center justify-center relative"
                         style="background: linear-gradient(135deg, #FFF5F2, #FFE8E0); border: 2px solid #FFD4C4">
                        <x-icon name="map-pin" class="w-10 h-10" style="color: #E8531D" />
                        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             style="background: linear-gradient(135deg, #E8531D, #FF7043)">1</div>
                    </div>
                    <h3 class="font-bold text-lg mb-2 font-jakarta">Aktifkan Lokasi</h3>
                    <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">Izinkan akses GPS untuk menemukan pengrajin terdekat di sekitar Anda</p>
                </div>

                <div class="text-center relative z-10">
                    <div class="w-24 h-24 mx-auto mb-5 rounded-3xl flex items-center justify-center relative"
                         style="background: linear-gradient(135deg, #FFF5F2, #FFE8E0); border: 2px solid #FFD4C4">
                        <x-icon name="sparkles" class="w-10 h-10" style="color: #E8531D" />
                        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             style="background: linear-gradient(135deg, #E8531D, #FF7043)">2</div>
                    </div>
                    <h3 class="font-bold text-lg mb-2 font-jakarta">Pilih Produk</h3>
                    <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">Jelajahi katalog produk handmade, baca review, dan pilih yang Anda suka</p>
                </div>

                <div class="text-center relative z-10">
                    <div class="w-24 h-24 mx-auto mb-5 rounded-3xl flex items-center justify-center relative"
                         style="background: linear-gradient(135deg, #FFF5F2, #FFE8E0); border: 2px solid #FFD4C4">
                        <x-icon name="truck" class="w-10 h-10" style="color: #E8531D" />
                        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             style="background: linear-gradient(135deg, #E8531D, #FF7043)">3</div>
                    </div>
                    <h3 class="font-bold text-lg mb-2 font-jakarta">Pesan & Terima</h3>
                    <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">Checkout mudah, bayar aman, dan produk diantar langsung ke rumah Anda</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         PROMO BANNER
    ══════════════════════════════════════════════════ -->
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="relative overflow-hidden rounded-3xl p-8 md:p-12"
             style="background: linear-gradient(135deg, #FF6B35, #E8531D, #D4451A); box-shadow: 0 20px 60px rgba(232,83,29,0.3)">
            <div class="absolute inset-0 pointer-events-none opacity-30"
                 style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22><circle cx=%2230%22 cy=%2230%22 r=%221.5%22 fill=%22white%22 opacity=%220.3%22/></svg>')"></div>
            <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full" style="background: rgba(255,255,255,0.08)"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full" style="background: rgba(255,255,255,0.06)"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold mb-4"
                         style="background: rgba(255,255,255,0.2); color: white">
                        <x-icon name="fire" class="w-4 h-4" /> PROMO SPESIAL
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-3 font-jakarta leading-tight">
                        Gratis Ongkir<br>Sepanjang Agustus!
                    </h2>
                    <p class="text-white/70 text-base mb-6 max-w-md">
                        Berlaku untuk semua produk dari pengrajin terverifikasi. Minimum belanja Rp50.000.
                    </p>
                    <a href="{{ route('products', ['promo' => 'free-shipping']) }}" wire:navigate
                       class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl font-bold bg-white transition-all duration-200 hover:scale-105"
                       style="color: #E8531D; box-shadow: 0 4px 20px rgba(0,0,0,0.15)">
                        <x-icon name="shopping-bag" class="w-5 h-5" /> Belanja Sekarang
                    </a>
                </div>
                <div class="text-center">
                    <div class="text-7xl md:text-8xl font-black text-white/20 font-jakarta">-100%</div>
                    <p class="text-white/50 text-sm font-semibold mt-2">ONGKIR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         NEARBY PRODUCTS (Location-based)
    ══════════════════════════════════════════════════ -->
    <div class="bg-white border-y py-16" style="border-color: #F0E8E0">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <h2 class="kl-section-title flex items-center gap-2">
                        <x-icon name="map-pin" class="w-6 h-6 animate-kl-pulse" style="color: #E8531D" />
                        Produk Terdekat
                    </h2>
                    <p class="kl-section-sub !mb-0">Produk dari pengrajin di sekitar lokasi Anda</p>
                </div>
                @if (!$locationRequested)
                    <button id="activate-location-btn"
                            onclick="navigator.geolocation.getCurrentPosition(
                                pos => { @this.setBuyerLocation(pos.coords.latitude, pos.coords.longitude); },
                                () => @this.locationDenied()
                            )"
                            class="kl-btn-primary text-sm py-2.5 shrink-0">
                        <x-icon name="map-pin" class="w-4 h-4" /> Aktifkan Lokasi
                    </button>
                @else
                    <a href="{{ route('nearby') }}" wire:navigate
                       class="kl-btn-secondary text-sm py-2.5 shrink-0">
                        Lihat Semua <x-icon name="arrow-right" class="w-4 h-4" />
                    </a>
                @endif
            </div>

            @if ($locationRequested && empty($nearbyProducts))
                <div class="py-12 text-center rounded-2xl" style="background: #FFFBF7; border: 2px dashed #F0E8E0">
                    <p class="text-4xl mb-3">🔍</p>
                    <h3 class="text-lg font-semibold text-gray-700 mb-1">Produk terdekat tidak ditemukan</h3>
                    <p class="text-gray-500 text-sm">Coba perluas jangkauan pencarian atau jelajahi semua produk</p>
                    <a href="{{ route('products') }}" wire:navigate class="kl-btn-primary inline-flex mt-4 text-sm py-2">
                        Lihat Semua Produk
                    </a>
                </div>
            @elseif (!empty($nearbyProducts))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 kl-stagger">
                    @foreach ($nearbyProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <!-- Location not yet requested -->
                <div class="py-16 text-center rounded-2xl" style="background: linear-gradient(135deg, #FFFBF7, #FFF5F2); border: 2px dashed rgba(232,83,29,0.15)">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center animate-kl-pulse"
                         style="background: linear-gradient(135deg, #FFE8E0, #FFD4C4)">
                        <x-icon name="map-pin" class="w-8 h-8" style="color: #E8531D" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2 font-jakarta">Temukan Produk di Sekitar Anda</h3>
                    <p class="text-gray-500 mb-6">Izinkan akses lokasi untuk melihat pengrajin terdekat</p>
                    <button id="nearby-location-btn"
                            onclick="navigator.geolocation.getCurrentPosition(
                                pos => { @this.setBuyerLocation(pos.coords.latitude, pos.coords.longitude); },
                                () => @this.locationDenied()
                            )"
                            class="kl-btn-primary">
                        <x-icon name="map-pin" class="w-5 h-5" /> Aktifkan Lokasi Saya
                    </button>
                    <p class="text-xs text-gray-400 mt-3">Lokasi Anda hanya digunakan untuk menghitung jarak</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         FEATURED PRODUCTS
    ══════════════════════════════════════════════════ -->
    @if ($featuredProducts->count() > 0)
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="kl-section-title">✨ Produk Unggulan</h2>
                <p class="kl-section-sub !mb-0">Produk pilihan dari pengrajin terbaik kami</p>
            </div>
            <a href="{{ route('products') }}" wire:navigate class="kl-btn-secondary text-sm py-2.5 shrink-0">
                Lihat Semua →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 kl-stagger">
            @foreach ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
    @endif

    <!-- ══════════════════════════════════════════════════
         TRENDING
    ══════════════════════════════════════════════════ -->
    @if ($trendingProducts->count() > 0)
    <div class="py-16" style="background: linear-gradient(to bottom, #FFF5F2, white)">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <h2 class="kl-section-title flex items-center gap-2">
                        <x-icon name="fire" class="w-6 h-6" style="color: #E8531D" /> Sedang Trending
                    </h2>
                    <p class="kl-section-sub !mb-0">Produk yang paling banyak dilihat minggu ini</p>
                </div>
                <a href="{{ route('products', ['sort' => 'popular']) }}" wire:navigate class="kl-btn-secondary text-sm py-2.5 shrink-0">
                    Lihat Semua →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 kl-stagger">
                @foreach ($trendingProducts as $i => $product)
                    <x-product-card :product="$product" showRank :rank="$loop->iteration" />
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ══════════════════════════════════════════════════
         FEATURED SELLERS
    ══════════════════════════════════════════════════ -->
    @if ($featuredSellers->count() > 0)
    <div class="bg-white border-y py-16" style="border-color: #F0E8E0">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="kl-section-title flex items-center justify-center gap-2">
                    <x-icon name="star" solid class="w-6 h-6" style="color: #F59E0B" /> Pengrajin Unggulan
                </h2>
                <p class="kl-section-sub">Bergabung dengan pengrajin terverifikasi kami</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 kl-stagger">
                @foreach ($featuredSellers as $seller)
                    <a href="{{ route('seller-store', $seller->id) }}" wire:navigate
                       class="kl-card p-6 group animate-fade-in-up">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <!-- Seller avatar -->
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-bold text-white shrink-0 group-hover:scale-110 transition-transform"
                                     style="background: linear-gradient(135deg, #2D6A4F, #40916C)">
                                    {{ strtoupper(substr($seller->shop_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 font-jakarta">{{ $seller->shop_name }}</h3>
                                    <p class="text-xs text-gray-500"><x-icon name="map-pin" class="w-3.5 h-3.5 inline-block" /> {{ $seller->city ?? 'Indonesia' }}</p>
                                </div>
                            </div>
                            <span class="kl-badge kl-badge-green"><x-icon name="shield-check" class="w-3.5 h-3.5 inline-block" /> Verified</span>
                        </div>

                        <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                            {{ $seller->description ?? 'Pengrajin handmade lokal berkualitas.' }}
                        </p>

                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @if ($seller->pickup_available)
                                <span class="kl-badge kl-badge-blue"><x-icon name="home-modern" class="w-3.5 h-3.5 inline-block" /> Pickup</span>
                            @endif
                            @if ($seller->delivery_available)
                                <span class="kl-badge kl-badge-blue"><x-icon name="truck" class="w-3.5 h-3.5 inline-block" /> Delivery</span>
                            @endif
                            @if ($seller->custom_order_available)
                                <span class="kl-badge kl-badge-purple"><x-icon name="sparkles" class="w-3.5 h-3.5 inline-block" /> Custom</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t" style="border-color: #F0E8E0">
                            <p class="text-xs text-gray-500">{{ $seller->products->count() }} Produk</p>
                            <span class="text-sm font-semibold transition-colors group-hover:underline" style="color: #E8531D">
                                Kunjungi Toko →
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ══════════════════════════════════════════════════
         RECOMMENDED PRODUCTS
    ══════════════════════════════════════════════════ -->
    @if ($recommendedProducts->count() > 0)
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="kl-section-title flex items-center gap-2">
                    <x-icon name="sparkles" class="w-6 h-6" style="color: #E8531D" /> Rekomendasi Untuk Anda
                </h2>
                <p class="kl-section-sub !mb-0">Pilihan produk yang mungkin Anda suka</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 kl-stagger">
            @foreach ($recommendedProducts as $product)
                <x-product-card :product="$product" size="small" />
            @endforeach
        </div>
    </div>
    @endif

    <!-- ══════════════════════════════════════════════════
         TESTIMONIALS
    ══════════════════════════════════════════════════ -->
    <div class="py-16 bg-white border-y" style="border-color: #F0E8E0">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="kl-section-title">Apa Kata Mereka?</h2>
                <p class="kl-section-sub">Ribuan pembeli puas sudah berbelanja di KaryaLokal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Testimonial 1 -->
                <div class="kl-card p-6 relative">
                    <div class="flex gap-1 mb-3">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4" fill="#F59E0B" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        "Produknya unik-unik, beda dari yang di marketplace lain. Pengrajinnya juga ramah dan bisa custom. Recommended banget!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             style="background: linear-gradient(135deg, #E8531D, #FF7043)">SA</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">Siti A.</p>
                            <p class="text-xs text-gray-400">Jakarta · 3 minggu lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="kl-card p-6 relative">
                    <div class="flex gap-1 mb-3">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4" fill="#F59E0B" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        "Pertama kali beli crochet di sini, kualitasnya juara! Pengiriman juga cepat. Sekarang udah langganan sama 3 pengrajin berbeda."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             style="background: linear-gradient(135deg, #2D6A4F, #40916C)">RP</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">Rina P.</p>
                            <p class="text-xs text-gray-400">Bandung · 1 minggu lalu</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="kl-card p-6 relative">
                    <div class="flex gap-1 mb-3">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4" fill="#F59E0B" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        "Suka banget fitur terdekatnya, jadi bisa ketemu pengrajin langsung. Hadiah ulang tahun jadi lebih personal dan berkesan!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             style="background: linear-gradient(135deg, #7C3AED, #A78BFA)">DW</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">Dewi W.</p>
                            <p class="text-xs text-gray-400">Surabaya · 5 hari lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         TRUST BADGES + NEWSLETTER
    ══════════════════════════════════════════════════ -->
    <div class="py-16" style="background: #FFFBF7">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Trust Badges -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                         style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5)">
                        <x-icon name="lock-closed" class="w-8 h-8" style="color: #065F46" />
                    </div>
                    <h4 class="font-bold text-sm text-gray-800 font-jakarta">Pembayaran Aman</h4>
                    <p class="text-xs text-gray-500 mt-1">100% Secure Checkout</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                         style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE)">
                        <x-icon name="truck" class="w-8 h-8" style="color: #1D4ED8" />
                    </div>
                    <h4 class="font-bold text-sm text-gray-800 font-jakarta">Pengiriman Cepat</h4>
                    <p class="text-xs text-gray-500 mt-1">Dikirim dalam 1-3 hari</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                         style="background: linear-gradient(135deg, #FFF7ED, #FFEDD5)">
                        <x-icon name="arrow-path" class="w-8 h-8" style="color: #92400E" />
                    </div>
                    <h4 class="font-bold text-sm text-gray-800 font-jakarta">Garansi Retur</h4>
                    <p class="text-xs text-gray-500 mt-1">Kembali jika tidak sesuai</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                         style="background: linear-gradient(135deg, #FDF2F8, #FCE7F3)">
                        <x-icon name="chat-bubble-oval-left" class="w-8 h-8" style="color: #BE185D" />
                    </div>
                    <h4 class="font-bold text-sm text-gray-800 font-jakarta">Chat Langsung</h4>
                    <p class="text-xs text-gray-500 mt-1">Hubungi pengrajin langsung</p>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="bg-white rounded-3xl p-8 md:p-12 text-center"
                 style="border: 2px solid #F0E8E0; box-shadow: 0 8px 40px rgba(0,0,0,0.04)">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #FFF5F2, #FFE8E0)">
                    <x-icon name="gift" class="w-8 h-8" style="color: #E8531D" />
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2 font-jakarta">Dapatkan Info Promo & Produk Baru</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Berlangganan newsletter kami untuk mendapatkan diskon eksklusif dan update produk terbaru.</p>
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" placeholder="Masukkan email Anda..."
                           class="kl-input flex-1 text-sm" />
                    <button class="kl-btn-primary py-3 px-6 text-sm whitespace-nowrap justify-center">
                        Berlangganan →
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-3">Kami hormati privasi Anda. Unsubscribe kapan saja.</p>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════ -->
    <footer class="border-t" style="background: #1A0A00; border-color: rgba(255,255,255,0.05)">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('storage/images/logo/logo-karyalokal.svg') }}" alt="KaryaLokal" class="h-10 w-auto brightness-0 invert">
                    </div>
                    <p class="text-white/50 text-sm leading-relaxed mb-4">
                        Platform marketplace kerajinan tangan Indonesia. Menghubungkan pembeli dengan pengrajin lokal terdekat.
                    </p>
                    <p class="text-white/30 text-xs">© 2026 KaryaLokal. Bangga Produk Lokal.</p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4 font-jakarta">Jelajahi</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('products') }}" wire:navigate class="text-white/50 hover:text-white text-sm transition-colors">Semua Produk</a></li>
                        <li><a href="{{ route('nearby') }}" wire:navigate class="text-white/50 hover:text-white text-sm transition-colors">Terdekat</a></li>
                        <li><a href="{{ route('nearby-map') }}" wire:navigate class="text-white/50 hover:text-white text-sm transition-colors">Peta Pengrajin</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4 font-jakarta">Jual</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('seller.register') }}" wire:navigate class="text-white/50 hover:text-white text-sm transition-colors">Daftar Penjual</a></li>
                        <li><a href="{{ route('login') }}" wire:navigate class="text-white/50 hover:text-white text-sm transition-colors">Masuk</a></li>
                        <li><a href="{{ route('register') }}" wire:navigate class="text-white/50 hover:text-white text-sm transition-colors">Daftar</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t pt-6 flex flex-col sm:flex-row items-center justify-between gap-4" style="border-color: rgba(255,255,255,0.08)">
                <p class="text-white/30 text-xs">Dibuat dengan ❤️ untuk mendukung UMKM Indonesia</p>
                <div class="flex gap-4">
                    <span class="text-white/20 text-xs">Handmade · Lokal · Terpercaya</span>
                </div>
            </div>
        </div>
    </footer>

</div>
