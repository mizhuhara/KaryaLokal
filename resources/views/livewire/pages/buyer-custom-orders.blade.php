<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\CustomOrder;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'customOrders' => CustomOrder::with('seller', 'images')
                ->where('buyer_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Custom Order Saya</h1>

            @if ($customOrders->count() > 0)
                <div class="space-y-6">
                    @foreach ($customOrders as $order)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-lg">{{ $order->title }}</h3>
                                    <p class="text-sm text-gray-600">ke {{ $order->seller->shop_name }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status === 'discussing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'quoted' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $order->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="p-6">
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
                                        <span class="text-gray-500">Harga Penawaran:</span>
                                        <p class="font-semibold text-orange-600">{{ $order->quoted_price ? 'Rp ' . number_format($order->quoted_price, 0, ',', '.') : '-' }}</p>
                                    </div>
                                </div>

                                @if ($order->seller_notes)
                                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                        <p class="font-semibold text-sm mb-1">Catatan Penjual:</p>
                                        <p class="text-gray-700 text-sm">{{ $order->seller_notes }}</p>
                                    </div>
                                @endif

                                @if ($order->images->count() > 0)
                                    <div class="flex gap-2 mb-4">
                                        @foreach ($order->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-20 h-20 object-cover rounded-lg" />
                                        @endforeach
                                    </div>
                                @endif

                                @if ($order->status === 'quoted')
                                    <div class="flex gap-2">
                                        <a href="{{ route('chat.user', $order->seller->user_id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">💬 Chat Penjual</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">{{ $customOrders->links() }}</div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">🎨</div>
                    <h3 class="text-xl font-semibold mb-2">Belum Ada Custom Order</h3>
                    <p class="text-gray-600">Ajukan custom order ke penjual favorit Anda</p>
                </div>
            @endif
        </div>
    </div>

</div>
