<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\CustomOrder;
use App\Models\CustomOrderImage;
use App\Models\SellerProfile;

new class extends Component {
    use WithFileUploads;

    public $seller_id;
    public $title = '';
    public $description = '';
    public $budget = '';
    public $deadline = '';
    public $uploadedFiles = [];

    public function mount($seller)
    {
        $this->seller_id = SellerProfile::findOrFail($seller)->id;
    }

    public function submit()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'uploadedFiles.*' => 'image|max:5120',
        ]);

        $customOrder = CustomOrder::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $this->seller_id,
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget ?: null,
            'deadline' => $this->deadline ?: null,
        ]);

        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('custom-orders', 'public');
            CustomOrderImage::create([
                'custom_order_id' => $customOrder->id,
                'image_path' => $path,
            ]);
        }

        // Notify seller
        $seller = SellerProfile::find($this->seller_id);
        if ($seller && $seller->user) {
            $seller->user->createNotification(
                'custom_order',
                'Custom Order Baru!',
                auth()->user()->name . ' mengajukan custom order: ' . $this->title,
                $customOrder
            );
        }

        $this->dispatch('notify', message: 'Custom order berhasil dikirim!');
        return redirect()->route('buyer.custom-orders');
    }

    public function with()
    {
        return [
            'seller' => SellerProfile::findOrFail($this->seller_id),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto px-6 py-8">
            <a href="{{ route('seller-store', $seller->id) }}" class="text-orange-600 hover:text-orange-700 mb-4 inline-block">← Kembali ke Toko</a>

            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-3xl font-bold mb-2">Custom Order</h1>
                <p class="text-gray-600 mb-6">Ajukan pesanan custom ke <strong>{{ $seller->shop_name }}</strong></p>

                <form wire:submit="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Judul Pesanan *</label>
                        <input
                            type="text"
                            wire:model="title"
                            placeholder="Misal: Bouquet bunga mawar merah custom"
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Deskripsi & Detail *</label>
                        <textarea
                            wire:model="description"
                            rows="5"
                            placeholder="Jelaskan detail pesanan: ukuran, warna, jumlah, referensi, dll..."
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Budget (Rp)</label>
                            <input
                                type="number"
                                wire:model="budget"
                                placeholder="Estimasi budget"
                                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                            />
                            @error('budget') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Deadline</label>
                            <input
                                type="date"
                                wire:model="deadline"
                                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                            />
                            @error('deadline') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Foto Referensi (Opsional, maks 5MB)</label>
                        <input
                            type="file"
                            wire:model="uploadedFiles"
                            multiple
                            accept="image/*"
                            class="w-full px-4 py-3 border rounded-lg"
                        />
                        @error('uploadedFiles.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        @if (count($uploadedFiles) > 0)
                            <div class="grid grid-cols-3 gap-3 mt-3">
                                @foreach ($uploadedFiles as $file)
                                    <img src="{{ $file->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-lg"
                    >
                        Kirim Custom Order
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
