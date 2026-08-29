<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;

new class extends Component {
    public $receiver_id;
    public $message = '';
    public $conversations = [];
    public $selectedConversation = null;
    public $messages = [];

    public function mount($user = null)
    {
        if ($user) {
            $this->selectConversation((int) $user);
        } else {
            $this->loadConversations();
        }
    }

    public function loadConversations()
    {
        $userId = auth()->id();

        $sentTo = Message::where('sender_id', $userId)->pluck('receiver_id')->unique();
        $receivedFrom = Message::where('receiver_id', $userId)->pluck('sender_id')->unique();

        $allUserIds = $sentTo->merge($receivedFrom)->unique();

        $this->conversations = User::whereIn('id', $allUserIds)
            ->get()
            ->map(function ($user) use ($userId) {
                $lastMessage = Message::where(function ($q) use ($userId, $user) {
                    $q->where('sender_id', $userId)->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($userId, $user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $userId);
                })->latest()->first();

                $unread = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'user' => $user,
                    'last_message' => $lastMessage,
                    'unread' => $unread,
                ];
            })
            ->sortByDesc(function ($item) {
                return $item['last_message']?->created_at;
            })
            ->values();
    }

    public function selectConversation($userId)
    {
        $this->receiver_id = $userId;
        $this->selectedConversation = User::find($userId);
        $this->loadMessages($userId);

        Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->update(['is_read' => true]);

        $this->loadConversations();
    }

    public function loadMessages($userId)
    {
        $this->messages = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();
    }

    public function sendMessage()
    {
        if (empty(trim($this->message)) || !$this->receiver_id) {
            return;
        }

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiver_id,
            'message' => trim($this->message),
        ]);

        Notification::create([
            'user_id' => $this->receiver_id,
            'type' => 'chat',
            'title' => auth()->user()->name . ' mengirim pesan',
            'message' => Str::limit($this->message, 120),
            'is_read' => false,
        ]);

        $this->message = '';
        $this->refreshMessages();
    }

    #[On('message-sent')]
    public function refreshMessages()
    {
        if ($this->receiver_id) {
            $this->loadMessages($this->receiver_id);

            Message::where('sender_id', $this->receiver_id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $this->loadConversations();
    }

    public function startNewChat($userId)
    {
        $this->selectConversation($userId);
    }

    public function with()
    {
        return [
            'conversations' => $this->conversations,
            'selectedConversation' => $this->selectedConversation,
            'messages' => $this->messages,
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">💬 Chat</h1>
                <p class="text-gray-600 text-sm">Komunikasi langsung dengan penjual</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="kl-card overflow-hidden" style="height: 600px;">
                <div class="flex h-full">
                    <!-- Conversations Sidebar -->
                    <div class="w-80 border-r border-kl bg-gray-50 flex flex-col shrink-0">
                        <!-- Search / Header -->
                        <div class="p-4 border-b border-kl">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Percakapan</p>
                        </div>

                        <!-- Conversations List -->
                        <div class="flex-1 overflow-y-auto kl-scroll">
                            @if ($conversations->count() > 0)
                                @foreach ($conversations as $conv)
                                    <button
                                        wire:click="selectConversation({{ $conv['user']->id }})"
                                        class="w-full text-left px-4 py-3 border-b border-kl transition-all duration-200 hover:bg-orange-50/50 {{ $selectedConversation?->id === $conv['user']->id ? 'bg-orange-50 border-l-4 border-l-kl-primary' : '' }}"
                                    >
                                        <div class="flex items-start gap-3">
                                            <!-- Avatar -->
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                                 style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light))">
                                                {{ strtoupper(substr($conv['user']->name, 0, 1)) }}
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="font-semibold text-sm text-gray-800 font-jakarta truncate">{{ $conv['user']->name }}</p>
                                                    <div class="flex items-center gap-2 shrink-0 ml-2">
                                                        @if ($conv['last_message'])
                                                            <span class="text-[10px] text-gray-400">
                                                                {{ $conv['last_message']->created_at->isToday() ? $conv['last_message']->created_at->format('H:i') : ($conv['last_message']->created_at->isYesterday() ? 'Kemarin' : $conv['last_message']->created_at->format('d M')) }}
                                                            </span>
                                                        @endif
                                                        @if ($conv['unread'] > 0)
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style="background: var(--kl-primary)">
                                                                {{ $conv['unread'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 truncate mt-0.5">
                                                    {{ $conv['last_message']?->message ?? 'Mulai chat...' }}
                                                </p>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            @else
                                <div class="p-8 text-center">
                                    <p class="text-3xl mb-2">💬</p>
                                    <p class="text-xs text-gray-500">Belum ada percakapan</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col min-w-0">
                        @if ($selectedConversation)
                            <!-- Chat Header -->
                            <div class="px-6 py-4 border-b border-kl bg-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white"
                                         style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light))">
                                        {{ strtoupper(substr($selectedConversation->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800 font-jakarta">{{ $selectedConversation->name }}</p>
                                        @if ($selectedConversation->isSeller())
                                            <p class="text-[11px] text-gray-500">Penjual</p>
                                        @endif
                                    </div>
                                </div>
                                @if ($selectedConversation->sellerProfile)
                                    <a href="{{ route('seller-store', $selectedConversation->sellerProfile->id) }}" wire:navigate
                                       class="kl-btn-ghost text-xs" style="color: var(--kl-primary)">
                                        Kunjungi Toko →
                                    </a>
                                @endif
                            </div>

                            <!-- Messages Area -->
                            <div wire:poll.3s="refreshMessages" class="flex-1 overflow-y-auto p-5 space-y-3 kl-scroll" style="background: linear-gradient(to bottom, #FFFAF7, #F9F5F2)">
                                @forelse ($messages as $msg)
                                    <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[70%] rounded-2xl px-4 py-2.5 {{ $msg->sender_id === auth()->id() ? 'rounded-br-md' : 'rounded-bl-md' }}"
                                             style="{{ $msg->sender_id === auth()->id() ? 'background: var(--kl-primary); color: white;' : 'background: white; border: 1px solid #F0E8E0;' }}">
                                            <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                                            <div class="flex items-center gap-1 mt-1 {{ $msg->sender_id === auth()->id() ? 'justify-end' : '' }}">
                                                <p class="text-[10px] {{ $msg->sender_id === auth()->id() ? 'text-white/70' : 'text-gray-400' }}">
                                                    {{ $msg->created_at->isToday() ? $msg->created_at->format('H:i') : $msg->created_at->format('d M H:i') }}
                                                </p>
                                                @if ($msg->sender_id === auth()->id() && $msg->is_read)
                                                    <span class="text-[10px] text-white/70">✓✓</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex items-center justify-center h-full">
                                        <div class="text-center">
                                            <p class="text-4xl mb-2">💬</p>
                                            <p class="text-sm text-gray-500">Mulai percakapan dengan {{ $selectedConversation->name }}</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Message Input -->
                            <div class="p-4 border-t border-kl bg-white">
                                <form wire:submit="sendMessage" class="flex gap-2 items-center">
                                    <input
                                        type="text"
                                        wire:model="message"
                                        placeholder="Ketik pesan..."
                                        class="kl-input flex-1"
                                    />
                                    <button
                                        type="submit"
                                        class="kl-btn-primary py-3 px-5 text-sm justify-center shrink-0"
                                    >
                                        Kirim
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-6xl mb-4">💬</div>
                                    <h3 class="kl-section-title">Mulai Chat</h3>
                                    <p class="text-gray-500 text-sm mt-1">Pilih percakapan atau mulai chat baru dari halaman produk</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
