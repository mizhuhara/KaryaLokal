<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Notification;

new class extends Component
{
    public $unreadCount = 0;
    public $cartCount = 0;

    public function mount()
    {
        $this->updateUnreadCount();
        $this->refreshCartCount();
    }

    #[On('cart-updated')]
    public function refreshCartCount()
    {
        $this->cartCount = collect(session('cart', []))->sum('quantity');
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

    public function getNavLinksProperty()
    {
        if (!auth()->check()) {
            return [
                ['route' => 'products', 'label' => 'Produk', 'icon' => 'shopping-bag'],
                ['route' => 'nearby', 'label' => 'Terdekat', 'icon' => 'map-pin'],
                ['route' => 'cart', 'label' => 'Keranjang', 'icon' => 'shopping-cart'],
                ['route' => 'buyer.orders', 'label' => 'Pesanan', 'icon' => 'cube'],
                ['route' => 'wishlist', 'label' => 'Wishlist', 'icon' => 'heart'],
                ['route' => 'chat', 'label' => 'Chat', 'icon' => 'chat-bubble-left-ellipsis'],
            ];
        }

        $user = auth()->user();

        if ($user->isAdmin()) {
            return [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'adjustments-horizontal'],
                ['route' => 'admin.users', 'label' => 'Users', 'icon' => 'users'],
                ['route' => 'admin.sellers', 'label' => 'Sellers', 'icon' => 'building-storefront'],
                ['route' => 'admin.products', 'label' => 'Produk', 'icon' => 'cube'],
                ['route' => 'admin.orders', 'label' => 'Pesanan', 'icon' => 'shopping-cart'],
                ['route' => 'home', 'label' => 'Lihat Situs', 'icon' => 'globe-alt'],
            ];
        }

        if ($user->isSeller() && $user->sellerProfile && request()->routeIs('seller.*')) {
            return [
                ['route' => 'seller.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['route' => 'seller.products', 'label' => 'Produk', 'icon' => 'cube'],
                ['route' => 'seller.orders', 'label' => 'Pesanan', 'icon' => 'shopping-cart'],
                ['route' => 'seller.custom-orders', 'label' => 'Custom', 'icon' => 'sparkles'],
                ['route' => 'chat', 'label' => 'Chat', 'icon' => 'chat-bubble-left-ellipsis'],
                ['route' => 'home', 'label' => 'Lihat Situs', 'icon' => 'globe-alt'],
            ];
        }

        return [
            ['route' => 'products', 'label' => 'Produk', 'icon' => 'shopping-bag'],
            ['route' => 'nearby', 'label' => 'Terdekat', 'icon' => 'map-pin'],
            ['route' => 'cart', 'label' => 'Keranjang', 'icon' => 'shopping-cart'],
            ['route' => 'buyer.orders', 'label' => 'Pesanan', 'icon' => 'cube'],
            ['route' => 'wishlist', 'label' => 'Wishlist', 'icon' => 'heart'],
            ['route' => 'chat', 'label' => 'Chat', 'icon' => 'chat-bubble-left-ellipsis'],
        ];
    }
}; ?>

<nav x-data="{ open: false, notifOpen: false, userOpen: false }"
     wire:poll.30s="updateUnreadCount"
     @keydown.escape.window="open = false; notifOpen = false; userOpen = false"
     class="kl-navbar sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left: Logo + Main Nav -->
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 shrink-0">
                    <img src="{{ asset('storage/images/logo/logo-karyalokal.png') }}" alt="KaryaLokal" class="h-10 w-auto">
                </a>

                <!-- Desktop Nav Links (exclude cart) -->
                <div class="hidden lg:flex items-center gap-1">
                    @foreach ($this->navLinks as $link)
                        @continue($link['route'] === 'cart')
                        <a href="{{ route($link['route']) }}" wire:navigate
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs(str_replace('.dashboard', '*', $link['route'])) || request()->routeIs($link['route']) ? 'bg-orange-50 text-kl-primary font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Right: Auth / User Area -->
            <div class="flex items-center gap-2">
                <!-- Cart Icon Button -->
                <a
                    href="{{ route('cart') }}"
                    wire:navigate
                    class="relative p-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('cart') ? 'bg-orange-50 text-kl-primary' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}"
                    title="Keranjang"
                >
                    <x-icon name="shopping-cart" class="w-5 h-5" />
                    @if ($this->cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center px-1 rounded-full text-white text-[10px] font-bold ring-2 ring-white"
                              style="background: #E8531D">
                            {{ $this->cartCount > 99 ? '99+' : $this->cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    @if (!auth()->user()->isAdmin() || !request()->routeIs('admin.*'))
                    <!-- Notification Bell -->
                    <div class="relative" @click.away="notifOpen = false">
                        <button @click="notifOpen = !notifOpen"
                                class="relative p-2.5 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200">
                            <x-icon name="bell" class="w-5 h-5" />
                            @if ($unreadCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full text-white text-[10px] font-bold ring-2 ring-white"
                                      style="background: #E8531D">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notifOpen" x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-cloak
                             class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl z-50 overflow-hidden"
                             style="border: 1px solid #F0E8E0; box-shadow: 0 8px 40px rgba(0,0,0,0.12)">
                            <div class="p-4 border-b flex justify-between items-center" style="border-color: #F0E8E0">
                                <h3 class="font-bold font-jakarta text-sm text-gray-800">Notifikasi</h3>
                                @if ($unreadCount > 0)
                                    <button wire:click="markAllRead"
                                            class="text-[11px] font-semibold px-2 py-1 rounded-lg hover:bg-orange-50 transition-colors"
                                            style="color: #E8531D">
                                        Tandai dibaca
                                    </button>
                                @endif
                            </div>
                            <div class="max-h-72 overflow-y-auto kl-scroll">
                                @php $notifications = auth()->user()->notifications()->latest()->limit(8)->get(); @endphp
                                @if ($notifications->count() > 0)
                                    @foreach ($notifications as $notif)
                                        <div class="px-4 py-3 border-b hover:bg-gray-50 transition-colors cursor-pointer"
                                             style="border-color: #F9F5F2; {{ !$notif->is_read ? 'background: #FFFAF7;' : '' }}">
                                            <div class="flex gap-3 items-start">
                                                <div class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ !$notif->is_read ? 'bg-orange-500' : 'bg-gray-300' }}"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-sm text-gray-800 truncate">{{ $notif->title }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="py-10 text-center">
                                        <x-icon name="bell" class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                                        <p class="text-xs text-gray-500">Tidak ada notifikasi</p>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('notifications') }}" wire:navigate
                               class="block p-3 text-center text-xs font-semibold border-t hover:bg-gray-50 transition"
                               style="border-color: #F0E8E0; color: #E8531D">
                                Lihat Semua →
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- User Dropdown -->
                    <div class="relative" @click.away="userOpen = false">
                        <button @click="userOpen = !userOpen"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-xl border transition-all duration-200 hover:shadow-sm hover:border-orange-200"
                                style="border-color: #F0E8E0; background: white">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                 style="background: linear-gradient(135deg, #E8531D, #FF7043)">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:inline text-sm font-semibold text-gray-700 max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <span :class="{'rotate-180': userOpen}" class="transition-transform duration-200 inline-flex text-gray-400">
                                <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                            </span>
                        </button>

                        <div x-show="userOpen" x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-cloak
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl z-50 py-2 overflow-hidden"
                             style="border: 1px solid #F0E8E0; box-shadow: 0 8px 40px rgba(0,0,0,0.12)">

                            <div class="px-4 py-3 border-b" style="border-color: #F9F5F2; background: #FFFAF7">
                                <p class="font-bold text-sm text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                <div class="mt-1.5">
                                    @if (auth()->user()->isAdmin())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" style="background: #5B21B6; color: white"><x-icon name="adjustments-horizontal" class="w-3 h-3" /> Admin</span>
                                    @elseif (auth()->user()->isSeller())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" style="background: #2D6A4F; color: white"><x-icon name="building-storefront" class="w-3 h-3" /> Seller</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" style="background: #E8531D; color: white"><x-icon name="shopping-bag" class="w-3 h-3" /> Buyer</span>
                                    @endif
                                </div>
                            </div>

                            <div class="py-1">
                                @if (auth()->user()->isSeller())
                                    <a href="{{ route('seller.dashboard') }}" wire:navigate
                                       class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 transition-colors">
                                        <x-icon name="building-storefront" class="w-4 h-4 text-gray-400" /> Dashboard Toko
                                    </a>
                                    <a href="{{ route('chat') }}" wire:navigate
                                       class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 transition-colors">
                                        <x-icon name="chat-bubble-left-ellipsis" class="w-4 h-4 text-gray-400" /> Chat Pembeli
                                    </a>
                                    <a href="{{ route('home') }}" wire:navigate
                                       class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 transition-colors">
                                        <x-icon name="globe-alt" class="w-4 h-4 text-gray-400" /> Mode Pembeli
                                    </a>
                                @endif
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                                       class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 transition-colors">
                                        <x-icon name="adjustments-horizontal" class="w-4 h-4 text-gray-400" /> Admin Panel
                                    </a>
                                    <a href="{{ route('home') }}" wire:navigate
                                       class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 transition-colors">
                                        <x-icon name="globe-alt" class="w-4 h-4 text-gray-400" /> Lihat Situs
                                    </a>
                                @endif
                                <a href="{{ route('profile') }}" wire:navigate
                                   class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <x-icon name="user" class="w-4 h-4 text-gray-400" /> Profile Saya
                                </a>
                            </div>

                            <div class="border-t pt-1" style="border-color: #F9F5F2">
                                <button wire:click="logout"
                                        class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-left font-semibold">
                                    <x-icon name="arrow-right-start-on-rectangle" class="w-4 h-4" /> Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Guest Buttons -->
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('login') }}" wire:navigate
                           class="px-4 py-2 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-100 transition-all duration-200">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" wire:navigate
                           class="kl-btn-primary text-sm py-2 px-4">
                            Daftar Gratis
                        </a>
                    </div>
                    <a href="{{ route('login') }}" wire:navigate
                       class="sm:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                        <x-icon name="user" class="w-5 h-5" />
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="open = !open"
                        class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-all duration-200">
                    <x-icon x-show="!open" name="bars-3" class="w-6 h-6" />
                    <x-icon x-show="open" x-cloak name="x-mark" class="w-6 h-6" />
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden border-t bg-white max-h-[calc(100vh-4rem)] overflow-y-auto kl-scroll"
         style="border-color: #F0E8E0">

        <div class="px-4 py-3 space-y-1">
            @foreach ($this->navLinks as $link)
                @continue($link['route'] === 'cart')
                <a href="{{ route($link['route']) }}" wire:navigate
                   @click="open = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs($link['route']) ? 'bg-orange-50 text-kl-primary' : 'text-gray-700 hover:bg-gray-50' }}">
                    <x-icon :name="$link['icon']" class="w-5 h-5" />
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach

            @auth
                <a href="{{ route('notifications') }}" wire:navigate
                   @click="open = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <x-icon name="bell" class="w-5 h-5" />
                    <span>Notifikasi</span>
                    @if ($unreadCount > 0)
                        <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style="background: var(--kl-primary)">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>
            @endauth
        </div>

        @guest
            <div class="px-4 py-3 border-t space-y-2" style="border-color: #F0E8E0">
                <a href="{{ route('login') }}" wire:navigate
                   @click="open = false"
                   class="block w-full text-center px-4 py-2.5 rounded-xl border text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                   style="border-color: #F0E8E0">
                    Masuk
                </a>
                <a href="{{ route('register') }}" wire:navigate
                   @click="open = false"
                   class="kl-btn-primary justify-center w-full text-sm py-2.5">
                    Daftar Gratis
                </a>
            </div>
        @endguest
    </div>
</nav>


