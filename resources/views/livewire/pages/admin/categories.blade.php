<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    public $showForm = false;
    public $editingId = null;
    public $name = '';

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:255']);

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update(['name' => $this->name]);
            $this->dispatch('notify', message: 'Kategori diperbarui');
        } else {
            Category::create(['name' => $this->name]);
            $this->dispatch('notify', message: 'Kategori dibuat');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->showForm = true;
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Kategori dihapus');
    }

    public function with()
    {
        return [
            'categories' => Category::withCount('products')->orderBy('name')->paginate(20),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">🏷️ Manajemen Kategori</h1>
                <p class="text-gray-600 text-sm">Total {{ $categories->total() }} kategori</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Add Button -->
            <div class="kl-card p-6 mb-6 flex justify-end">
                <button
                    wire:click="openForm"
                    class="px-6 py-2.5 bg-kl-primary text-white rounded-lg hover:bg-kl-primary-dark font-semibold transition shadow"
                >
                    + Tambah Kategori
                </button>
            </div>

            <!-- Form -->
            @if ($showForm)
                <div class="kl-card p-6 mb-6">
                    <h3 class="text-lg font-bold font-jakarta mb-4">{{ $editingId ? '✏️ Edit Kategori' : '➕ Tambah Kategori Baru' }}</h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Nama kategori"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                        />
                        @error('name') <span class="text-red-500 text-sm self-center">{{ $message }}</span> @enderror
                        <div class="flex gap-2">
                            <button wire:click="save" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold transition">
                                Simpan
                            </button>
                            <button type="button" wire:click="$set('showForm', false)" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Categories Table -->
            <div class="kl-card overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-kl">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Jumlah Produk</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kl">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $category->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 bg-kl-warm rounded-full text-xs font-bold text-gray-700">{{ $category->products_count }} produk</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        wire:click="edit({{ $category->id }})"
                                        class="text-sky-600 hover:text-sky-800 font-semibold text-sm transition mr-3"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        wire:click="deleteCategory({{ $category->id }})"
                                        wire:confirm="Hapus kategori ini?"
                                        class="text-red-600 hover:text-red-800 font-semibold text-sm transition"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-kl bg-gray-50">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
