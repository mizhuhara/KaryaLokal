<?php

use Livewire\Volt\Component;
use App\Models\Review;
use App\Models\Order;

new class extends Component {
    public $order_id;
    public $product_id;
    public $rating = 5;
    public $comment = '';
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
        ]);

        Review::updateOrCreate(
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

        $this->dispatch('notify', message: 'Review berhasil disimpan');
        $this->showForm = false;
    }

    public function with()
    {
        return [
            'review' => Review::where('user_id', auth()->id())
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
                        <span class="text-2xl {{ $i <= $review->rating ? '⭐' : '☆' }}"></span>
                    @endfor
                </div>
                <span class="text-lg font-bold">{{ $review->rating }}/5</span>
            </div>
            @if ($review->comment)
                <p class="text-gray-700">{{ $review->comment }}</p>
            @endif
            <button
                wire:click="$set('showForm', true)"
                class="text-orange-600 hover:text-orange-700 text-sm font-semibold"
            >
                Ubah Review
            </button>
        </div>
    @endif

    @if ($showForm)
        <form wire:submit="submitReview" class="space-y-4">
            <h4 class="font-semibold">Berikan Rating</h4>

            <div class="flex gap-2">
                @for ($i = 1; $i <= 5; $i++)
                    <button
                        type="button"
                        wire:click="$set('rating', {{ $i }})"
                        class="text-4xl transition"
                        :class="rating >= {{ $i }} ? '⭐' : '☆'"
                    >
                        @if ($rating >= $i)
                            ⭐
                        @else
                            ☆
                        @endif
                    </button>
                @endfor
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Komentar (opsional)</label>
                <textarea
                    wire:model="comment"
                    rows="3"
                    placeholder="Bagikan pengalaman Anda dengan produk ini..."
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                ></textarea>
                @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
                >
                    Simpan Review
                </button>
                <button
                    type="button"
                    wire:click="$set('showForm', false)"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                >
                    Batal
                </button>
            </div>
        </form>
    @elseif (!$review)
        <button
            wire:click="$set('showForm', true)"
            class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
        >
            ⭐ Beri Rating
        </button>
    @endif
</div>
