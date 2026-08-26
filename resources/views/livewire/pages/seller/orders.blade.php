<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Order;

new class extends Component {
    use WithPagination;

    public $filter = 'pending';

    public function confirmOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->seller_profile_id !== auth()->user()->sellerProfile?->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            $this->dispatch('notify', message: 'Order tidak bisa dikonfirmasi');
            return;
        }

        $order->update(['status' => 'confirmed']);
        $this->dispatch('notify', message: 'Order dikonfirmasi');
    }

    public function markProcessing($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->seller_profile_id !== auth()->user()->sellerProfile?->id) {
            abort(403);
        }

        $order->update(['status' => 'processing']);
        $this->dispatch('notify', message: 'Order sedang diproses');
    }

    public function markReady($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->seller_profile_id !== auth()->user()->sellerProfile?->id) {
            abort(403);
        }

        $order->update(['status' => 'ready']);
        $this->dispatch('notify', message: 'Order siap diambil/dikirim');
    }

    public function markShipped($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->seller_profile_id !== auth()->user()->sellerProfile?->id) {
            abort(403);
        }

        $order->update(['status' => 'shipped']);
        $this->dispatch('notify', message: 'Order dikirim');
    }

    public function markCompleted($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->seller_profile_id !== auth()->user()->sellerProfile?->id) {
            abort(403);
        }

        $order->update(['status' => 'completed', 'completed_at' => now()]);
        $this->dispatch('notify', message: 'Order selesai');
    }

    public function rejectOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->seller_profile_id !== auth()->user()->sellerProfile?->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            $this->dispatch('notify', message: 'Order tidak bisa ditolak');
            return;
        }

        $order->update(['status' => 'rejected']);
        $this->dispatch('notify', message: 'Order ditolak');
    }

    public function with()
    {
        $seller = auth()->user()->sellerProfile;

        if (!$seller) {
            return ['orders' => collect([])];
        }

        $query = Order::where('seller_profile_id', $seller->id)
            ->with('user', 'items.product');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10),
            'stats' => [
                'pending' => Order::where('seller_profile_id', $seller->id)->where('status', 'pending')->count(),
                'processing' => Order::where('seller_profile_id', $seller->id)->where('status', 'processing')->count(),
                'ready' => Order::where('seller_profile_id', $seller->id)->where('status', 'ready')->count(),
                'completed' => Order::where('seller_profile_id', $seller->id)->where('status', 'completed')->count(),
            ],
        ];
    }
};

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Manajemen Pesanan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Menunggu Konfirmasi</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Sedang Diproses</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['processing'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Siap Diambil/Dikirim</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['ready'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Selesai</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex gap-2 mb-8 border-b bg-white">
                @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $status => $label)
                    <button
                        wire:click="$set('filter', '{{ $status }}')"
                        class="px-4 py-3 font-medium border-b-2 transition {{ $filter === $status ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600 hover:text-gray-800' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Orders -->
            @if ($orders->count() > 0)
                <div class="space-y-6">
                    @foreach ($orders as $order)
                        <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
                            <!-- Header -->
                            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b">
                                <div>
                                    <p class="font-semibold text-lg">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-600">Pembeli: {{ $order->user->name }} | {{ $order->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'processing' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $order->status === 'ready' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="px-6 py-4 border-b">
                                <p class="font-semibold mb-3">Produk:</p>
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
                            <div class="px-6 py-4 flex gap-2 flex-wrap">
                                @if ($order->status === 'pending')
                                    <button
                                        wire:click="confirmOrder({{ $order->id }})"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold"
                                    >
                                        ✓ Terima
                                    </button>
                                    <button
                                        wire:click="rejectOrder({{ $order->id }})"
                                        wire:confirm="Tolak pesanan ini?"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold"
                                    >
                                        ✕ Tolak
                                    </button>
                                @elseif ($order->status === 'confirmed')
                                    <button
                                        wire:click="markProcessing({{ $order->id }})"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-semibold"
                                    >
                                        🔄 Mulai Proses
                                    </button>
                                @elseif ($order->status === 'processing')
                                    <button
                                        wire:click="markReady({{ $order->id }})"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold"
                                    >
                                        📦 Siap
                                    </button>
                                @elseif ($order->status === 'ready')
                                    <button
                                        wire:click="markShipped({{ $order->id }})"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold"
                                    >
                                        🚀 Dikirim
                                    </button>
                                @elseif ($order->status === 'shipped')
                                    <button
                                        wire:click="markCompleted({{ $order->id }})"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold"
                                    >
                                        ✅ Selesai
                                    </button>
                                @endif

                                <a href="#" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                                    💬 Chat
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-12 text-center">
                    <div class="text-4xl mb-4">📭</div>
                    <p class="text-gray-600">Belum ada pesanan</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
