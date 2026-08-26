<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function markAsRead($notifId)
    {
        $notif = auth()->user()->notifications()->findOrFail($notifId);
        $notif->markAsRead();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['is_read' => true, 'read_at' => now()]);
        $this->dispatch('notify', message: 'Semua notifikasi ditandai sudah dibaca');
    }

    public function with()
    {
        return [
            'notifications' => auth()->user()->notifications()->latest()->paginate(20),
        ];
    }
};

?>

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto px-6 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Notifikasi</h1>
                <button
                    wire:click="markAllRead"
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold"
                >
                    Tandai Semua Sudah Dibaca
                </button>
            </div>

            @if ($notifications->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @foreach ($notifications as $notif)
                        <div
                            class="p-4 border-b {{ !$notif->is_read ? 'bg-orange-50' : '' }} hover:bg-gray-50"
                            wire:click="markAsRead({{ $notif->id }})"
                        >
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        @if (!$notif->is_read)
                                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                        @endif
                                        <h3 class="font-semibold text-sm">{{ $notif->title }}</h3>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                                    <div class="flex items-center gap-4 mt-2">
                                        <span class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                        <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">{{ ucfirst($notif->type) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-4xl mb-4">🔔</div>
                    <h3 class="text-xl font-semibold mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-600">Notifikasi akan muncul di sini ketika ada aktivitas terkait pesanan Anda</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
