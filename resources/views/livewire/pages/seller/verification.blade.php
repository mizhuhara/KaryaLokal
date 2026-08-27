<?php

use Livewire\Attributes\Layout;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\Volt\Component;
use App\Models\SellerVerification;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public $verification;
    public $document_type = 'ktp';
    public $document;
    public $notes = '';

    public function mount()
    {
        $this->verification = Auth::user()->sellerProfile->verification;
    }

    public function submitVerification()
    {
        $this->validate([
            'document_type' => 'required|in:ktp,siup,neraca',
            'document' => 'required|file|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $path = $this->document->store('seller-verifications', 'public');

        SellerVerification::create([
            'seller_profile_id' => Auth::user()->sellerProfile->id,
            'document_type' => $this->document_type,
            'document_path' => $path,
            'notes' => $this->notes,
        ]);

        $this->verification = Auth::user()->sellerProfile->verification;
        $this->document = null;
        $this->notes = '';
        $this->dispatch('notify', message: 'Verifikasi berhasil dikirim');
    }
};

?>

<x-slot name="header">
    <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
        Verifikasi Toko
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Status -->
        <div class="p-4 sm:p-8 bg-white shadow-card sm:rounded-lg">
            <header>
                <h2 class="text-lg font-medium text-neutral-900">Status Verifikasi</h2>
            </header>

            <div class="mt-6">
                @if (!$verification)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-yellow-800 font-semibold">Belum Diajukan</p>
                        <p class="text-yellow-700 text-sm mt-1">Ajukan verifikasi untuk mendapatkan badge toko terpercaya</p>
                    </div>
                @elseif ($verification->status === 'pending')
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <p class="text-blue-800 font-semibold">Menunggu Review</p>
                        <p class="text-blue-700 text-sm mt-1">Dikirim pada {{ $verification->created_at->format('d M Y H:i') }}</p>
                    </div>
                @elseif ($verification->status === 'approved')
                    <div class="bg-green-50 border-l-4 border-green-400 p-4">
                        <p class="text-green-800 font-semibold">✅ Terverifikasi</p>
                        <p class="text-green-700 text-sm mt-1">Diverifikasi pada {{ $verification->reviewed_at->format('d M Y H:i') }}</p>
                    </div>
                @else
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <p class="text-red-800 font-semibold">Ditolak</p>
                        <p class="text-red-700 text-sm mt-1">{{ $verification->rejection_reason ?? 'Tidak ada alasan' }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit Form -->
        @if (!$verification || $verification->status === 'rejected')
            <div class="p-4 sm:p-8 bg-white shadow-card sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-neutral-900">Ajukan Verifikasi</h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Unggah dokumen untuk memverifikasi toko Anda
                    </p>
                </header>

                <form wire:submit="submitVerification" class="mt-6 space-y-6 max-w-xl">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Jenis Dokumen</label>
                        <select wire:model="document_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="ktp">KTP</option>
                            <option value="siup">SIUP / NIB</option>
                            <option value="neraca">Neraca / Rekening Koran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Upload Dokumen</label>
                        <input type="file" wire:model="document" accept="image/*,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"/>
                        @error('document') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Catatan (Opsional)</label>
                        <textarea wire:model="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <x-primary-button>Kirim Verifikasi</x-primary-button>
                </form>
            </div>
        @endif

    </div>
</div>
