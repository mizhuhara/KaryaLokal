<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public function mount()
    {
        if (!session()->has('cart')) {
            session()->put('cart', []);
        }
    }

    public function addToCart($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);

        $key = "product_{$productId}";

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'seller_id' => $product->seller_profile_id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->primaryImage?->image_path,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang');
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        $key = "product_{$productId}";

        unset($cart[$key]);
        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Produk dihapus dari keranjang');
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cart = session()->get('cart', []);
        $key = "product_{$productId}";

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $quantity;
            session()->put('cart', $cart);
            $this->dispatch('cart-updated');
        }
    }

    public function clearCart()
    {
        session()->put('cart', []);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Keranjang dikosongkan');
    }

    public function with()
    {
        $cart = session()->get('cart', []);

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        $groupedByStore = collect($cart)->groupBy('seller_id');

        $sellerIds = collect($cart)->pluck('seller_id')->unique()->filter();
        $sellers = \App\Models\SellerProfile::whereIn('id', $sellerIds)->pluck('shop_name', 'id');

        return [
            'cartItems' => $cart,
            'cartCount' => count($cart),
            'total' => $total,
            'groupedByStore' => $groupedByStore,
            'sellers' => $sellers,
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
                    <x-icon name="shopping-cart" class="w-7 h-7" style="color: var(--kl-primary)" /> Keranjang Belanja
                </h1>
                <p class="text-gray-600 text-sm">{{ $cartCount }} item di keranjang</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            @if ($cartCount > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-5">
                        @foreach ($groupedByStore as $sellerId => $items)
                            <div class="kl-card overflow-hidden">
                                <!-- Store Header -->
                                <div class="px-5 py-3 flex items-center gap-2 border-b border-kl" style="background: #FFF8F5">
                                    <x-icon name="building-storefront" class="w-5 h-5" style="color: var(--kl-primary)" />
                                    <span class="text-sm font-semibold font-jakarta text-gray-700">{{ $sellers[$sellerId] ?? 'Toko' }} ({{ $items->count() }} produk)</span>
                                </div>

                                <div class="divide-y divide-kl">
                                    @foreach ($items as $key => $item)
                                        <div class="p-5 flex gap-4 items-start group">
                                            <!-- Product Image -->
                                            <a href="{{ route('product-detail', $item['product_id']) }}" wire:navigate class="shrink-0">
                                                @if ($item['image'])
                                                    <img
                                                        src="{{ asset('storage/' . $item['image']) }}"
                                                        alt="{{ $item['name'] }}"
                                                        class="w-20 h-20 object-cover rounded-xl"
                                                    />
                                                @else
                                                    <div class="w-20 h-20 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl flex items-center justify-center">
                                                        <x-icon name="cube" class="w-8 h-8 opacity-20" />
                                                    </div>
                                                @endif
                                            </a>

                                            <!-- Product Info -->
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('product-detail', $item['product_id']) }}" wire:navigate>
                                                    <h3 class="font-semibold text-sm text-gray-800 mb-1 line-clamp-2 group-hover:text-kl-primary transition font-jakarta">{{ $item['name'] }}</h3>
                                                </a>
                                                <p class="text-base font-bold mb-3" style="color: var(--kl-primary)">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>

                                                <div class="flex items-center justify-between">
                                                    <!-- Quantity Controls -->
                                                    <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                                                        <button
                                                            wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['quantity'] - 1 }})"
                                                            class="kl-btn-ghost px-2.5 py-1 text-sm"
                                                        >
                                                            −
                                                        </button>
                                                        <span class="text-sm font-bold w-8 text-center">{{ $item['quantity'] }}</span>
                                                        <button
                                                            wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['quantity'] + 1 }})"
                                                            class="kl-btn-ghost px-2.5 py-1 text-sm"
                                                        >
                                                            +
                                                        </button>
                                                    </div>

                                                    <button
                                                        wire:click="removeFromCart({{ $item['product_id'] }})"
                                                        class="text-xs font-semibold text-red-500 hover:text-red-700 transition px-2 py-1 rounded-lg hover:bg-red-50"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="text-right shrink-0">
                                                <p class="text-sm font-bold" style="color: var(--kl-primary)">
                                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="kl-card p-6 sticky top-24">
                            <h3 class="text-lg font-bold mb-6 font-jakarta">Ringkasan Pesanan</h3>

                            <div class="space-y-3 mb-6 pb-6 border-b border-kl">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-semibold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Ongkos Kirim</span>
                                    <span>Dihitung saat checkout</span>
                                </div>
                            </div>

                            <div class="flex justify-between text-lg font-bold mb-6 pt-2">
                                <span>Total</span>
                                <span style="color: var(--kl-primary)">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            <a href="{{ route('checkout') }}" wire:navigate class="kl-btn-primary w-full py-3 justify-center text-center mb-3">
                                Lanjut ke Checkout <x-icon name="arrow-right" class="w-4 h-4" />
                            </a>

                            <button
                                wire:click="clearCart"
                                wire:confirm="Kosongkan seluruh keranjang?"
                                class="w-full py-2.5 rounded-xl font-semibold text-sm transition text-red-600 hover:bg-red-50 border border-red-200"
                            >
                                Kosongkan Keranjang
                            </button>

                            <a href="{{ route('products') }}" wire:navigate class="block text-center mt-4 text-sm font-medium hover:underline" style="color: var(--kl-primary)">
                                <x-icon name="arrow-left" class="w-4 h-4 inline-block align-middle" /> Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="kl-card p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center"
                         style="background: linear-gradient(135deg, #FFF5F2, #FFE8E0)">
                        <x-icon name="shopping-cart" class="w-10 h-10" style="color: var(--kl-primary)" />
                    </div>
                    <h3 class="kl-section-title">Keranjang Kosong</h3>
                    <p class="text-gray-500 text-sm mb-6">Mulai berbelanja untuk menambahkan produk ke keranjang</p>
                    <a href="{{ route('products') }}" wire:navigate class="kl-btn-primary text-sm py-2.5">
                        <x-icon name="shopping-bag" class="w-4 h-4" /> Jelajahi Produk
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
