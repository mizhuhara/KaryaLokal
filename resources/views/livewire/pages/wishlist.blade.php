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

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1 flex items-center gap-2">
                    <x-icon name="heart" solid class="w-7 h-7" style="color: var(--kl-primary)" /> Wishlist Saya
                </h1>
                <p class="text-gray-600 text-sm">{{ $wishlists->total() }} produk yang Anda simpan</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            @if ($wishlists->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10 kl-stagger">
                    @foreach ($wishlists as $wishlist)
                        @php $product = $wishlist->product; @endphp
                        <div class="kl-product-card group relative animate-fade-in-up">
                            <!-- Remove Button -->
                            <button
                                wire:click="deleteWishlist({{ $wishlist->id }})"
                                class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 shadow-lg opacity-0 group-hover:opacity-100"
                            >
                                ✕
                            </button>

                            <!-- Image -->
                            <a href="{{ route('product-detail', $product->id) }}" wire:navigate>
                                <div class="relative overflow-hidden h-48 bg-gradient-to-br from-orange-50 to-amber-50">
                                    @if ($product->primaryImage)
                                        <img
                                            src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                            alt="{{ $product->name }}"
                                            class="kl-product-img w-full h-full"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl opacity-20">🎨</div>
                                    @endif

                                    @if ($product->is_customizable || $product->is_ready_stock)
                                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                                        @if ($product->is_customizable)
                                            <span class="kl-badge kl-badge-purple text-[10px]">Custom</span>
                                        @endif
                                        @if ($product->is_ready_stock)
                                            <span class="kl-badge kl-badge-green text-[10px]">Ready</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </a>

                            <!-- Content -->
                            <div class="p-4">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $product->category?->name ?? '—' }}</p>
                                <a href="{{ route('product-detail', $product->id) }}" wire:navigate class="block">
                                    <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 mb-2 group-hover:text-kl-primary transition font-jakarta">{{ $product->name }}</h3>
                                </a>
                                <p class="font-bold text-base mb-2" style="color: var(--kl-primary)">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 truncate mb-3">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                                <a href="{{ route('product-detail', $product->id) }}" wire:navigate
                                   class="w-full text-center block kl-btn-primary text-xs py-2 justify-center">
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
                <div class="kl-card p-12 text-center">
                    <div class="text-6xl mb-4">💔</div>
                    <h3 class="kl-section-title">Wishlist Kosong</h3>
                    <p class="text-gray-500 text-sm mb-6">Belum ada produk favorit yang Anda simpan</p>
                    <a href="{{ route('products') }}" wire:navigate class="kl-btn-primary text-sm py-2.5">
                        Jelajahi Katalog Produk
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
