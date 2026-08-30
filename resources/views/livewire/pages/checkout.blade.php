<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PaymentService;

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
        $paymentService = new PaymentService();
        $paymentUrl = null;

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
                $seller->user->notify(new \App\Notifications\NewOrderForSeller($order));
            }

            // Create payment
            $paymentResult = $paymentService->createTransaction($order);
            if ($paymentResult['success'] && $paymentResult['payment_url']) {
                $paymentUrl = $paymentResult['payment_url'];
            }
        }

        session()->put('cart', []);

        if ($paymentUrl) {
            $this->dispatch('notify', message: 'Pesanan dibuat! Silakan bayar.');
            return redirect()->away($paymentUrl);
        } else {
            $this->dispatch('notify', message: 'Pesanan berhasil dibuat!');
            return redirect()->route('buyer.orders');
        }
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

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1 flex items-center gap-2">
                    <x-icon name="credit-card" class="w-7 h-7" style="color: var(--kl-primary)" /> Checkout
                </h1>
                <p class="text-gray-600 text-sm">Selesaikan pesanan Anda</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <form wire:submit="placeOrder">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-5">
                        <!-- Delivery Type -->
                        <div class="kl-card p-6">
                            <h3 class="text-lg font-bold mb-4 font-jakarta flex items-center gap-2">
                                <x-icon name="truck" class="w-5 h-5" style="color: var(--kl-primary)" /> Metode Pengiriman
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-data>
                                <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all duration-200 border-2"
                                       :class="$wire.delivery_type === 'pickup' ? 'border-kl-primary bg-orange-50' : 'border-kl hover:border-gray-300'">
                                    <input type="radio" wire:model="delivery_type" value="pickup" class="w-4 h-4" style="accent-color: var(--kl-primary)" />
                                    <x-icon name="home-modern" class="w-6 h-6 shrink-0" style="color: var(--kl-primary)" />
                                    <div>
                                        <p class="font-semibold text-sm">Ambil di Toko</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Ambil langsung ke penjual</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all duration-200 border-2"
                                       :class="$wire.delivery_type === 'delivery' ? 'border-kl-primary bg-orange-50' : 'border-kl hover:border-gray-300'">
                                    <input type="radio" wire:model="delivery_type" value="delivery" class="w-4 h-4" style="accent-color: var(--kl-primary)" />
                                    <x-icon name="truck" class="w-6 h-6 shrink-0" style="color: var(--kl-primary)" />
                                    <div>
                                        <p class="font-semibold text-sm">Pengiriman ke Alamat</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Dikirim ke alamat Anda</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        @if ($delivery_type === 'delivery')
                            <div class="kl-card p-6">
                                <h3 class="text-lg font-bold mb-4 font-jakarta flex items-center gap-2">
                                    <x-icon name="map-pin" class="w-5 h-5" style="color: var(--kl-primary)" /> Alamat Pengiriman
                                </h3>
                                <textarea
                                    wire:model="delivery_address"
                                    rows="4"
                                    placeholder="Jalan, No., RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                    class="kl-input"
                                ></textarea>
                                @error('delivery_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <!-- Notes -->
                        <div class="kl-card p-6">
                            <h3 class="text-lg font-bold mb-4 font-jakarta flex items-center gap-2">
                                <x-icon name="clipboard-document-list" class="w-5 h-5" style="color: var(--kl-primary)" /> Catatan untuk Penjual
                            </h3>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                placeholder="Misal: jam berapa bisa diambil, permintaan khusus, warna tertentu, dll..."
                                class="kl-input"
                            ></textarea>
                        </div>

                        <!-- Terms -->
                        <div class="kl-card p-6">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" wire:model="agreeTerms" class="w-4 h-4 mt-0.5" style="accent-color: var(--kl-primary)" />
                                <span class="text-sm text-gray-700 leading-relaxed">
                                    Saya setuju dengan <strong>syarat dan ketentuan</strong> pembelian. Produk handmade membutuhkan waktu pembuatan, dan penjual akan menghubungi untuk konfirmasi.
                                </span>
                            </label>
                            @error('agreeTerms') <p class="text-red-500 text-xs mt-2 ml-7">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="kl-card p-6 sticky top-24">
                            <h3 class="text-lg font-bold mb-5 font-jakarta flex items-center gap-2">
                                <x-icon name="receipt-percent" class="w-5 h-5" style="color: var(--kl-primary)" /> Ringkasan Pesanan
                            </h3>

                            <div class="space-y-3 mb-5 pb-5 border-b border-kl">
                                @foreach ($cartItems as $item)
                                    <div class="flex items-center gap-3">
                                        @if ($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" />
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                                <x-icon name="cube" class="w-5 h-5 opacity-30" />
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-800 line-clamp-1">{{ $item['name'] }}</p>
                                            <p class="text-[11px] text-gray-500">{{ $item['quantity'] }}x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                        </div>
                                        <p class="text-xs font-semibold shrink-0" style="color: var(--kl-primary)">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="space-y-2 mb-5">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Toko</span>
                                    <span>{{ $groupedByStore }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Ongkos Kirim</span>
                                    <span>Dihitung oleh penjual</span>
                                </div>
                            </div>

                            <div class="flex justify-between text-lg font-bold pt-5 border-t border-kl mb-6">
                                <span>Total</span>
                                <span style="color: var(--kl-primary)">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            <button
                                type="submit"
                                class="kl-btn-primary w-full py-3 justify-center text-base"
                            >
                                <x-icon name="credit-card" class="w-5 h-5" /> Buat Pesanan
                            </button>

                            <p class="text-center text-[11px] text-gray-400 mt-3">Pembayaran akan diproses melalui Midtrans</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
