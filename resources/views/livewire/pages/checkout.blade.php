<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\OrderItem;

new class extends Component {
    public $notes = '';
    public $delivery_type = 'pickup';
    public $delivery_address = '';
    public $agreeTerms = false;

    public function mount()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart');
        }
    }

    public function placeOrder()
    {
        $this->validate([
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_type,delivery|string',
            'agreeTerms' => 'accepted',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            $this->dispatch('notify', message: 'Keranjang kosong');
            return;
        }

        $groupedBySeller = collect($cart)->groupBy('seller_id');

        foreach ($groupedBySeller as $sellerId => $items) {
            $total = $items->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'user_id' => auth()->id(),
                'seller_profile_id' => $sellerId,
                'status' => 'pending',
                'total_amount' => $total,
                'notes' => $this->notes,
                'delivery_type' => $this->delivery_type,
                'delivery_address' => $this->delivery_type === 'delivery' ? $this->delivery_address : null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Notify seller
            $seller = \App\Models\SellerProfile::find($sellerId);
            if ($seller && $seller->user) {
                $seller->user->createNotification(
                    'order',
                    'Pesanan Baru!',
                    auth()->user()->name . ' memesan produk senilai Rp ' . number_format($total, 0, ',', '.'),
                    $order
                );
            }
        }

        session()->put('cart', []);
        $this->dispatch('notify', message: 'Pesanan berhasil dibuat!');
        return redirect()->route('buyer.orders');
    }

    public function with()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return [
            'cartItems' => $cart,
            'total' => $total,
            'groupedByStore' => collect($cart)->groupBy('seller_id')->count(),
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Checkout</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form -->
                <div class="lg:col-span-2 space-y-6">
                    <form wire:submit="placeOrder" class="space-y-6">
                        <!-- Delivery Type -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Metode Pengiriman</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer" :class="$wire.delivery_type === 'pickup' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                    <input type="radio" wire:model="delivery_type" value="pickup" class="w-4 h-4" />
                                    <span>Ambil di Toko</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer" :class="$wire.delivery_type === 'delivery' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                    <input type="radio" wire:model="delivery_type" value="delivery" class="w-4 h-4" />
                                    <span>Pengiriman ke Alamat</span>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Address (if delivery selected) -->
                        @if ($delivery_type === 'delivery')
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-bold mb-4">Alamat Pengiriman</h3>
                                <textarea
                                    wire:model="delivery_address"
                                    rows="4"
                                    placeholder="Jalan, No., RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                    class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                                ></textarea>
                                @error('delivery_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Notes -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Catatan untuk Penjual</h3>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                placeholder="Misal: jam berapa bisa diambil, permintaan khusus, dll..."
                                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                            ></textarea>
                        </div>

                        <!-- Terms -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <label class="flex items-start gap-3">
                                <input type="checkbox" wire:model="agreeTerms" class="w-4 h-4 mt-1" />
                                <span class="text-sm">
                                    Saya setuju dengan syarat dan ketentuan pembelian. Produk handmade membutuhkan waktu pembuatan, dan penjual akan menghubungi untuk konfirmasi.
                                </span>
                            </label>
                            @error('agreeTerms') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-lg"
                        >
                            Buat Pesanan
                        </button>
                    </form>
                </div>

                <!-- Summary -->
                <div class="bg-white rounded-lg shadow p-6 h-fit">
                    <h3 class="text-lg font-bold mb-6">Ringkasan Pesanan</h3>

                    <div class="space-y-4 mb-6 pb-6 border-b">
                        @foreach ($cartItems as $item)
                            <div class="text-sm">
                                <p class="font-medium">{{ $item['name'] }}</p>
                                <p class="text-gray-600">{{ $item['quantity'] }}x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                <p class="font-semibold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span>Toko</span>
                            <span>{{ $groupedByStore }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Ongkos Kirim</span>
                            <span>-</span>
                        </div>
                    </div>

                    <div class="flex justify-between text-lg font-bold pt-6 border-t">
                        <span>Total</span>
                        <span class="text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
