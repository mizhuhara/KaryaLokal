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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">Laporan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    <p class="text-sm text-gray-600">Menunggu</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['reviewed'] }}</p>
                    <p class="text-sm text-gray-600">Ditinjau</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['action_taken'] }}</p>
                    <p class="text-sm text-gray-600">Tindakan Diambil</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="flex gap-2 mb-6">
                @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'reviewed' => 'Ditinjau', 'action_taken' => 'Tindakan', 'dismissed' => 'Ditolak'] as $val => $label)
                    <button wire:click="$set('filter', '{{ $val }}')" class="px-4 py-2 rounded-lg text-sm {{ $filter === $val ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 shadow hover:bg-gray-50' }}">{{ $label }}</button>
                @endforeach
            </div>

            <!-- Reports -->
            @if ($reports->count() > 0)
                <div class="space-y-4">
                    @foreach ($reports as $report)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded text-xs font-semibold">{{ $report->reason }}</span>
                                        <span class="px-2 py-0.5 {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }} rounded text-xs">{{ $report->status }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">Dilaporkan oleh: {{ $report->user->name }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</span>
                            </div>

                            @if ($report->description)
                                <p class="text-gray-700 mb-3">{{ $report->description }}</p>
                            @endif

                            <div class="text-sm text-gray-600 mb-4">
                                <p><strong>Tipe:</strong> {{ class_basename($report->reportable_type) }}</p>
                                @if ($report->reportable)
                                    <p><strong>Nama:</strong> {{ $report->reportable->name ?? $report->reportable->shop_name ?? $report->reportable->title ?? '-' }}</p>
                                @endif
                            </div>

                            @if ($report->status === 'pending')
                                <div class="flex gap-2">
                                    <button wire:click="markReviewed({{ $report->id }})" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Ditinjau</button>
                                    <button wire:click="markActionTaken({{ $report->id }})" class="px-3 py-1 bg-green-600 text-white rounded text-sm">Tindakan</button>
                                    <button wire:click="markDismissed({{ $report->id }})" class="px-3 py-1 bg-gray-400 text-white rounded text-sm">Tolak</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">{{ $reports->links() }}</div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">✅</div>
                    <p class="text-gray-600">Tidak ada laporan</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
