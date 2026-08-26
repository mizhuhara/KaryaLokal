<?php

use Livewire\Volt\Component;
use App\Models\Message;
use App\Models\User;

new class extends Component {
    public $receiver_id;
    public $message = '';
    public $conversations = [];
    public $selectedConversation = null;
    public $messages = [];

    public function mount()
    {
        $this->loadConversations();
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

        $this->message = '';
        $this->loadMessages($this->receiver_id);
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

<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-8">Chat</h1>

            <div class="bg-white rounded-lg shadow overflow-hidden" style="height: 600px;">
                <div class="flex h-full">
                    <!-- Conversations List -->
                    <div class="w-80 border-r bg-gray-50">
                        @if ($conversations->count() > 0)
                            @foreach ($conversations as $conv)
                                <button
                                    wire:click="selectConversation({{ $conv['user']->id }})"
                                    class="w-full text-left p-4 border-b hover:bg-gray-100 {{ $selectedConversation?->id === $conv['user']->id ? 'bg-orange-50' : '' }}"
                                >
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold">{{ $conv['user']->name }}</p>
                                            <p class="text-sm text-gray-600 truncate max-w-[200px]">
                                                {{ $conv['last_message']?->message ?? 'Mulai chat...' }}
                                            </p>
                                        </div>
                                        @if ($conv['unread'] > 0)
                                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $conv['unread'] }}</span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        @else
                            <div class="p-6 text-center text-gray-500">
                                <p class="text-sm">Belum ada percakapan</p>
                            </div>
                        @endif
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col">
                        @if ($selectedConversation)
                            <!-- Header -->
                            <div class="p-4 border-b bg-white flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-lg">{{ $selectedConversation->name }}</p>
                                    @if ($selectedConversation->isSeller())
                                        <p class="text-sm text-gray-600">Penjual</p>
                                    @endif
                                </div>
                                <a href="{{ route('seller-store', $selectedConversation->sellerProfile?->id) }}" class="text-orange-600 hover:text-orange-700 text-sm">
                                    Kunjungi Toko
                                </a>
                            </div>

                            <!-- Messages -->
                            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                                @forelse ($messages as $msg)
                                    <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[70%] rounded-lg px-4 py-2 {{ $msg->sender_id === auth()->id() ? 'bg-orange-500 text-white' : 'bg-white shadow' }}">
                                            <p>{{ $msg->message }}</p>
                                            <p class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-orange-100' : 'text-gray-500' }} mt-1">
                                                {{ $msg->created_at->format('H:i') }}
                                                @if ($msg->sender_id === auth()->id() && $msg->is_read)
                                                    ✓✓
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-gray-500 py-8">
                                        <p>Mulai percakapan...</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Input -->
                            <div class="p-4 border-t bg-white">
                                <form wire:submit="sendMessage" class="flex gap-2">
                                    <input
                                        type="text"
                                        wire:model="message"
                                        placeholder="Ketik pesan..."
                                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                                    />
                                    <button
                                        type="submit"
                                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
                                    >
                                        Kirim
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex-1 flex items-center justify-center text-gray-500">
                                <div class="text-center">
                                    <div class="text-4xl mb-4">💬</div>
                                    <p>Pilih percakapan atau mulai chat baru</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
