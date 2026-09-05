<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    public $seller_profile_id;
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

    public function loadForEdit($id)
    {
        $product = Product::findOrFail($id);
        if ($product->seller_profile_id !== $this->seller_profile_id) {
            abort(403);
        }
        $this->editingId       = $id;
        $this->name            = $product->name;
        $this->description     = $product->description ?? '';
        $this->price           = $product->price;
        $this->stock           = $product->stock;
        $this->category_id     = $product->category_id ?? '';
        $this->is_customizable = $product->is_customizable;
        $this->is_ready_stock  = $product->is_ready_stock;

        $this->dispatch('open-form');
    }

    public function clearForm()
    {
        $this->editingId       = null;
        $this->name            = '';
        $this->description     = '';
        $this->price           = '';
        $this->stock           = '';
        $this->category_id     = '';
        $this->is_customizable = false;
        $this->is_ready_stock  = true;
        $this->resetValidation();
    }

    public function save()
    {
        $validated = $this->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'category_id'   => 'nullable|integer|exists:categories,id',
            'is_customizable' => 'boolean',
            'is_ready_stock'  => 'boolean',
        ]);

        $validated['category_id'] = $validated['category_id'] ?: null;

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            if ($product->seller_profile_id !== $this->seller_profile_id) {
                abort(403);
            }
            $product->update($validated);
            $this->dispatch('notify', message: 'Produk diperbarui');
        } else {
            Product::create([
                ...$validated,
                'seller_profile_id' => $this->seller_profile_id,
            ]);
            $this->dispatch('notify', message: 'Produk berhasil dibuat');
        }

        $this->clearForm();
        $this->dispatch('close-form');
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
            'products'   => Product::where('seller_profile_id', $this->seller_profile_id)->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ];
    }
};

?>

    <div>
{{-- Alpine state untuk kontrol form (client-side, tidak perlu AJAX) --}}
    <div
        x-data="{ open: false, isEdit: false }"
        x-on:open-form.window="open = true"
        x-on:close-form.window="open = false"
        class="min-h-screen bg-kl-warm"
    >
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">📦 Produk Saya</h1>
                <p class="text-gray-600 text-sm">Kelola produk toko Anda</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold font-jakarta text-gray-900">{{ $products->total() }} Produk</h2>
                {{-- Tombol Tambah: Alpine toggle, tidak perlu Livewire AJAX --}}
                <button
                    type="button"
                    x-on:click="open = true; isEdit = false; $wire.clearForm()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-kl-primary text-white text-sm font-semibold hover:bg-kl-primary-dark transition shadow"
                >
                    <span>+</span> Tambah Produk
                </button>
            </div>

            {{-- Form (ditampilkan/disembunyikan oleh Alpine, bukan Livewire) --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="kl-card p-6 sm:p-8 mb-8"
                style="display: none"
            >
                <h3 class="text-xl font-bold font-jakarta mb-1">
                    <span x-show="!isEdit">➕ Tambah Produk Baru</span>
                    <span x-show="isEdit">✏️ Edit Produk</span>
                </h3>
                <p class="text-sm text-gray-500 mb-6">Lengkapi informasi produk Anda di bawah ini</p>

                <form wire:submit="save" class="space-y-5">
                    <div>
                        <label class="kl-label">Nama Produk <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: Tas Anyaman Rotan"
                            class="kl-input"
                        />
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="kl-label">Deskripsi</label>
                        <textarea
                            wire:model="description"
                            rows="4"
                            placeholder="Jelaskan bahan, ukuran, dan keunikan produk Anda..."
                            class="kl-input resize-y"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="kl-label">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                step="1"
                                min="0"
                                wire:model="price"
                                placeholder="250000"
                                class="kl-input"
                            />
                            @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="kl-label">Stok <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                min="0"
                                wire:model="stock"
                                placeholder="10"
                                class="kl-input"
                            />
                            @error('stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="kl-label">Kategori</label>
                        <select wire:model="category_id" class="kl-input">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                        <label class="flex items-center justify-between p-4 rounded-2xl border border-kl cursor-pointer hover:border-kl-primary transition">
                            <div>
                                <p class="font-semibold text-sm text-gray-900">Stok Siap 📦</p>
                                <p class="text-xs text-gray-500">Produk sudah tersedia untuk langsung dibeli</p>
                            </div>
                            <input type="checkbox" wire:model="is_ready_stock" class="sr-only peer" />
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-kl-primary transition peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition"></div>
                        </label>
                        <label class="flex items-center justify-between p-4 rounded-2xl border border-kl cursor-pointer hover:border-kl-primary transition">
                            <div>
                                <p class="font-semibold text-sm text-gray-900">Bisa Custom 🎨</p>
                                <p class="text-xs text-gray-500">Terima pesanan dengan permintaan khusus</p>
                            </div>
                            <input type="checkbox" wire:model="is_customizable" class="sr-only peer" />
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-kl-primary transition peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition"></div>
                        </label>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="kl-btn-primary text-sm justify-center disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">
                                <span x-show="!isEdit">Simpan Produk</span>
                                <span x-show="isEdit">Simpan Perubahan</span>
                            </span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <button
                            type="button"
                            x-on:click="open = false; $wire.clearForm()"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            {{-- Daftar Produk --}}
            @if ($products->count() > 0)
                <div class="kl-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-kl">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Nama</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Harga</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Stok</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Kategori</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-kl">
                                @foreach ($products as $product)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                            <div class="flex gap-1.5 mt-1">
                                                @if ($product->is_ready_stock)
                                                    <span class="kl-badge kl-badge-blue">📦 Ready</span>
                                                @endif
                                                @if ($product->is_customizable)
                                                    <span class="kl-badge kl-badge-purple">🎨 Custom</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm {{ $product->stock > 0 ? 'text-gray-700' : 'text-red-600 font-semibold' }}">
                                                {{ $product->stock > 0 ? $product->stock : 'Habis' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $product->category?->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <a
                                                href="{{ route('seller.product-images', $product->id) }}"
                                                class="text-purple-600 hover:text-purple-800 font-semibold text-sm transition mr-3"
                                            >Gambar</a>
                                            <button
                                                type="button"
                                                x-on:click="isEdit = true; $wire.loadForEdit({{ $product->id }})"
                                                class="text-sky-600 hover:text-sky-800 font-semibold text-sm transition mr-3"
                                            >Edit</button>
                                            <button
                                                wire:click="delete({{ $product->id }})"
                                                wire:confirm="Yakin hapus produk ini?"
                                                class="text-red-600 hover:text-red-800 font-semibold text-sm transition"
                                            >Hapus</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-kl bg-gray-50">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <div class="kl-card p-12 text-center">
                    <div class="text-5xl mb-4">📦</div>
                    <h3 class="kl-section-title">Belum Ada Produk</h3>
                    <p class="text-gray-500 text-sm mb-6">Tambahkan produk pertama Anda untuk mulai berjualan</p>
                    <button
                        type="button"
                        x-on:click="open = true; isEdit = false; $wire.clearForm()"
                        class="kl-btn-primary text-sm"
                    >+ Tambah Produk Pertama</button>
                </div>
            @endif
        </div>
    </div>

</div>
