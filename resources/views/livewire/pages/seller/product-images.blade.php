<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductImage;

new class extends Component {
    use WithFileUploads;

    public $product_id;
    public $images = [];
    public $uploadedFiles = [];

    public function mount($productId)
    {
        $this->product_id = $productId;
        $product = Product::findOrFail($productId);

        if ($product->sellerProfile->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function save()
    {
        $this->validate([
            'uploadedFiles.*' => 'image|max:5120',
        ]);

        $product = Product::findOrFail($this->product_id);

        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $product->images()->count() === 0,
            ]);
        }

        $this->uploadedFiles = [];
        $this->dispatch('notify', message: 'Gambar berhasil diupload');
    }

    public function deleteImage($imageId)
    {
        $image = ProductImage::findOrFail($imageId);

        if ($image->product->sellerProfile->user_id !== auth()->id()) {
            abort(403);
        }

        if ($image->is_primary && $image->product->images()->count() > 1) {
            $image->product->images()->where('id', '!=', $image->id)->first()->update(['is_primary' => true]);
        }

        $image->delete();
        $this->dispatch('notify', message: 'Gambar dihapus');
    }

    public function setPrimary($imageId)
    {
        $image = ProductImage::findOrFail($imageId);

        if ($image->product->sellerProfile->user_id !== auth()->id()) {
            abort(403);
        }

        $image->product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        $this->dispatch('notify', message: 'Gambar utama diperbarui');
    }

    public function with()
    {
        return [
            'product' => Product::findOrFail($this->product_id),
            'productImages' => ProductImage::where('product_id', $this->product_id)->orderBy('sort_order')->get(),
        ];
    }
};

?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Gambar Produk: {{ $product->name }}</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Upload Gambar (Maks 5MB per file)</label>
                <input
                    type="file"
                    wire:model="uploadedFiles"
                    multiple
                    accept="image/*"
                    class="w-full px-3 py-2 border rounded-lg"
                />
                @error('uploadedFiles.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if (count($uploadedFiles) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($uploadedFiles as $file)
                        <div class="relative">
                            <img src="{{ $file->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg" />
                            <span class="absolute top-1 right-1 bg-blue-500 text-white text-xs px-2 py-1 rounded">Baru</span>
                        </div>
                    @endforeach
                </div>

                <button
                    type="button"
                    wire:click="save"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                    Simpan Gambar
                </button>
            @endif
        </div>
    </div>

    @if ($productImages->count() > 0)
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Gambar Tersimpan</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($productImages as $img)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-32 object-cover rounded-lg" />

                        @if ($img->is_primary)
                            <span class="absolute top-1 right-1 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Utama</span>
                        @endif

                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">
                            @if (!$img->is_primary)
                                <button
                                    wire:click="setPrimary({{ $img->id }})"
                                    class="px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600"
                                >
                                    Jadikan Utama
                                </button>
                            @endif

                            <button
                                wire:click="deleteImage({{ $img->id }})"
                                wire:confirm="Hapus gambar ini?"
                                class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
