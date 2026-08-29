<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\CustomOrder;

new class extends Component {
    use WithPagination;

    public $selectedId = null;
    public $quoted_price = '';
    public $seller_notes = '';

    public function selectOrder($id)
    {
        $this->selectedId = $id;
        $order = CustomOrder::findOrFail($id);
        $this->quoted_price = $order->quoted_price ?? '';
        $this->seller_notes = $order->seller_notes ?? '';
    }

    public function quotePrice()
    {
        $this->validate([
            'quoted_price' => 'required|numeric|min:0',
            'seller_notes' => 'nullable|string|max:1000',
        ]);

        $order = CustomOrder::findOrFail($this->selectedId);
        $order->update([
            'status' => 'quoted',
            'quoted_price' => $this->quoted_price,
            'seller_notes' => $this->seller_notes,
        ]);

        $order->buyer->createNotification(
            'custom_order',
            'Custom Order Ditanggapi!',
            $order->seller->shop_name . ' memberikan penawaran untuk: ' . $order->title,
            $order
        );

        $this->dispatch('notify', message: 'Harga berhasil dikirim');
        $this->selectedId = null;
    }

    public function markDiscussing($id)
    {
        CustomOrder::findOrFail($id)->update(['status' => 'discussing']);
        $this->dispatch('notify', message: 'Status diubah ke diskusi');
    }

    public function markCompleted($id)
    {
        CustomOrder::findOrFail($id)->update(['status' => 'completed', 'completed_at' => now()]);
        $this->dispatch('notify', message: 'Custom order selesai');
    }

    public function markRejected($id)
    {
        $order = CustomOrder::findOrFail($id);
        $order->update(['status' => 'rejected']);
        $this->dispatch('notify', message: 'Custom order ditolak');
    }

    public function with()
    {
        $seller = auth()->user()->sellerProfile;
        return [
            'customOrders' => CustomOrder::with('buyer', 'images')
                ->where('seller_id', $seller->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'selectedOrder' => $this->selectedId ? CustomOrder::with('images')->find($this->selectedId) : null,
        ];
    }
};

?>

    <div>
<x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">Custom Order</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($customOrders->count() > 0)
                <div class="space-y-6">
                    @foreach ($customOrders as $order)
                        <div class="bg-white overflow-hidden shadow-card sm:rounded-lg {{ $selectedId === $order->id ? 'ring-2 ring-orange-500' : '' }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold">{{ $order->title }}</h3>
                                        <p class="text-sm text-gray-600">dari {{ $order->buyer->name }} • {{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'discussing' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'quoted' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>

                                <p class="text-gray-700 mb-4">{{ $order->description }}</p>

                                <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                                    <div>
                                        <span class="text-gray-500">Budget:</span>
                                        <p class="font-semibold">{{ $order->budget ? 'Rp ' . number_format($order->budget, 0, ',', '.') : '-' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Deadline:</span>
                                        <p class="font-semibold">{{ $order->deadline ? $order->deadline->format('d M Y') : '-' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Penawaran:</span>
                                        <p class="font-semibold text-orange-600">{{ $order->quoted_price ? 'Rp ' . number_format($order->quoted_price, 0, ',', '.') : '-' }}</p>
                                    </div>
                                </div>

                                @if ($order->images->count() > 0)
                                    <div class="flex gap-2 mb-4">
                                        @foreach ($order->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-20 h-20 object-cover rounded-lg" />
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex gap-2 flex-wrap">
                                    @if ($order->status === 'pending')
                                        <button
                                            wire:click="markDiscussing({{ $order->id }})"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                                        >
                                            💬 Mulai Diskusi
                                        </button>
                                    @endif

                                    @if (in_array($order->status, ['pending', 'discussing']))
                                        <button
                                            wire:click="selectOrder({{ $order->id }})"
                                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold"
                                        >
                                            💰 Kirim Penawaran
                                        </button>
                                        <button
                                            wire:click="markRejected({{ $order->id }})"
                                            wire:confirm="Tolak custom order ini?"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm"
                                        >
                                            ✕ Tolak
                                        </button>
                                    @endif

                                    @if ($order->status === 'quoted')
                                        <button
                                            wire:click="markCompleted({{ $order->id }})"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm"
                                        >
                                            ✓ Selesai
                                        </button>
                                    @endif

                                    <a href="{{ route('chat.user', $order->buyer_id) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                                        💬 Chat
                                    </a>
                                </div>
                            </div>

                            <!-- Quote Form -->
                            @if ($selectedId === $order->id)
                                <div class="border-t p-6 bg-gray-50">
                                    <h4 class="font-bold mb-4">Kirim Penawaran Harga</h4>
                                    <form wire:submit="quotePrice" class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-1">Harga (Rp) *</label>
                                            <input type="number" wire:model="quoted_price" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-400" />
                                            @error('quoted_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-1">Catatan</label>
                                            <textarea wire:model="seller_notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-400"></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">Kirim Penawaran</button>
                                            <button type="button" wire:click="$set('selectedId', null)" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">{{ $customOrders->links() }}</div>
            @else
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-12 text-center">
                    <div class="text-4xl mb-4">🎨</div>
                    <p class="text-gray-600">Belum ada custom order</p>
                </div>
            @endif
        </div>
    </div>

</div>
