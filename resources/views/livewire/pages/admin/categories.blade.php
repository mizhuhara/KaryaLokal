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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Manajemen Kategori
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Button -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6 mb-6">
                <button
                    wire:click="openForm"
                    class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
                >
                    + Tambah Kategori
                </button>
            </div>

            <!-- Form Modal -->
            @if ($showForm)
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold mb-4">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
                    <form wire:submit="save" class="flex gap-4">
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Nama kategori"
                            class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            Simpan
                        </button>
                        <button type="button" wire:click="$set('showForm', false)" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                    </form>
                </div>
            @endif

            <!-- Categories Table -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Jumlah Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">{{ $category->id }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $category->products_count }}</td>
                                <td class="px-6 py-4 flex gap-2">
                                    <button
                                        wire:click="edit({{ $category->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        wire:click="deleteCategory({{ $category->id }})"
                                        wire:confirm="Hapus kategori ini?"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
