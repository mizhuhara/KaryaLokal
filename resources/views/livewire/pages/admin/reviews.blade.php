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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Manajemen Review
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Search -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                <input type="text" wire:model.live="search" placeholder="Cari review, nama user, atau nama produk..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"/>
            </div>

            <!-- Reviews List -->
            <div class="space-y-4">
                @forelse ($reviews as $review)
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <p class="font-semibold">{{ $review->user->name }}</p>
                                    <span class="text-yellow-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Produk: {{ $review->product->name ?? 'Produk dihapus' }}</p>
                                @if ($review->comment)
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                @endif
                                @if ($review->images->count() > 0)
                                    <div class="flex gap-2 mt-3">
                                        @foreach ($review->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-16 h-16 object-cover rounded" alt="Review image">
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <button wire:click="deleteReview({{ $review->id }})" wire:confirm="Hapus review ini?" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-12 text-center">
                        <p class="text-gray-500">Tidak ada review ditemukan</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-center">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
