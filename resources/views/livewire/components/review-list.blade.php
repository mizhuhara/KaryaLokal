<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Review;

new class extends Component {
    use WithPagination;

    public $product_id;

    public function mount($productId)
    {
        $this->product_id = $productId;
    }

    public function with()
    {
        $reviews = Review::where('product_id', $this->product_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $stats = Review::where('product_id', $this->product_id)
            ->selectRaw('COUNT(*) as total, AVG(rating) as avg_rating')
            ->first();

        return [
            'reviews' => $reviews,
            'stats' => $stats,
        ];
    }
};

?>

<div class="bg-white rounded-lg shadow p-6 space-y-6">
    <h3 class="text-2xl font-bold">Rating & Review</h3>

    <!-- Stats -->
    @if ($stats->total > 0)
        <div class="flex items-center gap-8">
            <div>
                <p class="text-5xl font-bold">{{ number_format($stats->avg_rating, 1) }}</p>
                <div class="flex gap-1 mt-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="text-xl {{ $i <= round($stats->avg_rating) ? '⭐' : '☆' }}"></span>
                    @endfor
                </div>
                <p class="text-gray-600 mt-2">{{ $stats->total }} review</p>
            </div>

            <div class="flex-1 space-y-2">
                @for ($i = 5; $i >= 1; $i--)
                    @php
                        $count = Review::where('product_id', $product_id)->where('rating', $i)->count();
                        $percent = $stats->total > 0 ? ($count / $stats->total) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="w-12 text-sm">{{ $i }} ⭐</span>
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="w-12 text-sm text-gray-600">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>
    @else
        <p class="text-gray-600">Belum ada review untuk produk ini</p>
    @endif

    <!-- Reviews List -->
    @if ($reviews->count() > 0)
        <div class="space-y-4 border-t pt-6">
            @foreach ($reviews as $review)
                <div class="border-b pb-4 last:border-b-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold">{{ $review->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="flex gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="text-lg {{ $i <= $review->rating ? '⭐' : '☆' }}"></span>
                            @endfor
                        </div>
                    </div>
                    @if ($review->comment)
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($reviews->hasPages())
            <div class="flex justify-center">
                {{ $reviews->links() }}
            </div>
        @endif
    @endif
</div>
