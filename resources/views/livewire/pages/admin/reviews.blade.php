<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Review;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteReview($reviewId)
    {
        Review::findOrFail($reviewId)->delete();
        $this->dispatch('notify', message: 'Review dihapus');
    }

    public function with()
    {
        $query = Review::with(['user', 'product', 'images']);

        if ($this->search) {
            $query->where('comment', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        return [
            'reviews' => $query->orderBy('created_at', 'desc')->paginate(20),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">⭐ Manajemen Review</h1>
                <p class="text-gray-600 text-sm">Total {{ $reviews->total() }} review</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">
            <!-- Search -->
            <div class="kl-card p-6">
                <input type="text" wire:model.live="search" placeholder="Cari review, nama user, atau nama produk..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"/>
            </div>

            <!-- Reviews List -->
            <div class="space-y-4">
                @forelse ($reviews as $review)
                    <div class="kl-card p-6">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <p class="font-semibold text-gray-900 font-jakarta">{{ $review->user->name }}</p>
                                    <span class="text-yellow-500 text-sm tracking-wider">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Produk: <strong class="text-gray-700">{{ $review->product->name ?? 'Produk dihapus' }}</strong></p>
                                @if ($review->comment)
                                    <p class="text-gray-700 bg-kl-warm p-4 rounded-lg text-sm">{{ $review->comment }}</p>
                                @endif
                                @if ($review->images->count() > 0)
                                    <div class="flex gap-2 mt-3">
                                        @foreach ($review->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-16 h-16 object-cover rounded-lg border border-kl" alt="Review image">
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <button wire:click="deleteReview({{ $review->id }})" wire:confirm="Hapus review ini?" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition shrink-0">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="kl-card p-12 text-center">
                        <div class="text-4xl mb-3">💬</div>
                        <p class="text-gray-500">Tidak ada review ditemukan</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-center">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>

</div>
