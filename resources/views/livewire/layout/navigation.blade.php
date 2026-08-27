<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use App\Models\Notification;

new class extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function updateUnreadCount()
    {
        if (auth()->check()) {
            $this->unreadCount = auth()->user()->unreadNotifications()->count();
        }
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['is_read' => true, 'read_at' => now()]);
        $this->unreadCount = 0;
        $this->dispatch('notify', message: 'Semua notifikasi ditandai sudah dibaca');
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false, notifOpen: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" wire:navigate>
                        <span class="text-xl font-bold text-orange-600">KaryaLokal</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('products')" :active="request()->routeIs('products')" wire:navigate>
                        Produk
                    </x-nav-link>
                    <x-nav-link :href="route('nearby')" :active="request()->routeIs('nearby')" wire:navigate>
                        Terdekat
                    </x-nav-link>
                    @if (auth()->check())
                        <x-nav-link :href="route('cart')" :active="request()->routeIs('cart')" wire:navigate>
                            Keranjang
                        </x-nav-link>
                        <x-nav-link :href="route('buyer.orders')" :active="request()->routeIs('buyer.orders')" wire:navigate>
                            Pesanan
                        </x-nav-link>
                        <x-nav-link :href="route('wishlist')" :active="request()->routeIs('wishlist')" wire:navigate>
                            Wishlist
                        </x-nav-link>
                        <x-nav-link :href="route('chat')" :active="request()->routeIs('chat*')" wire:navigate>
                            Chat
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @if (auth()->check())
                    <!-- Notification Bell -->
                    <div class="relative" @click.away="notifOpen = false">
                        <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if ($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notifOpen" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 border">
                            <div class="p-4 border-b flex justify-between items-center">
                                <h3 class="font-semibold">Notifikasi</h3>
                                @if ($unreadCount > 0)
                                    <button wire:click="markAllRead" class="text-sm text-orange-600 hover:text-orange-700">
                                        Tandai semua dibaca
                                    </button>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @php $notifications = auth()->user()->notifications()->latest()->limit(10)->get(); @endphp
                                @if ($notifications->count() > 0)
                                    @foreach ($notifications as $notif)
                                        <div class="p-4 border-b {{ !$notif->is_read ? 'bg-orange-50' : '' }} hover:bg-gray-50">
                                            <p class="font-semibold text-sm">{{ $notif->title }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ $notif->message }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-6 text-center text-gray-500 text-sm">Tidak ada notifikasi</div>
                                @endif
                            </div>
                            <div class="p-3 text-center border-t">
                                <a href="{{ route('notifications') }}" class="text-sm text-orange-600 hover:text-orange-700">
                                    Lihat Semua →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name ?? '']) }}" x-text="name"></div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (auth()->user()->isSeller())
                            <x-dropdown-link :href="route('seller.dashboard')" wire:navigate>
                                Dashboard Toko
                            </x-dropdown-link>
                        @endif
                        @if (auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.dashboard')" wire:navigate>
                                Admin Panel
                            </x-dropdown-link>
                        @endif
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Masuk</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Daftar</a>
            @endif
            </div>
        </div>
    </div>

    <!-- Responsive -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('products')" wire:navigate>Produk</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('nearby')" wire:navigate>Terdekat</x-responsive-nav-link>
            @if (auth()->check())
                <x-responsive-nav-link :href="route('cart')" wire:navigate>Keranjang</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('buyer.orders')" wire:navigate>Pesanan</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('wishlist')" wire:navigate>Wishlist</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('chat')" wire:navigate>Chat</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications')" wire:navigate>Notifikasi</x-responsive-nav-link>
                @if (auth()->user()->isSeller())
                    <x-responsive-nav-link :href="route('seller.dashboard')" wire:navigate>Dashboard Toko</x-responsive-nav-link>
                @endif
                @if (auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" wire:navigate>Admin Panel</x-responsive-nav-link>
                @endif
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            @if (auth()->check())
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile')" wire:navigate>Profile</x-responsive-nav-link>
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>Log Out</x-responsive-nav-link>
                    </button>
                </div>
            @endif
        </div>
    </div>
</nav>
