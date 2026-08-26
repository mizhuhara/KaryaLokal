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
        $query = auth()->user()->orders()->with('seller', 'items.product');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10),
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Pesanan Saya</h1>

            <!-- Filter Tabs -->
            <div class="flex gap-2 mb-8 border-b">
                @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $status => $label)
                    <button
                        wire:click="$set('filter', '{{ $status }}')"
                        class="px-4 py-3 font-medium border-b-2 transition {{ $filter === $status ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600 hover:text-gray-800' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Orders List -->
            @if ($orders->count() > 0)
                <div class="space-y-6">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b">
                                <div>
                                    <p class="font-semibold text-lg">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $order->status === 'ready' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="px-6 py-4 border-b">
                                <p class="font-semibold mb-3">Toko: {{ $order->seller->shop_name }}</p>
                                <div class="space-y-2">
                                    @foreach ($order->items as $item)
                                        <div class="flex justify-between text-sm">
                                            <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div class="px-6 py-4 border-b text-sm bg-gray-50">
                                <p class="font-semibold mb-2">
                                    🚚 {{ $order->delivery_type === 'pickup' ? 'Ambil di Toko' : 'Pengiriman ke Alamat' }}
                                </p>
                                @if ($order->delivery_type === 'delivery' && $order->delivery_address)
                                    <p class="text-gray-600">{{ $order->delivery_address }}</p>
                                @endif
                                @if ($order->notes)
                                    <p class="text-gray-600 mt-2"><strong>Catatan:</strong> {{ $order->notes }}</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="px-6 py-4 flex gap-3 justify-end">
                                <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    💬 Chat Penjual
                                </a>

                                @if ($order->isPending())
                                    <button
                                        wire:click="cancelOrder({{ $order->id }})"
                                        wire:confirm="Batalkan pesanan ini?"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm"
                                    >
                                        Batalkan
                                    </button>
                                @endif

                                @if ($order->isCompleted())
                                    <a href="#" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                        ⭐ Beri Rating
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Review Form for Completed Orders -->
                        @if ($order->isCompleted())
                            <div class="px-6 py-4 bg-gray-50 border-t">
                                @foreach ($order->items as $item)
                                    <div class="mb-4 last:mb-0">
                                        <p class="font-semibold text-sm mb-3">{{ $item->product->name }}</p>
                                        <livewire:components.review-form :orderId="$order->id" :productId="$item->product_id" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">📦</div>
                    <h3 class="text-xl font-semibold mb-2">Belum Ada Pesanan</h3>
                    <p class="text-gray-600 mb-6">Anda belum membuat pesanan apapun</p>
                    <a href="{{ route('products') }}" class="inline-block px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
                        Mulai Berbelanja
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
