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

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-3xl mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="kl-section-title mb-1">🔔 Notifikasi</h1>
                        <p class="text-gray-600 text-sm">Semua aktivitas terkait pesanan Anda</p>
                    </div>
                    <button
                        wire:click="markAllRead"
                        class="kl-btn-ghost text-xs font-semibold"
                        style="color: var(--kl-primary)"
                    >
                        Tandai Semua Sudah Dibaca
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-6 py-8">
            @if ($notifications->count() > 0)
                <div class="kl-card overflow-hidden kl-stagger">
                    @foreach ($notifications as $notif)
                        <div
                            class="px-6 py-4 border-b border-kl transition cursor-pointer {{ !$notif->is_read ? 'hover:bg-orange-50/50' : 'hover:bg-gray-50' }}"
                            style="{{ !$notif->is_read ? 'background: #FFFAF7;' : '' }}"
                            wire:click="markAsRead({{ $notif->id }})"
                        >
                            <div class="flex gap-3 items-start animate-fade-in-up">
                                <!-- Unread Indicator -->
                                <div class="shrink-0 mt-2">
                                    @if (!$notif->is_read)
                                        <div class="w-2.5 h-2.5 rounded-full" style="background: var(--kl-primary)"></div>
                                    @else
                                        <div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="font-semibold text-sm text-gray-800 font-jakarta {{ !$notif->is_read ? '' : 'text-gray-600' }}">
                                            {{ $notif->title }}
                                        </p>
                                        <span class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold border border-kl bg-white text-gray-500">
                                            {{ ucfirst($notif->type) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $notif->message }}</p>
                                    <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="kl-card p-12 text-center">
                    <div class="text-6xl mb-4">🔔</div>
                    <h3 class="kl-section-title">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-500 text-sm">Notifikasi akan muncul di sini ketika ada aktivitas terkait pesanan Anda</p>
                </div>
            @endif
        </div>
    </div>

</div>
