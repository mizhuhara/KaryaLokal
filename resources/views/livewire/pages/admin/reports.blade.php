<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Report;

new class extends Component {
    use WithPagination;

    public $filter = 'all';

    public function markReviewed($id)
    {
        Report::findOrFail($id)->update(['status' => 'reviewed', 'reviewed_at' => now(), 'reviewed_by' => auth()->id()]);
        $this->dispatch('notify', message: 'Laporan ditandai sudah ditinjau');
    }

    public function markActionTaken($id)
    {
        Report::findOrFail($id)->update(['status' => 'action_taken', 'reviewed_at' => now(), 'reviewed_by' => auth()->id()]);
        $this->dispatch('notify', message: 'Tindakan sudah diambil');
    }

    public function markDismissed($id)
    {
        Report::findOrFail($id)->update(['status' => 'dismissed', 'reviewed_at' => now(), 'reviewed_by' => auth()->id()]);
        $this->dispatch('notify', message: 'Laporan ditolak');
    }

    public function with()
    {
        $query = Report::with('user', 'reportable', 'reviewer');
        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }
        return [
            'reports' => $query->orderBy('created_at', 'desc')->paginate(15),
            'stats' => [
                'pending' => Report::where('status', 'pending')->count(),
                'reviewed' => Report::where('status', 'reviewed')->count(),
                'action_taken' => Report::where('status', 'action_taken')->count(),
            ],
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">🚨 Laporan</h1>
                <p class="text-gray-600 text-sm">Tinjau laporan dari pengguna</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="kl-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold font-jakarta text-yellow-600">{{ $stats['pending'] }}</p>
                        <p class="text-sm text-gray-600 font-medium">Menunggu</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-yellow-100 text-2xl">⏳</div>
                </div>
                <div class="kl-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold font-jakarta text-blue-600">{{ $stats['reviewed'] }}</p>
                        <p class="text-sm text-gray-600 font-medium">Ditinjau</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-blue-100 text-2xl">📋</div>
                </div>
                <div class="kl-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold font-jakarta text-green-600">{{ $stats['action_taken'] }}</p>
                        <p class="text-sm text-gray-600 font-medium">Tindakan Diambil</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-green-100 text-2xl">✅</div>
                </div>
            </div>

            <!-- Filter -->
            <div class="kl-card p-4 mb-6">
                <div class="flex flex-wrap gap-3">
                    @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'reviewed' => 'Ditinjau', 'action_taken' => 'Tindakan', 'dismissed' => 'Ditolak'] as $val => $label)
                        <button wire:click="$set('filter', '{{ $val }}')" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $filter === $val ? 'bg-kl-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Reports -->
            @if ($reports->count() > 0)
                <div class="space-y-4">
                    @foreach ($reports as $report)
                        <div class="kl-card p-6">
                            <div class="flex justify-between items-start mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold">{{ $report->reason }}</span>
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700' }}">{{ $report->status }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">Dilaporkan oleh: <strong class="text-gray-800">{{ $report->user->name }}</strong></p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</span>
                            </div>

                            @if ($report->description)
                                <p class="text-gray-700 mb-3 bg-kl-warm p-4 rounded-lg text-sm">{{ $report->description }}</p>
                            @endif

                            <div class="text-sm text-gray-600 mb-4">
                                <p><strong class="text-gray-800">Tipe:</strong> {{ class_basename($report->reportable_type) }}</p>
                                @if ($report->reportable)
                                    <p><strong class="text-gray-800">Nama:</strong> {{ $report->reportable->name ?? $report->reportable->shop_name ?? $report->reportable->title ?? '-' }}</p>
                                @endif
                            </div>

                            @if ($report->status === 'pending')
                                <div class="flex gap-2 flex-wrap">
                                    <button wire:click="markReviewed({{ $report->id }})" class="px-4 py-1.5 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700 transition">Ditinjau</button>
                                    <button wire:click="markActionTaken({{ $report->id }})" class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">Tindakan</button>
                                    <button wire:click="markDismissed({{ $report->id }})" class="px-4 py-1.5 bg-gray-400 text-white rounded-lg text-sm font-semibold hover:bg-gray-500 transition">Tolak</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">{{ $reports->links() }}</div>
            @else
                <div class="kl-card p-12 text-center">
                    <div class="text-4xl mb-4">🎉</div>
                    <p class="text-gray-600">Tidak ada laporan</p>
                </div>
            @endif
        </div>
    </div>

</div>
