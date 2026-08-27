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

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Katalog Produk</h1>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar Filter -->
                <div class="bg-white rounded-lg shadow p-6 h-fit">
                    <h3 class="text-lg font-bold mb-4">Filter</h3>

                    <!-- Search -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Cari Produk</label>
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Nama produk..."
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                    </div>

                    <!-- Category -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Kategori</label>
                        <select
                            wire:model.live="category_id"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Harga</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input
                                type="number"
                                wire:model.live="minPrice"
                                placeholder="Min"
                                class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm"
                            />
                            <input
                                type="number"
                                wire:model.live="maxPrice"
                                placeholder="Max"
                                class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm"
                            />
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Rating Minimum</label>
                        <select
                            wire:model.live="minRating"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                            <option value="">Semua Rating</option>
                            <option value="4">4+ Bintang</option>
                            <option value="3">3+ Bintang</option>
                            <option value="2">2+ Bintang</option>
                        </select>
                    </div>

                    <!-- Service Filters -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Ketersediaan</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="isCustomizable" class="rounded text-orange-500">
                                <span class="text-sm">Bisa Custom</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="readyStock" class="rounded text-orange-500" wire:click="$set('preOrder', false)">
                                <span class="text-sm">Stok Siap</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="preOrder" class="rounded text-orange-500" wire:click="$set('readyStock', false)">
                                <span class="text-sm">Pre-order</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">Layanan Penjual</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="pickupAvailable" class="rounded text-orange-500">
                                <span class="text-sm">Pickup</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="deliveryAvailable" class="rounded text-orange-500">
                                <span class="text-sm">Delivery</span>
                            </label>
                        </div>
                    </div>

                    <!-- Distance Filter -->
                    @if ($hasLocation)
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2">Jarak Maksimal</label>
                            <select
                                wire:model.live="maxDistance"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
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
                    <div>
                        <label class="block text-sm font-medium mb-2">Urutkan</label>
                        <select
                            wire:model.live="sort"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                            <option value="newest">Terbaru</option>
                            <option value="popular">Populer</option>
                            <option value="cheapest">Termurah</option>
                            <option value="expensive">Termahal</option>
                            <option value="highest_rating">Rating Tertinggi</option>
                            @if ($hasLocation)
                                <option value="nearest">Terdekat</option>
                            @endif
                        </select>
                    </div>

                    <!-- Location Toggle -->
                    <div class="mt-6">
                        @if (!$hasLocation)
                            <button
                                onclick="navigator.geolocation.getCurrentPosition(
                                    pos => {
                                        @this.setBuyerLocation(pos.coords.latitude, pos.coords.longitude);
                                    },
                                    () => alert('Lokasi tidak dapat diakses')
                                )"
                                class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-sm"
                            >
                                📍 Aktifkan Lokasi
                            </button>
                        @else
                            <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg">
                                <span class="text-sm text-green-800">Lokasi aktif</span>
                                <button
                                    wire:click="$set('buyerLat', null); $set('buyerLng', null)"
                                    class="text-sm text-red-600 hover:underline"
                                >
                                    Matikan
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:col-span-3">
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            @foreach ($products as $product)
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
                                        <p class="text-xs text-gray-500 mb-1">{{ $product->category?->name ?? '-' }}</p>
                                        <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $product->name }}</h3>
                                        <p class="text-orange-600 font-bold text-lg mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                                        <div class="flex items-center justify-between mb-3">
                                            <p class="text-sm text-gray-600">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                                            @if (isset($product->distance) && $product->distance !== null)
                                                <span class="text-xs text-gray-500">{{ number_format($product->distance, 1) }} km</span>
                                            @endif
                                        </div>

                                        @if (isset($product->avg_rating) && $product->avg_rating > 0)
                                            <div class="flex items-center gap-1 mb-2">
                                                <span class="text-yellow-500">★</span>
                                                <span class="text-sm font-medium">{{ number_format($product->avg_rating, 1) }}</span>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if ($product->is_customizable)
                                                <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Custom</span>
                                            @endif
                                            @if ($product->is_ready_stock)
                                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Siap</span>
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
                        <div class="bg-white rounded-lg shadow p-12 text-center">
                            <div class="text-4xl mb-4">🔍</div>
                            <h3 class="text-xl font-semibold mb-2">Produk Tidak Ditemukan</h3>
                            <p class="text-gray-600">Coba ubah filter atau cari dengan kata kunci berbeda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
