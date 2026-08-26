<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    public $seller_profile_id;
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $description = '';
    public $price = '';
    public $stock = '';
    public $category_id = '';
    public $is_customizable = false;
    public $is_ready_stock = true;

    public function mount()
    {
        $this->seller_profile_id = auth()->user()->sellerProfile?->id;

        if (!$this->seller_profile_id) {
            $this->redirect(route('seller.register'));
        }
    }

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stock = '';
        $this->category_id = '';
        $this->is_customizable = false;
        $this->is_ready_stock = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_customizable' => 'boolean',
            'is_ready_stock' => 'boolean',
        ]);

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            $product->update($validated);
            $this->dispatch('notify', message: 'Produk diperbarui');
        } else {
            Product::create([
                ...$validated,
                'seller_profile_id' => $this->seller_profile_id,
            ]);
            $this->dispatch('notify', message: 'Produk dibuat');
        }

        $this->closeForm();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_profile_id !== $this->seller_profile_id) {
            abort(403);
        }

        $this->editingId = $id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;
        $this->is_customizable = $product->is_customizable;
        $this->is_ready_stock = $product->is_ready_stock;
        $this->showForm = true;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->seller_profile_id !== $this->seller_profile_id) {
            abort(403);
        }

        $product->delete();
        $this->dispatch('notify', message: 'Produk dihapus');
    }

    public function with()
    {
        return [
            'products' => Product::where('seller_profile_id', $this->seller_profile_id)
                ->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ];
    }
};

?>

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Produk Saya</h2>
        <button
            wire:click="openForm"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
            + Tambah Produk
        </button>
    </div>

    @if ($showForm)
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h3 class="text-lg font-semibold mb-4">
                {{ $editingId ? 'Edit Produk' : 'Tambah Produk Baru' }}
            </h3>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Produk</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea
                        wire:model="description"
                        rows="4"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga</label>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="price"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Stok</label>
                        <input
                            type="number"
                            wire:model="stock"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Kategori</label>
                    <select
                        wire:model="category_id"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_ready_stock" />
                        <span class="text-sm">Stok Siap</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_customizable" />
                        <span class="text-sm">Bisa Custom</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Simpan
                    </button>
                    <button
                        type="button"
                        wire:click="closeForm"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($products->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Harga</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Stok</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $product->stock }}</td>
                            <td class="px-4 py-3 text-sm">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm flex gap-2">
                                <button
                                    wire:click="edit({{ $product->id }})"
                                    class="text-blue-600 hover:text-blue-800"
                                >
                                    Edit
                                </button>
                                <a
                                    href="{{ route('seller.product-images', $product->id) }}"
                                    class="text-purple-600 hover:text-purple-800"
                                >
                                    Gambar
                                </a>
                                <button
                                    wire:click="delete({{ $product->id }})"
                                    wire:confirm="Yakin hapus produk ini?"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-4 py-3">
                {{ $products->links() }}
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                Belum ada produk. <button wire:click="openForm" class="text-blue-600 hover:underline">Tambah sekarang</button>
            </div>
        @endif
    </div>
</div>
