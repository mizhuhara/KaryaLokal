<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\SellerProfile;

new class extends Component {
    use WithPagination;

    public $seller_id;
    public $sort = 'newest';

    public function mount($seller)
    {
        $this->seller_id = $seller->id;
    }

    public function with()
    {
        $seller = SellerProfile::findOrFail($this->seller_id);

        $query = $seller->products()->where('is_active', true)->with('primaryImage', 'category');

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
            'seller' => $seller,
            'products' => $query->paginate(12),
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Store Header -->
        <div class="bg-gradient-to-r from-orange-400 to-red-400 text-white py-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex items-center gap-4">
                        @if ($seller->shop_logo)
                            <img src="{{ asset('storage/' . $seller->shop_logo) }}" class="w-20 h-20 object-cover rounded-lg shadow" alt="Logo Toko">
                        @else
                            <div class="w-20 h-20 bg-white/20 rounded-lg flex items-center justify-center text-3xl">🏪</div>
                        @endif
                        <div>
                            <h1 class="text-4xl font-bold mb-2">{{ $seller->shop_name }}</h1>
                            <p class="text-lg opacity-90">{{ $seller->city }}, {{ $seller->province }}</p>
                        @if ($seller->is_verified)
                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-2xl">✅</span>
                                <span>Toko Terverifikasi</span>
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <div class="bg-white/20 backdrop-blur rounded-lg p-6">
                            <h3 class="text-sm opacity-75 mb-2">Tentang Toko</h3>
                            <p class="text-base leading-relaxed">{{ $seller->description ?? 'Tidak ada deskripsi' }}</p>

                            <div class="mt-6 flex gap-4 flex-wrap">
                                @if ($seller->pickup_available)
                                    <div class="flex items-center gap-2">
                                        <span>📍</span>
                                        <span>Pickup tersedia</span>
                                    </div>
                                @endif
                                @if ($seller->delivery_available)
                                    <div class="flex items-center gap-2">
                                        <span>🚚</span>
                                        <span>Delivery tersedia</span>
                                    </div>
                                @endif
                                @if ($seller->custom_order_available)
                                    <div class="flex items-center gap-2">
                                        <span>🎨</span>
                                        <span>Custom order tersedia</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-8 pt-8 border-t border-white/30">
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $seller->products()->where('is_active', true)->count() }}</p>
                        <p class="text-sm opacity-75">Produk</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">4.8</p>
                        <p class="text-sm opacity-75">Rating</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ rand(50, 500) }}</p>
                        <p class="text-sm opacity-75">Pengunjung</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">Produk Toko</h2>
                <select
                    wire:model.live="sort"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                >
                    <option value="newest">Terbaru</option>
                    <option value="popular">Populer</option>
                    <option value="cheapest">Termurah</option>
                    <option value="expensive">Termahal</option>
                </select>
            </div>

            @if ($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @foreach ($products as $product)
                        <a href="{{ route('product-detail', $product->id) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            @if ($product->primaryImage)
                                <img
                                    src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-40 object-cover"
                                />
                            @else
                                <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-400">
                                    Tidak ada gambar
                                </div>
                            @endif
                            <div class="p-4">
                                <p class="text-xs text-gray-500 mb-1">{{ $product->category?->name ?? '-' }}</p>
                                <h3 class="font-semibold text-sm line-clamp-2 mb-2">{{ $product->name }}</h3>
                                <p class="text-orange-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <div class="flex gap-1 mt-2 flex-wrap">
                                    @if ($product->is_customizable)
                                        <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded">Custom</span>
                                    @endif
                                    @if ($product->is_ready_stock)
                                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">Siap</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">📦</div>
                    <h3 class="text-xl font-semibold mb-2">Belum Ada Produk</h3>
                    <p class="text-gray-600">Toko ini belum memiliki produk aktif</p>
                </div>
            @endif
        </div>

        <!-- Contact Section -->
        <div class="bg-white border-t">
            <div class="max-w-7xl mx-auto px-6 py-12">
                <h3 class="text-2xl font-bold mb-8">Hubungi Toko</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold mb-4">Alamat</h4>
                        <p class="text-gray-600">
                            {{ $seller->address }}<br/>
                            {{ $seller->district }}, {{ $seller->city }}<br/>
                            {{ $seller->province }}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Hubungi Kami</h4>
                        <button class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold mb-2">
                            💬 Chat via WhatsApp
                        </button>
                        <button class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                            💭 Kirim Pesan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
