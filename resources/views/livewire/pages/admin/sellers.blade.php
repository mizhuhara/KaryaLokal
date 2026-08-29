<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\SellerProfile;

new class extends Component {
    use WithPagination;

    public $filter = 'all';

    public function verifySeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->update(['is_verified' => true, 'verified_at' => now()]);
        $seller->user->update(['role' => 'seller']);

        $verification = $seller->verification;
        if ($verification) {
            $verification->update([
                'status' => 'approved',
                'reviewed_at' => now(),
            ]);
        }

        $this->dispatch('notify', message: 'Seller diverifikasi');
    }

    public function rejectSeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->update(['is_verified' => false, 'verified_at' => null]);

        $verification = $seller->verification;
        if ($verification) {
            $verification->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'rejection_reason' => 'Dokumen tidak valid atau tidak memenuhi syarat',
            ]);
        }

        $this->dispatch('notify', message: 'Seller ditolak');
    }

    public function unverifySeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->update(['is_verified' => false, 'verified_at' => null]);
        $this->dispatch('notify', message: 'Verifikasi dicabut');
    }

    public function deleteSeller($sellerId)
    {
        $seller = SellerProfile::findOrFail($sellerId);
        $seller->user->update(['role' => 'buyer']);
        $seller->delete();
        $this->dispatch('notify', message: 'Seller dihapus');
    }

    public function with()
    {
        $query = SellerProfile::with('user');

        if ($this->filter === 'pending') {
            $query->where('is_verified', false);
        } elseif ($this->filter === 'verified') {
            $query->where('is_verified', true);
        }

        return [
            'sellers' => $query->orderBy('created_at', 'desc')->paginate(20),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">🏪 Manajemen Sellers</h1>
                <p class="text-gray-600 text-sm">Total {{ $sellers->total() }} seller</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Filter -->
            <div class="kl-card p-4 mb-6">
                <div class="flex gap-3">
                    @foreach (['all' => 'Semua', 'pending' => 'Menunggu Verifikasi', 'verified' => 'Sudah Verifikasi'] as $value => $label)
                        <button
                            wire:click="$set('filter', '{{ $value }}')"
                            class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === $value ? 'bg-kl-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Sellers List -->
            <div class="space-y-4">
                @foreach ($sellers as $seller)
                    <div class="kl-card overflow-hidden">
                        <div class="p-6 flex justify-between items-start gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold font-jakarta text-gray-900">{{ $seller->shop_name }}</h3>
                                    @if ($seller->is_verified)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Terverifikasi</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">Menunggu</span>
                                    @endif
                                </div>
                                <p class="text-gray-600 text-sm mb-2">{{ $seller->address }}, {{ $seller->city }}, {{ $seller->province }}</p>
                                <p class="text-sm text-gray-500">
                                    User: <span class="font-semibold text-gray-700">{{ $seller->user->name }}</span> ({{ $seller->user->email }})
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Produk: <span class="font-semibold">{{ $seller->products()->count() }}</span> | Bergabung: {{ $seller->created_at->format('d M Y') }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2 justify-end shrink-0">
                                <a href="{{ route('seller-store', $seller->id) }}" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 text-sm font-semibold transition">
                                    Lihat
                                </a>
                                @if (!$seller->is_verified)
                                    <button
                                        wire:click="verifySeller({{ $seller->id }})"
                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-semibold transition"
                                    >
                                        ✓ Verifikasi
                                    </button>
                                    <button
                                        wire:click="rejectSeller({{ $seller->id }})"
                                        wire:confirm="Tolak seller ini?"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm transition"
                                    >
                                        Tolak
                                    </button>
                                @else
                                    <button
                                        wire:click="unverifySeller({{ $seller->id }})"
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm transition"
                                    >
                                        Cabut
                                    </button>
                                @endif
                                <button
                                    wire:click="deleteSeller({{ $seller->id }})"
                                    wire:confirm="Hapus seller ini? Semua produk juga akan dihapus."
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm transition"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $sellers->links() }}
            </div>
        </div>
    </div>

</div>
