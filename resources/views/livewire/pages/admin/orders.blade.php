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

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">🛒 Manajemen Pesanan</h1>
                <p class="text-gray-600 text-sm">Total {{ $orders->total() }} pesanan</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $key => $label)
                    <div class="kl-card p-4 text-center cursor-pointer kl-hover-lift transition"
                         wire:click="$set('filter', '{{ $key }}')">
                        <p class="text-2xl font-bold font-jakarta {{ $filter === $key ? 'text-kl-primary' : 'text-neutral-900' }}">{{ $statusCounts[$key] }}</p>
                        <p class="text-xs text-neutral-600 font-medium">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Search -->
            <div class="kl-card p-6">
                <input type="text" wire:model.live="search" placeholder="Cari nomor pesanan atau nama pembeli..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"/>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                @forelse ($orders as $order)
                    <div class="kl-card p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="font-bold text-lg font-jakarta text-gray-900">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $order->user->name }} • {{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if ($order->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif ($order->status === 'confirmed') bg-blue-100 text-blue-700
                                    @elseif ($order->status === 'completed') bg-green-100 text-green-700
                                    @elseif ($order->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="text-sm text-gray-600">
                                <span>Toko: <strong class="text-gray-800">{{ $order->seller->shop_name }}</strong></span>
                                <span class="mx-2">•</span>
                                <span>{{ $order->items->count() }} item</span>
                                <span class="mx-2">•</span>
                                <span class="font-bold text-kl-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>

                            @if ($order->status === 'pending')
                                <div class="flex gap-2">
                                    <button wire:click="updateStatus({{ $order->id }}, 'confirmed')" class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">Konfirmasi</button>
                                    <button wire:click="updateStatus({{ $order->id }}, 'cancelled')" class="px-4 py-1.5 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition">Tolak</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="kl-card p-12 text-center">
                        <div class="text-4xl mb-3">🔍</div>
                        <p class="text-gray-500">Tidak ada pesanan ditemukan</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-center">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

</div>
