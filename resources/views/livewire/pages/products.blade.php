<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $sort = 'newest';
    public $minPrice = '';
    public $maxPrice = '';
    public $minRating = '';
    public $maxDistance = '';
    public $buyerLat = null;
    public $buyerLng = null;
    public $isCustomizable = null;
    public $pickupAvailable = null;
    public $deliveryAvailable = null;
    public $readyStock = null;
    public $preOrder = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function updatingMinRating()
    {
        $this->resetPage();
    }

    public function updatingMaxDistance()
    {
        $this->resetPage();
    }

    public function updatingIsCustomizable()
    {
        $this->resetPage();
    }

    public function updatingPickupAvailable()
    {
        $this->resetPage();
    }

    public function updatingDeliveryAvailable()
    {
        $this->resetPage();
    }

    public function updatingReadyStock()
    {
        $this->resetPage();
    }

    public function updatingPreOrder()
    {
        $this->resetPage();
    }

    public function setBuyerLocation($lat, $lng)
    {
        $this->buyerLat = $lat;
        $this->buyerLng = $lng;
        $this->resetPage();
    }

    public function with()
    {
        $hasLocation = $this->buyerLat && $this->buyerLng;

        $query = Product::where('is_active', true)
            ->with('primaryImage', 'sellerProfile', 'category');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Service filters
        if ($this->isCustomizable) {
            $query->where('is_customizable', true);
        }
        // Mutually exclusive: readyStock vs preOrder
        if ($this->readyStock && !$this->preOrder) {
            $query->where('is_ready_stock', true);
        } elseif ($this->preOrder && !$this->readyStock) {
            $query->where('is_ready_stock', false);
        }

        // Seller service filters (need join)
        if ($this->pickupAvailable || $this->deliveryAvailable) {
            $query->whereHas('sellerProfile', function ($sq) {
                if ($this->pickupAvailable) {
                    $sq->where('pickup_available', true);
                }
                if ($this->deliveryAvailable) {
                    $sq->where('delivery_available', true);
                }
            });
        }

        // Rating filter
        if ($this->minRating) {
            $query->whereHas('reviews', function ($q) {
                $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [$this->minRating]);
            });
        }

        // Distance: add raw select + optional filter
        if ($hasLocation) {
            $lat = $this->buyerLat;
            $lng = $this->buyerLng;

            $query->join('seller_profiles as sp', 'products.seller_profile_id', '=', 'sp.id')
                ->selectRaw('products.*, (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(sp.latitude)) *
                        cos(radians(sp.longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(sp.latitude))
                    )
                ) as distance', [$lat, $lng, $lat])
                ->whereNotNull('sp.latitude')
                ->whereNotNull('sp.longitude');

            if ($this->maxDistance) {
                $query->having('distance', '<=', $this->maxDistance);
            }
        }

        // Add avg_rating select for all sorts (shows rating on cards)
        $query->leftJoin('reviews as r', 'products.id', '=', 'r.product_id')
            ->selectRaw('products.*, COALESCE(AVG(r.rating), 0) as avg_rating')
            ->groupBy('products.id');

        switch ($this->sort) {
            case 'cheapest':
                $query->orderBy('price', 'asc');
                break;
            case 'expensive':
                $query->orderBy('price', 'desc');
                break;
            case 'nearest':
                if ($hasLocation) {
                    $query->orderBy('distance', 'asc');
                }
                break;
            case 'highest_rating':
                $query->orderBy('avg_rating', 'desc');
                break;
            case 'popular':
                $query->orderBy('id', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
        }

        return [
            'products' => $query->paginate(12),
            'categories' => Category::orderBy('name')->get(),
            'hasLocation' => $hasLocation,
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header Section -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">Katalog Produk Handmade</h1>
                <p class="text-gray-600">Temukan ribuan produk kerajinan tangan berkualitas dari pengrajin Indonesia</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Active Filter Tags + Product Count -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-bold text-gray-800">{{ $products->total() }}</span> produk
                </p>
                <div class="flex flex-wrap gap-2" x-data>
                    @if ($search)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                            "{{ $search }}"
                            <button wire:click="$set('search', '')" class="hover:text-orange-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($category_id)
                        @php $catName = $categories->firstWhere('id', $category_id)?->name ?? ''; @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $catName }}
                            <button wire:click="$set('category_id', '')" class="hover:text-blue-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($minPrice || $maxPrice)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                            Rp {{ $minPrice ? number_format($minPrice, 0, ',', '.') : '0' }} — Rp {{ $maxPrice ? number_format($maxPrice, 0, ',', '.') : '∞' }}
                            <button wire:click="$set('minPrice', ''); $set('maxPrice', '')" class="hover:text-green-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($minRating)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                            ⭐ {{ $minRating }}+ Bintang
                            <button wire:click="$set('minRating', '')" class="hover:text-yellow-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($isCustomizable)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            🎨 Custom
                            <button wire:click="$set('isCustomizable', null)" class="hover:text-purple-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($readyStock)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                            ✅ Ready Stock
                            <button wire:click="$set('readyStock', null)" class="hover:text-green-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($preOrder)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            ⏱️ Pre-order
                            <button wire:click="$set('preOrder', null)" class="hover:text-blue-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($pickupAvailable)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            🏠 Pickup
                            <button wire:click="$set('pickupAvailable', null)" class="hover:text-indigo-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                    @if ($deliveryAvailable)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                            🚚 Delivery
                            <button wire:click="$set('deliveryAvailable', null)" class="hover:text-cyan-900 ml-0.5">&times;</button>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Sidebar Filter -->
                <div class="lg:col-span-1">
                    <div class="kl-card p-6 sticky top-20 max-h-screen overflow-y-auto kl-scroll">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold font-jakarta">🔍 Filter</h3>
                            @if ($search || $category_id || $minPrice || $maxPrice || $minRating || $isCustomizable || $readyStock || $preOrder || $pickupAvailable || $deliveryAvailable)
                                <button wire:click="$set('search', ''); $set('category_id', ''); $set('minPrice', ''); $set('maxPrice', ''); $set('minRating', ''); $set('isCustomizable', null); $set('readyStock', null); $set('preOrder', null); $set('pickupAvailable', null); $set('deliveryAvailable', null)"
                                        class="text-xs font-semibold text-red-500 hover:text-red-700 transition">
                                    Reset All
                                </button>
                            @endif
                        </div>

                        <!-- Search -->
                        <div class="mb-6">
                            <label class="kl-label">Cari Produk</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input
                                    type="text"
                                    wire:model.live="search"
                                    placeholder="Nama, deskripsi..."
                                    class="kl-input text-sm pl-9"
                                />
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-6">
                            <label class="kl-label">Kategori</label>
                            <select
                                wire:model.live="category_id"
                                class="kl-input text-sm"
                            >
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <label class="kl-label">Rentang Harga</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    type="number"
                                    wire:model.live="minPrice"
                                    placeholder="Min"
                                    class="kl-input text-sm"
                                />
                                <input
                                    type="number"
                                    wire:model.live="maxPrice"
                                    placeholder="Max"
                                    class="kl-input text-sm"
                                />
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <button wire:click="$set('minPrice', '0'); $set('maxPrice', '50000')" class="text-[11px] px-2 py-1 rounded-lg bg-gray-100 hover:bg-orange-50 hover:text-orange-700 transition font-medium">&lt; 50rb</button>
                                <button wire:click="$set('minPrice', '50000'); $set('maxPrice', '100000')" class="text-[11px] px-2 py-1 rounded-lg bg-gray-100 hover:bg-orange-50 hover:text-orange-700 transition font-medium">50-100rb</button>
                                <button wire:click="$set('minPrice', '100000'); $set('maxPrice', '300000')" class="text-[11px] px-2 py-1 rounded-lg bg-gray-100 hover:bg-orange-50 hover:text-orange-700 transition font-medium">100-300rb</button>
                                <button wire:click="$set('minPrice', '300000'); $set('maxPrice', '')" class="text-[11px] px-2 py-1 rounded-lg bg-gray-100 hover:bg-orange-50 hover:text-orange-700 transition font-medium">&gt; 300rb</button>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-6">
                            <label class="kl-label">Rating Minimum</label>
                            <div class="space-y-2">
                                @foreach ([4, 3, 2] as $rating)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" wire:model.live="minRating" value="{{ $rating }}" class="w-4 h-4" style="accent-color: var(--kl-primary)">
                                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition flex items-center gap-1">
                                            @for ($i = 0; $i < $rating; $i++)
                                                <svg class="w-3.5 h-3.5" fill="#F59E0B" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                            <span class="ml-0.5">&amp; ke atas</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Service Filters -->
                        <div class="mb-6">
                            <label class="kl-label">Ketersediaan</label>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="isCustomizable" class="w-4 h-4 rounded" style="accent-color: var(--kl-primary)">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">🎨 Bisa Custom</span>
                                </label>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="readyStock" class="w-4 h-4 rounded" style="accent-color: var(--kl-primary)">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">✅ Stok Siap</span>
                                </label>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="preOrder" class="w-4 h-4 rounded" style="accent-color: var(--kl-primary)">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">⏱️ Pre-order</span>
                                </label>
                            </div>
                        </div>

                        <!-- Seller Services -->
                        <div class="mb-6">
                            <label class="kl-label">Layanan Penjual</label>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="pickupAvailable" class="w-4 h-4 rounded" style="accent-color: var(--kl-primary)">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">🏠 Pickup</span>
                                </label>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" wire:model.live="deliveryAvailable" class="w-4 h-4 rounded" style="accent-color: var(--kl-primary)">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">🚚 Delivery</span>
                                </label>
                            </div>
                        </div>

                        <!-- Distance Filter -->
                        @if ($hasLocation)
                            <div class="mb-6">
                                <label class="kl-label">Jarak Maksimal</label>
                                <select
                                    wire:model.live="maxDistance"
                                    class="kl-input text-sm"
                                >
                                    <option value="">Semua Jarak</option>
                                    <option value="5">Maks 5 km</option>
                                    <option value="10">Maks 10 km</option>
                                    <option value="25">Maks 25 km</option>
                                    <option value="50">Maks 50 km</option>
                                </select>
                            </div>
                        @endif

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="kl-label">Urutkan</label>
                            <select
                                wire:model.live="sort"
                                class="kl-input text-sm"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="popular">Populer</option>
                                <option value="cheapest">Termurah</option>
                                <option value="expensive">Termahal</option>
                                <option value="highest_rating">Rating Terbaik</option>
                                @if ($hasLocation)
                                    <option value="nearest">Terdekat</option>
                                @endif
                            </select>
                        </div>

                        <!-- Location Toggle -->
                        <div class="pt-6 border-t border-kl">
                            @if (!$hasLocation)
                                <button
                                    onclick="navigator.geolocation.getCurrentPosition(
                                        pos => {
                                            @this.setBuyerLocation(pos.coords.latitude, pos.coords.longitude);
                                        },
                                        () => alert('Lokasi tidak dapat diakses')
                                    )"
                                    class="kl-btn-primary w-full text-sm py-2.5 justify-center"
                                >
                                    📍 Aktifkan Lokasi
                                </button>
                            @else
                                <div class="flex items-center justify-between bg-green-50 p-3 rounded-xl border border-green-200">
                                    <span class="text-sm font-medium text-green-800">✓ Lokasi aktif</span>
                                    <button
                                        wire:click="$set('buyerLat', null); $set('buyerLng', null)"
                                        class="text-xs font-semibold text-red-600 hover:text-red-700 transition"
                                    >
                                        Matikan
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:col-span-4">
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-10 kl-stagger">
                            @foreach ($products as $product)
                                <a href="{{ route('product-detail', $product->id) }}" wire:navigate class="kl-product-card group animate-fade-in-up">
                                    <!-- Image Container -->
                                    <div class="relative overflow-hidden h-56 bg-gradient-to-br from-orange-50 to-amber-50">
                                        @if ($product->primaryImage)
                                            <img
                                                src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                alt="{{ $product->name }}"
                                                class="kl-product-img w-full h-full transition-transform duration-500 group-hover:scale-110"
                                            />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-5xl opacity-20">🎨</div>
                                        @endif

                                        <!-- Hover Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                                            <span class="px-5 py-2 bg-white rounded-xl text-sm font-bold shadow-lg" style="color: #E8531D">
                                                Lihat Detail →
                                            </span>
                                        </div>

                                        <!-- Badge Overlay -->
                                        @if ($product->is_customizable || $product->is_ready_stock)
                                        <div class="absolute top-3 right-3 flex flex-col gap-2">
                                            @if ($product->is_customizable)
                                                <span class="kl-badge kl-badge-purple text-xs">🎨 Custom</span>
                                            @endif
                                            @if ($product->is_ready_stock)
                                                <span class="kl-badge kl-badge-green text-xs">✅ Ready</span>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Distance Badge -->
                                        @if (isset($product->distance) && $product->distance !== null)
                                        <div class="absolute bottom-3 left-3">
                                            <span class="kl-badge kl-badge-orange text-xs">📍 {{ number_format($product->distance, 1) }} km</span>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="p-4">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ $product->category?->name ?? '—' }}</p>
                                        <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 font-jakarta group-hover:text-kl-primary transition">{{ $product->name }}</h3>

                                        <!-- Price -->
                                        <p class="text-lg font-bold mb-3" style="color: var(--kl-primary)">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>

                                        <!-- Seller & Rating -->
                                        <div class="flex items-center justify-between pt-3 border-t border-kl">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-medium text-gray-600 truncate">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</span>
                                            </div>
                                            @if (isset($product->avg_rating) && $product->avg_rating > 0)
                                                <div class="flex items-center gap-0.5">
                                                    <span class="text-xs font-semibold">{{ number_format($product->avg_rating, 1) }}</span>
                                                    <span class="text-xs">⭐</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="kl-card p-12 text-center">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="kl-section-title">Produk Tidak Ditemukan</h3>
                            <p class="text-gray-600 mb-6">Coba ubah filter atau cari dengan kata kunci berbeda</p>
                            <a href="{{ route('home') }}" wire:navigate class="kl-btn-primary text-sm py-2.5">
                                ← Kembali ke Beranda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
