<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\SellerProfile;
use App\Models\SellerVisit;

new class extends Component {
    use WithPagination;

    public $seller_id;
    public $sort = 'newest';

    public function mount($seller)
    {
        $seller = SellerProfile::findOrFail($seller);

        $this->seller_id = $seller->id;
        $this->trackVisit();
    }

    public function trackVisit()
    {
        SellerVisit::create([
            'seller_profile_id' => $this->seller_id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'page' => 'store',
        ]);
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

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Store Header Banner -->
        <div class="kl-hero py-12 md:py-16 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    <!-- Shop Profile -->
                    <div class="flex items-center gap-4">
                        @if ($seller->shop_logo)
                            <img src="{{ asset('storage/' . $seller->shop_logo) }}" class="w-24 h-24 object-cover rounded-2xl shadow-lg border-2 border-white/30" alt="Logo Toko">
                        @else
                            <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-lg"
                                 style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light)); border: 2px solid rgba(255,255,255,0.2)">
                                {{ strtoupper(substr($seller->shop_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-1 font-jakarta">{{ $seller->shop_name }}</h1>
                            <p class="text-white/70 text-sm">📍 {{ $seller->city }}, {{ $seller->province }}</p>
                            @if ($seller->is_verified)
                                <div class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-400/30">
                                    ✅ Toko Terverifikasi
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- About Shop -->
                    <div class="md:col-span-2">
                        <div class="rounded-2xl p-6 backdrop-blur-md" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12)">
                            <p class="text-white/80 text-sm leading-relaxed mb-4">
                                {{ $seller->description ?? 'Pengrajin handmade lokal berkualitas yang siap melayani pesanan Anda.' }}
                            </p>

                            <!-- Service Badges -->
                            <div class="flex gap-2 flex-wrap">
                                @if ($seller->pickup_available)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-white border border-white/20">
                                        🏠 Pickup
                                    </span>
                                @endif
                                @if ($seller->delivery_available)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-white border border-white/20">
                                        🚚 Delivery
                                    </span>
                                @endif
                                @if ($seller->custom_order_available)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-white border border-white/20">
                                        🎨 Custom Order
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Bar -->
                <div class="grid grid-cols-3 gap-4 mt-8 pt-8 border-t border-white/15 max-w-xl mx-auto md:mx-0">
                    <div class="text-center md:text-left">
                        <p class="text-2xl font-bold text-white font-jakarta">{{ $seller->products()->where('is_active', true)->count() }}</p>
                        <p class="text-white/50 text-xs">Produk Aktif</p>
                    </div>
                    <div class="text-center md:text-left border-x border-white/15 px-4">
                        <p class="text-2xl font-bold text-white font-jakarta">4.8 ⭐</p>
                        <p class="text-white/50 text-xs">Rating Toko</p>
                    </div>
                    <div class="text-center md:text-left">
                        <p class="text-2xl font-bold text-white font-jakarta">100%</p>
                        <p class="text-white/50 text-xs">Handmade</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="kl-section-title">Katalog Produk</h2>
                    <p class="text-gray-500 text-sm">Semua kerajinan buatan {{ $seller->shop_name }}</p>
                </div>
                <select
                    wire:model.live="sort"
                    class="kl-input text-sm sm:w-48"
                >
                    <option value="newest">Terbaru</option>
                    <option value="popular">Populer</option>
                    <option value="cheapest">Termurah</option>
                    <option value="expensive">Termahal</option>
                </select>
            </div>

            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10 kl-stagger">
                    @foreach ($products as $product)
                        <a href="{{ route('product-detail', $product->id) }}" wire:navigate class="kl-product-card group animate-fade-in-up">
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
                                <div class="absolute top-2 right-2 flex flex-col gap-1">
                                    @if ($product->is_customizable)
                                        <span class="kl-badge kl-badge-purple text-[10px]">Custom</span>
                                    @endif
                                    @if ($product->is_ready_stock)
                                        <span class="kl-badge kl-badge-green text-[10px]">Ready</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $product->category?->name ?? '—' }}</p>
                                <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 mb-2 group-hover:text-kl-primary transition font-jakarta">{{ $product->name }}</h3>
                                <p class="font-bold text-base" style="color: var(--kl-primary)">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="kl-card p-12 text-center">
                    <div class="text-5xl mb-3">📦</div>
                    <h3 class="kl-section-title">Belum Ada Produk</h3>
                    <p class="text-gray-500 text-sm">Toko ini belum menambahkan produk ke katalog</p>
                </div>
            @endif
        </div>

        <!-- Contact & Location Section -->
        <div class="bg-white border-t border-kl">
            <div class="max-w-7xl mx-auto px-6 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-bold font-jakarta mb-4 text-gray-800">📍 Lokasi Toko</h3>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            {{ $seller->address }}<br/>
                            {{ $seller->district }}, {{ $seller->city }}<br/>
                            {{ $seller->province }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold font-jakarta mb-4 text-gray-800">💬 Hubungi Penjual</h3>
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('chat.user', $seller->user_id) }}" wire:navigate
                               class="kl-btn-primary justify-center text-sm py-3">
                                💬 Mulai Chat Sekarang
                            </a>
                            @if ($seller->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $seller->phone) }}" target="_blank"
                                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition text-white bg-green-600 hover:bg-green-700">
                                    📱 Hubungi via WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
