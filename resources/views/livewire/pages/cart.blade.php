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
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang');
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        $key = "product_{$productId}";

        unset($cart[$key]);
        session()->put('cart', $cart);
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
        }
    }

    public function clearCart()
    {
        session()->put('cart', []);
        $this->dispatch('notify', message: 'Keranjang dikosongkan');
    }

    public function with()
    {
        $cart = session()->get('cart', []);

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        $groupedByStore = collect($cart)->groupBy('seller_id');

        return [
            'cartItems' => $cart,
            'cartCount' => count($cart),
            'total' => $total,
            'groupedByStore' => $groupedByStore,
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Keranjang Belanja</h1>

            @if ($cartCount > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Items -->
                    <div class="lg:col-span-2 space-y-6">
                        @foreach ($groupedByStore as $sellerId => $items)
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="bg-gray-100 px-6 py-3 border-b font-semibold">
                                    Toko ({{ $items->count() }} produk)
                                </div>

                                <div class="divide-y">
                                    @foreach ($items as $key => $item)
                                        <div class="p-6 flex gap-4">
                                            @if ($item['image'])
                                                <img
                                                    src="{{ asset('storage/' . $item['image']) }}"
                                                    alt="{{ $item['name'] }}"
                                                    class="w-20 h-20 object-cover rounded-lg"
                                                />
                                            @else
                                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                                    📦
                                                </div>
                                            @endif

                                            <div class="flex-1">
                                                <h3 class="font-semibold mb-2">{{ $item['name'] }}</h3>
                                                <p class="text-orange-600 font-bold mb-4">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>

                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2 border rounded-lg w-fit">
                                                        <button
                                                            wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['quantity'] - 1 }})"
                                                            class="px-3 py-1 hover:bg-gray-100"
                                                        >
                                                            −
                                                        </button>
                                                        <span class="px-4 py-1 border-l border-r">{{ $item['quantity'] }}</span>
                                                        <button
                                                            wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['quantity'] + 1 }})"
                                                            class="px-3 py-1 hover:bg-gray-100"
                                                        >
                                                            +
                                                        </button>
                                                    </div>

                                                    <button
                                                        wire:click="removeFromCart({{ $item['product_id'] }})"
                                                        class="text-red-600 hover:text-red-800 font-semibold"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <p class="text-lg font-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Summary -->
                    <div class="bg-white rounded-lg shadow p-6 h-fit">
                        <h3 class="text-lg font-bold mb-6">Ringkasan Pesanan</h3>

                        <div class="space-y-3 mb-6 pb-6 border-b">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Ongkos Kirim</span>
                                <span>-</span>
                            </div>
                        </div>

                        <div class="flex justify-between text-lg font-bold mb-6">
                            <span>Total</span>
                            <span class="text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout') }}" class="w-full block px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-center mb-2">
                            Lanjut ke Checkout
                        </a>

                        <button
                            wire:click="clearCart"
                            wire:confirm="Kosongkan keranjang?"
                            class="w-full px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold"
                        >
                            Kosongkan Keranjang
                        </button>

                        <a href="{{ route('products') }}" class="block text-center mt-4 text-orange-600 hover:text-orange-700">
                            ← Lanjut Belanja
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">🛒</div>
                    <h3 class="text-xl font-semibold mb-2">Keranjang Kosong</h3>
                    <p class="text-gray-600 mb-6">Mulai berbelanja untuk menambahkan produk ke keranjang</p>
                    <a href="{{ route('products') }}" class="inline-block px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
                        Jelajahi Produk
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
