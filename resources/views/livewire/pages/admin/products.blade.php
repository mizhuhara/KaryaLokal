<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function toggleProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);
        $this->dispatch('notify', message: $product->is_active ? 'Produk diaktifkan' : 'Produk dinonaktifkan');
    }

    public function deleteProduct($productId)
    {
        Product::findOrFail($productId)->delete();
        $this->dispatch('notify', message: 'Produk dihapus');
    }

    public function with()
    {
        $query = Product::with('sellerProfile', 'category');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return [
            'products' => $query->orderBy('created_at', 'desc')->paginate(20),
        ];
    }
};

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Manajemen Produk
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6 mb-6">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari produk..."
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                />
            </div>

            <!-- Products Table -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Seller</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $product->id }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-sm">{{ $product->name }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $product->sellerProfile->shop_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 flex gap-2">
                                        <button
                                            wire:click="toggleProduct({{ $product->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm"
                                        >
                                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                        <button
                                            wire:click="deleteProduct({{ $product->id }})"
                                            wire:confirm="Hapus produk ini?"
                                            class="text-red-600 hover:text-red-800 text-sm"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
