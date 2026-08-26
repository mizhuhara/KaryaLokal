<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Wishlist;

new class extends Component {
    use WithPagination;

    public function deleteWishlist($wishlistId)
    {
        Wishlist::findOrFail($wishlistId)->delete();
        $this->dispatch('notify', message: 'Dihapus dari wishlist');
    }

    public function with()
    {
        return [
            'wishlists' => auth()->user()->wishlists()
                ->with('product.primaryImage', 'product.sellerProfile', 'product.category')
                ->paginate(12),
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Wishlist Saya</h1>

            @if ($wishlists->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @foreach ($wishlists as $wishlist)
                        @php $product = $wishlist->product; @endphp
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden relative group">
                            <a href="{{ route('product-detail', $product->id) }}">
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
                            </a>

                            <button
                                wire:click="deleteWishlist({{ $wishlist->id }})"
                                class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 opacity-0 group-hover:opacity-100 transition"
                            >
                                ✕
                            </button>

                            <div class="p-4">
                                <p class="text-xs text-gray-500 mb-1">{{ $product->category?->name ?? '-' }}</p>
                                <a href="{{ route('product-detail', $product->id) }}" class="font-semibold text-sm line-clamp-2 mb-2 hover:text-orange-600">
                                    {{ $product->name }}
                                </a>
                                <p class="text-orange-600 font-bold mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-600 mb-3">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>

                                <a href="{{ route('product-detail', $product->id) }}" class="w-full inline-block px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 text-center">
                                    Lihat Produk
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-center">
                    {{ $wishlists->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">💔</div>
                    <h3 class="text-xl font-semibold mb-2">Wishlist Kosong</h3>
                    <p class="text-gray-600 mb-6">Tambahkan produk favorit Anda ke wishlist</p>
                    <a href="{{ route('products') }}" class="inline-block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
                        Lihat Katalog Produk
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
