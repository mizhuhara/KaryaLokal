<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Order;

new class extends Component {
    use WithPagination;

    public $filter = 'all';
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        $this->dispatch('notify', message: "Status pesanan diperbarui ke $status");
    }

    public function with()
    {
        $query = Order::with(['user', 'seller', 'items.product']);

        if ($this->search) {
            $query->where('order_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(20),
            'statusCounts' => [
                'all' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach (['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $key => $label)
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-4 text-center cursor-pointer hover:shadow-lg transition"
                         wire:click="$set('filter', '{{ $key }}')">
                        <p class="text-2xl font-bold {{ $filter === $key ? 'text-orange-600' : 'text-neutral-900' }}">{{ $statusCounts[$key] }}</p>
                        <p class="text-xs text-neutral-600">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Search -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                <input type="text" wire:model.live="search" placeholder="Cari nomor pesanan atau nama pembeli..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"/>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                @forelse ($orders as $order)
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="font-bold text-lg">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-600">{{ $order->user->name }} • {{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if ($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif ($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif ($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif ($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <span>Toko: <strong>{{ $order->seller->shop_name }}</strong></span>
                                <span class="mx-2">•</span>
                                <span>{{ $order->items->count() }} item</span>
                                <span class="mx-2">•</span>
                                <span class="font-bold text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>

                            @if ($order->status === 'pending')
                                <div class="flex gap-2">
                                    <button wire:click="updateStatus({{ $order->id }}, 'confirmed')" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">Konfirmasi</button>
                                    <button wire:click="updateStatus({{ $order->id }}, 'cancelled')" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Tolak</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-12 text-center">
                        <p class="text-gray-500">Tidak ada pesanan ditemukan</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-center">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
