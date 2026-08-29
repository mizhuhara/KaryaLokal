<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Order;

new class extends Component {
    use WithPagination;

    public $filter = 'all';

    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isPending()) {
            $this->dispatch('notify', message: 'Hanya pesanan pending yang bisa dibatalkan');
            return;
        }

        $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        $this->dispatch('notify', message: 'Pesanan dibatalkan');
    }

    public function with()
    {
        $query = auth()->user()->orders()->with('seller', 'items.product', 'payments');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">📦 Pesanan Saya</h1>
                <p class="text-gray-600 text-sm">Kelola dan lacak semua pesanan Anda</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Filter Tabs -->
            <div class="flex gap-2 mb-8 overflow-x-auto pb-2 kl-scroll -mx-2 px-2">
                @foreach (['all' => '🛒 Semua', 'pending' => '⏳ Menunggu', 'confirmed' => '✅ Dikonfirmasi', 'processing' => '🔄 Diproses', 'ready' => '📦 Siap', 'completed' => '🎉 Selesai', 'cancelled' => '❌ Dibatalkan'] as $status => $label)
                    <button
                        wire:click="$set('filter', '{{ $status }}')"
                        class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 border {{ $filter === $status ? 'border-kl-primary bg-orange-50 text-kl-primary' : 'border-kl bg-white text-gray-600 hover:border-gray-300' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if ($orders->count() > 0)
                <div class="space-y-5">
                    @foreach ($orders as $order)
                        <div class="kl-card overflow-hidden animate-fade-in-up">
                            <!-- Order Header -->
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-kl" style="background: #FFF8F5">
                                <div>
                                    <p class="font-bold text-base font-jakarta">{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : '' }}
                                        {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}
                                        {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800 border border-purple-200' : '' }}
                                        {{ $order->status === 'ready' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                                        {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <p class="text-xl font-bold" style="color: var(--kl-primary)">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="px-6 py-4 border-b border-kl">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-sm">🏪</span>
                                    <p class="font-semibold text-sm font-jakarta">{{ $order->seller->shop_name }}</p>
                                </div>
                                <div class="space-y-2">
                                    @foreach ($order->items as $item)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">{{ $item->product->name }} <span class="text-gray-400">×{{ $item->quantity }}</span></span>
                                            <span class="font-semibold" style="color: var(--kl-primary)">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Payment & Delivery Info -->
                            <div class="px-6 py-4 border-b border-kl grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm bg-gray-50">
                                <div>
                                    <p class="font-semibold mb-1 text-gray-600">💳 Pembayaran</p>
                                    @if ($order->payments->count() > 0)
                                        @foreach ($order->payments as $payment)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                            @if ($payment->payment_method)
                                                <span class="text-gray-600 ml-1">{{ strtoupper($payment->payment_method) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-xs">Belum ada pembayaran</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold mb-1 text-gray-600">🚚 Pengiriman</p>
                                    <p class="text-xs">{{ $order->delivery_type === 'pickup' ? '🏠 Ambil di Toko' : '📦 Dikirim ke alamat' }}</p>
                                    @if ($order->delivery_type === 'delivery' && $order->delivery_address)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $order->delivery_address }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($order->notes)
                                <div class="px-6 py-3 border-b border-kl bg-gray-50 text-xs">
                                    <span class="font-semibold">📝 Catatan:</span> <span class="text-gray-600">{{ $order->notes }}</span>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="px-6 py-4 flex flex-wrap gap-2 justify-end">
                                <a href="{{ route('chat.user', $order->seller->user_id) }}" wire:navigate
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold transition border border-kl-primary text-kl-primary hover:bg-orange-50">
                                    💬 Chat Penjual
                                </a>

                                @if ($order->isPending())
                                    <button
                                        wire:click="cancelOrder({{ $order->id }})"
                                        wire:confirm="Batalkan pesanan ini?"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold transition border border-red-300 text-red-600 hover:bg-red-50"
                                    >
                                        ✕ Batalkan
                                    </button>
                                @endif

                                @if ($order->isCompleted())
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold transition" style="background: var(--kl-secondary); color: white">
                                        ⭐ Selesai
                                    </span>
                                @endif
                            </div>

                            <!-- Review Form -->
                            @if ($order->isCompleted())
                                <div class="px-6 py-5 border-t border-kl" style="background: #FFF8F5">
                                    <p class="text-sm font-bold font-jakarta mb-3">Beri Rating Produk:</p>
                                    @foreach ($order->items as $item)
                                        <div class="mb-4 last:mb-0">
                                            <p class="text-xs font-semibold text-gray-600 mb-2">{{ $item->product->name }}</p>
                                            <livewire:components.review-form :orderId="$order->id" :productId="$item->product_id" />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="kl-card p-12 text-center">
                    <div class="text-6xl mb-4">📦</div>
                    <h3 class="kl-section-title">Belum Ada Pesanan</h3>
                    <p class="text-gray-500 text-sm mb-6">Mulai berbelanja untuk membuat pesanan pertama</p>
                    <a href="{{ route('products') }}" wire:navigate class="kl-btn-primary text-sm py-2.5">
                        🛍️ Mulai Berbelanja
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
