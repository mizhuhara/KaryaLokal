<?php

use Livewire\Volt\Component;
use App\Models\Report;

new class extends Component {
    public $reportableType;
    public $reportableId;
    public $reason = '';
    public $description = '';
    public $showForm = false;

    public function openForm()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $this->showForm = true;
    }

    public function submit()
    {
        $this->validate([
            'reason' => 'required|in:spam,inappropriate,fraud,duplicate,wrong_info,other',
            'description' => 'nullable|string|max:1000',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'reportable_type' => $this->reportableType,
            'reportable_id' => $this->reportableId,
            'reason' => $this->reason,
            'description' => $this->description,
        ]);

        $this->dispatch('notify', message: 'Laporan dikirim. Terima kasih telah membantu kami!');
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reason = '';
        $this->description = '';
    }
};

?>

<div>
    @if (!$showForm)
        <button
            wire:click="openForm"
            class="text-red-600 hover:text-red-800 text-sm font-semibold"
        >
            🚩 Laporkan
        </button>
    @else
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold mb-4">Laporkan</h3>

                <form wire:submit="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Alasan</label>
                        <select
                            wire:model="reason"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"
                        >
                            <option value="">-- Pilih Alasan --</option>
                            <option value="spam">Spam</option>
                            <option value="inappropriate">Tidak Pantas</option>
                            <option value="fraud">Penipuan</option>
                            <option value="duplicate">Duplikat</option>
                            <option value="wrong_info">Info Salah</option>
                            <option value="other">Lainnya</option>
                        </select>
                        @error('reason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Deskripsi (Opsional)</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            placeholder="Jelaskan alasan laporan ini..."
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"
                        ></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold"
                        >
                            Kirim Laporan
                        </button>
                        <button
                            type="button"
                            wire:click="$set('showForm', false)"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
