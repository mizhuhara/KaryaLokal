<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Review;
use App\Models\ReviewImage;

new class extends Component {
    use WithFileUploads;

    public $order_id;
    public $product_id;
    public $rating = 5;
    public $comment = '';
    public $uploadedFiles = [];
    public $showForm = false;

    public function mount($orderId, $productId)
    {
        $this->order_id = $orderId;
        $this->product_id = $productId;

        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $this->rating = $existing->rating;
            $this->comment = $existing->comment;
        }
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'uploadedFiles.*' => 'image|max:5120',
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $this->product_id,
            ],
            [
                'order_id' => $this->order_id,
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]
        );

        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('reviews', 'public');
            ReviewImage::create([
                'review_id' => $review->id,
                'image_path' => $path,
            ]);
        }

        $this->uploadedFiles = [];
        $this->dispatch('notify', message: 'Review berhasil disimpan');
        $this->showForm = false;
    }

    public function with()
    {
        return [
            'review' => Review::with('images')->where('user_id', auth()->id())
                ->where('product_id', $this->product_id)
                ->first(),
        ];
    }
};

?>

<div class="bg-white rounded-lg shadow p-6">
    @if ($review)
        <div class="space-y-4">
            <h4 class="font-semibold">Rating Anda</h4>
            <div class="flex items-center gap-2 mb-4">
                <div class="flex gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="text-2xl">{{ $i <= $review->rating ? '⭐' : '☆' }}</span>
                    @endfor
                </div>
                <span class="text-lg font-bold">{{ $review->rating }}/5</span>
            </div>
            @if ($review->comment)
                <p class="text-gray-700">{{ $review->comment }}</p>
            @endif
            @if ($review->images->count() > 0)
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($review->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-32 object-cover rounded-lg" />
                    @endforeach
                </div>
            @endif
            <button wire:click="$set('showForm', true)" class="text-orange-600 hover:text-orange-700 text-sm font-semibold">Ubah Review</button>
        </div>
    @endif

    @if ($showForm)
        <form wire:submit="submitReview" class="space-y-4">
            <h4 class="font-semibold">Berikan Rating</h4>

            <div class="flex gap-2">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="$set('rating', {{ $i }})" class="text-4xl">
                        @if ($rating >= $i) ⭐ @else ☆ @endif
                    </button>
                @endfor
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Komentar (opsional)</label>
                <textarea wire:model="comment" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Foto (opsional, maks 5MB)</label>
                <input type="file" wire:model="uploadedFiles" multiple accept="image/*" class="w-full px-3 py-2 border rounded-lg" />
                @error('uploadedFiles.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                @if (count($uploadedFiles) > 0)
                    <div class="grid grid-cols-3 gap-2 mt-3">
                        @foreach ($uploadedFiles as $file)
                            <img src="{{ $file->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg" />
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">Simpan Review</button>
                <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
            </div>
        </form>
    @elseif (!$review)
        <button wire:click="$set('showForm', true)" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">⭐ Beri Rating</button>
    @endif
</div>