<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Services\RecommendationService;

new class extends Component {
    public $product_id;
    public $quantity = 1;
    public $isInWishlist = false;
    public $isFavorited = false;
    private $product;

    public function mount($product)
    {
        $product = Product::findOrFail($product);

        $this->product_id = $product->id;
        $this->product = $product;
        $this->checkWishlist();
        $this->checkFavorite();
    }

    public function checkFavorite()
    {
        if (auth()->check() && $this->product) {
            $this->isFavorited = auth()->user()->favoriteSellers()
                ->where('seller_profile_id', $this->product->seller_profile_id)
                ->exists();
        }
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($this->product_id);
        $fav = auth()->user()->favoriteSellers()
            ->where('seller_profile_id', $product->seller_profile_id)
            ->first();

        if ($fav) {
            $fav->delete();
            $this->isFavorited = false;
            $this->dispatch('notify', message: 'Dihapus dari favorit');
        } else {
            auth()->user()->favoriteSellers()->create(['seller_profile_id' => $product->seller_profile_id]);
            $this->isFavorited = true;
            $this->dispatch('notify', message: 'Ditambahkan ke favorit');
        }
    }

    public function checkWishlist()
    {
        if (auth()->check()) {
            $this->isInWishlist = auth()->user()->wishlists()
                ->where('product_id', $this->product_id)
                ->exists();
        }
    }

    public function with()
    {
        $product = Product::with('images', 'sellerProfile', 'category')->findOrFail($this->product_id);

        return [
            'product' => $product,
            'similarProducts' => RecommendationService::getSimilarProducts($product, 4),
            'sameSellerProducts' => RecommendationService::getFromSameSeller($product, 4),
        ];
    }

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        $key = "product_{$this->product_id}";

        $product = Product::findOrFail($this->product_id);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $this->quantity;
        } else {
            $cart[$key] = [
                'product_id' => $this->product_id,
                'seller_id' => $product->seller_profile_id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $this->quantity,
                'image' => $product->primaryImage?->image_path,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang');
    }

    public function toggleWishlist()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $wishlist = auth()->user()->wishlists()->where('product_id', $this->product_id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $this->isInWishlist = false;
            $this->dispatch('notify', message: 'Dihapus dari wishlist');
        } else {
            auth()->user()->wishlists()->create(['product_id' => $this->product_id]);
            $this->isInWishlist = true;
            $this->dispatch('notify', message: 'Ditambahkan ke wishlist');
        }
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Breadcrumb -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-3">
                <a href="{{ route('products') }}" wire:navigate class="text-kl-primary hover:underline text-sm font-medium inline-flex items-center gap-1">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Main Product Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Image Gallery - 2 cols -->
                <div class="lg:col-span-2">
                    <div class="kl-card overflow-hidden">
                        @if ($product->images->count() > 0)
                            <div class="bg-white">
                                <img
                                    id="mainImage"
                                    src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-96 md:h-[500px] object-cover"
                                />
                            </div>
                            @if ($product->images->count() > 1)
                                <div class="p-4 bg-gray-50 border-t border-kl flex gap-2 overflow-x-auto">
                                    @foreach ($product->images as $img)
                                        <button
                                            onclick="document.getElementById('mainImage').src = '{{ asset('storage/' . $img->image_path) }}'"
                                            class="shrink-0 h-20 w-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-kl-primary transition"
                                        >
                                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="" class="w-full h-full object-cover" />
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="w-full h-96 md:h-[500px] bg-gradient-to-br from-orange-50 to-amber-50 flex items-center justify-center text-6xl opacity-30">
                                🎨
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Info Sidebar -->
                <div class="lg:col-span-1">
                    <div class="kl-card p-6 sticky top-24">
                        <!-- Category & Title -->
                        <div class="mb-4">
                            @if ($product->category)
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">{{ $product->category->name }}</p>
                            @endif
                            <h1 class="text-2xl font-bold font-jakarta text-gray-800 leading-tight">{{ $product->name }}</h1>
                        </div>

                        <!-- Price -->
                        <div class="mb-6 pb-6 border-b border-kl">
                            <p class="text-3xl font-bold" style="color: var(--kl-primary)">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">💰 Harga tetap</p>
                        </div>

                        <!-- Status Badges -->
                        <div class="mb-6 space-y-2">
                            @if ($product->is_ready_stock)
                                <div class="kl-badge kl-badge-green w-full justify-center py-2">
                                    ✅ Stok Siap
                                </div>
                            @else
                                <div class="kl-badge kl-badge-blue w-full justify-center py-2">
                                    ⏱️ Pre-order
                                </div>
                            @endif
                            @if ($product->is_customizable)
                                <div class="kl-badge kl-badge-purple w-full justify-center py-2">
                                    🎨 Bisa Custom
                                </div>
                            @endif
                        </div>

                        <!-- Stock Info -->
                        <div class="mb-6 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-600 font-semibold">Stok tersedia: <strong>{{ $product->stock }} item</strong></p>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mb-6">
                            <label class="kl-label">Jumlah Pesanan</label>
                            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 w-fit">
                                <button
                                    wire:click="$set('quantity', Math.max(1, $wire.quantity - 1))"
                                    class="kl-btn-ghost px-3 py-2 text-lg"
                                >
                                    −
                                </button>
                                <span class="text-lg font-bold w-12 text-center">{{ $quantity }}</span>
                                <button
                                    wire:click="$set('quantity', $wire.quantity + 1)"
                                    class="kl-btn-ghost px-3 py-2 text-lg"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2 mb-6">
                            <button
                                wire:click="addToCart"
                                class="kl-btn-primary w-full py-3 justify-center text-base"
                            >
                                🛒 Tambah ke Keranjang
                            </button>
                            <button
                                wire:click="toggleWishlist"
                                class="w-full py-3 rounded-xl font-semibold transition border-2 {{ $isInWishlist ? 'border-red-500 text-red-500 bg-red-50 hover:bg-red-100' : 'border-kl-primary text-kl-primary hover:bg-orange-50' }}"
                            >
                                {{ $isInWishlist ? '❤️ Di Wishlist' : '♡ Tambah Wishlist' }}
                            </button>
                        </div>

                        <!-- Chat Button -->
                        <a
                            href="{{ route('chat.user', $product->sellerProfile->user_id) }}" wire:navigate
                            class="w-full py-3 rounded-xl font-semibold transition text-center block"
                            style="background: var(--kl-secondary); color: white"
                        >
                            💬 Chat Penjual
                        </a>
                    </div>
                </div>
            </div>

            <!-- Seller Card -->
            <div class="kl-card p-6 mb-12">
                <h3 class="font-bold text-lg font-jakarta mb-4">👤 Informasi Penjual</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Seller Basic Info -->
                    <div>
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold text-white shrink-0"
                                 style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light))">
                                {{ strtoupper(substr($product->sellerProfile->shop_name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg font-jakarta">{{ $product->sellerProfile->shop_name }}</h3>
                                <p class="text-sm text-gray-600">📍 {{ $product->sellerProfile->city }}, {{ $product->sellerProfile->province }}</p>
                                @if ($product->sellerProfile->is_verified)
                                    <p class="text-xs font-semibold text-green-600 mt-1">✅ Terverifikasi</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-3">Layanan Tersedia:</p>
                        <div class="flex flex-wrap gap-2">
                            @if ($product->sellerProfile->pickup_available)
                                <span class="kl-badge kl-badge-blue">🏠 Pickup</span>
                            @endif
                            @if ($product->sellerProfile->delivery_available)
                                <span class="kl-badge kl-badge-blue">🚚 Delivery</span>
                            @endif
                            @if ($product->sellerProfile->custom_order_available)
                                <span class="kl-badge kl-badge-purple">🎨 Custom</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="flex flex-col gap-2">
                        <button
                            wire:click="toggleFavorite"
                            class="w-full py-2.5 rounded-xl font-semibold transition text-sm {{ $isFavorited ? 'bg-red-500 text-white hover:bg-red-600' : 'border-2 border-red-300 text-red-600 hover:bg-red-50' }}"
                        >
                            {{ $isFavorited ? '💖 Toko Favorit' : '♡ Favorit' }}
                        </button>
                        <a
                            href="{{ route('seller-store', $product->sellerProfile->id) }}" wire:navigate
                            class="w-full py-2.5 rounded-xl font-semibold transition text-center text-sm"
                            style="background: var(--kl-primary); color: white"
                        >
                            Kunjungi Toko →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="kl-card p-8 mb-12">
                <h3 class="kl-section-title mb-4">📝 Deskripsi Produk</h3>
                <div class="prose prose-sm max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="mb-12">
                <livewire:components.review-list :productId="$product->id" />
            </div>

            <!-- Same Seller Products -->
            @if ($sameSellerProducts->count() > 0)
                <div class="mb-12">
                    <h3 class="kl-section-title mb-6">🏪 Produk Lain dari {{ $product->sellerProfile->shop_name }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($sameSellerProducts as $item)
                            <a href="{{ route('product-detail', $item->id) }}" wire:navigate class="kl-product-card group">
                                <div class="relative overflow-hidden h-40 bg-gradient-to-br from-orange-50 to-amber-50">
                                    @if ($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" alt="{{ $item->name }}" class="kl-product-img w-full h-full" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl opacity-20">🎨</div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-xs font-semibold text-gray-500 mb-1 uppercase">{{ $item->category?->name ?? '—' }}</p>
                                    <p class="font-semibold text-gray-800 line-clamp-2 text-sm mb-2 group-hover:text-kl-primary transition">{{ $item->name }}</p>
                                    <p class="font-bold text-sm" style="color: var(--kl-primary)">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Similar Products -->
            @if ($similarProducts->count() > 0)
                <div>
                    <h3 class="kl-section-title mb-6">💡 Produk Serupa</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($similarProducts as $item)
                            <a href="{{ route('product-detail', $item->id) }}" wire:navigate class="kl-product-card group">
                                <div class="relative overflow-hidden h-40 bg-gradient-to-br from-purple-50 to-pink-50">
                                    @if ($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" alt="{{ $item->name }}" class="kl-product-img w-full h-full" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl opacity-20">💡</div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-gray-500 truncate mb-1">{{ $item->sellerProfile->shop_name ?? '—' }}</p>
                                    <p class="font-semibold text-gray-800 line-clamp-2 text-sm mb-2 group-hover:text-kl-primary transition">{{ $item->name }}</p>
                                    <p class="font-bold text-sm" style="color: var(--kl-primary)">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
