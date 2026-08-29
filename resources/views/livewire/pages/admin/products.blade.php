<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $price = '';
    public $stock = '';
    public $category_id = '';
    public $seller_profile_id = '';
    public $is_customizable = false;
    public $is_ready_stock = true;
    public $is_active = true;

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stock = '';
        $this->category_id = '';
        $this->seller_profile_id = '';
        $this->is_customizable = false;
        $this->is_ready_stock = true;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string|max:5000',
            'price'             => 'required|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'category_id'       => 'required|exists:categories,id',
            'seller_profile_id' => 'required|exists:seller_profiles,id',
        ], [
            'name.required'              => 'Nama produk wajib diisi.',
            'price.required'             => 'Harga wajib diisi.',
            'price.numeric'              => 'Harga harus berupa angka.',
            'stock.required'             => 'Stok wajib diisi.',
            'stock.integer'              => 'Stok harus berupa angka.',
            'category_id.required'       => 'Kategori wajib dipilih.',
            'category_id.exists'         => 'Kategori tidak valid.',
            'seller_profile_id.required' => 'Seller wajib dipilih.',
            'seller_profile_id.exists'   => 'Seller tidak valid.',
        ]);

        $data = [
            'name'              => $this->name,
            'description'       => $this->description,
            'price'             => $this->price,
            'stock'             => $this->stock,
            'category_id'       => $this->category_id,
            'seller_profile_id' => $this->seller_profile_id,
            'is_customizable'   => $this->is_customizable,
            'is_ready_stock'    => $this->is_ready_stock,
            'is_active'         => $this->is_active,
        ];

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Produk berhasil diperbarui');
        } else {
            Product::create($data);
            $this->dispatch('notify', message: 'Produk berhasil ditambahkan');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function edit($productId)
    {
        $product = Product::findOrFail($productId);
        $this->editingId          = $productId;
        $this->name               = $product->name;
        $this->description        = $product->description ?? '';
        $this->price              = $product->price;
        $this->stock              = $product->stock;
        $this->category_id        = $product->category_id;
        $this->seller_profile_id  = $product->seller_profile_id;
        $this->is_customizable    = $product->is_customizable;
        $this->is_ready_stock     = $product->is_ready_stock;
        $this->is_active          = $product->is_active;
        $this->showForm           = true;
    }

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with()
    {
        $query = Product::with('sellerProfile', 'category');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return [
            'products'       => $query->orderBy('created_at', 'desc')->paginate(20),
            'categories'     => Category::orderBy('name')->get(),
            'sellerProfiles' => SellerProfile::orderBy('shop_name')->get(),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">📦 Manajemen Produk</h1>
                <p class="text-gray-600 text-sm">Total {{ $products->total() }} produk</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">

            {{-- Search + Tambah Button --}}
            <div class="kl-card p-6 mb-6 flex flex-col sm:flex-row gap-4 items-center">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari produk..."
                    class="flex-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                />
                <button
                    wire:click="openForm"
                    class="whitespace-nowrap px-6 py-2.5 bg-kl-primary text-white rounded-lg hover:bg-kl-primary-dark font-semibold transition shadow"
                >
                    + Tambah Produk
                </button>
            </div>

            {{-- Form Tambah / Edit Produk --}}
            @if ($showForm)
                <div class="kl-card p-6 mb-6">
                    <h3 class="text-lg font-bold font-jakarta mb-5">
                        {{ $editingId ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Nama Produk --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="Masukkan nama produk"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                            />
                            @error('name')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                            <textarea
                                wire:model="description"
                                rows="3"
                                placeholder="Deskripsi produk (opsional)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary resize-none"
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Harga --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="price"
                                placeholder="0"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                            />
                            @error('price')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Stok <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="stock"
                                placeholder="0"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                            />
                            @error('stock')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="category_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary bg-white"
                            >
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Seller --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Seller <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="seller_profile_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary bg-white"
                            >
                                <option value="">-- Pilih Seller --</option>
                                @foreach ($sellerProfiles as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->shop_name }}</option>
                                @endforeach
                            </select>
                            @error('seller_profile_id')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Checkboxes --}}
                        <div class="md:col-span-2 flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    wire:model="is_customizable"
                                    class="w-4 h-4 rounded border-gray-300 text-kl-primary focus:ring-kl-primary"
                                />
                                <span class="text-sm font-medium text-gray-700">Bisa Custom Order</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    wire:model="is_ready_stock"
                                    class="w-4 h-4 rounded border-gray-300 text-kl-primary focus:ring-kl-primary"
                                />
                                <span class="text-sm font-medium text-gray-700">Ready Stock</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    wire:model="is_active"
                                    class="w-4 h-4 rounded border-gray-300 text-kl-primary focus:ring-kl-primary"
                                />
                                <span class="text-sm font-medium text-gray-700">Aktif / Tampilkan Produk</span>
                            </label>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 mt-6 pt-5 border-t border-gray-100">
                        <button
                            wire:click="save"
                            wire:loading.attr="disabled"
                            class="px-8 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold transition disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $editingId ? '💾 Perbarui Produk' : '✅ Simpan Produk' }}
                            </span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <button
                            type="button"
                            wire:click="$set('showForm', false)"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            @endif

            {{-- Products Table --}}
            <div class="kl-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-kl">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Nama</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Seller</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-kl">
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                        @if ($product->is_customizable)
                                            <span class="text-xs text-purple-600 font-medium">✦ Custom</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $product->sellerProfile->shop_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $product->stock }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <a
                                            href="{{ route('seller.product-images', $product->id) }}"
                                            wire:navigate
                                            class="text-purple-600 hover:text-purple-800 font-semibold text-sm transition mr-2"
                                        >
                                            Gambar
                                        </a>
                                        <button
                                            wire:click="edit({{ $product->id }})"
                                            class="text-sky-600 hover:text-sky-800 font-semibold text-sm transition mr-2"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            wire:click="toggleProduct({{ $product->id }})"
                                            class="text-blue-600 hover:text-blue-800 font-semibold text-sm transition mr-2"
                                        >
                                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                        <button
                                            wire:click="deleteProduct({{ $product->id }})"
                                            wire:confirm="Hapus produk ini? Tindakan ini tidak bisa dibatalkan."
                                            class="text-red-600 hover:text-red-800 font-semibold text-sm transition"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <p class="text-4xl mb-2">📦</p>
                                        <p class="font-medium">Belum ada produk{{ $search ? ' yang cocok dengan pencarian' : '' }}.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-kl bg-gray-50">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
