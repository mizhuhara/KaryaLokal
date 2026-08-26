<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $sort = 'newest';
    public $minPrice = '';
    public $maxPrice = '';

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

    public function with()
    {
        $query = Product::where('is_active', true)
            ->with('primaryImage', 'sellerProfile', 'category');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%");
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

        switch ($this->sort) {
            case 'cheapest':
                $query->orderBy('price', 'asc');
                break;
            case 'expensive':
                $query->orderBy('price', 'desc');
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
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:col-span-3">
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            @foreach ($products as $product)
                                <a href="#" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
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
                                        <p class="text-sm text-gray-600 mb-3">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
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
