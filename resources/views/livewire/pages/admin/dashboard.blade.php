<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Order;

new class extends Component {
    public function with()
    {
        return [
            'totalUsers' => User::count(),
            'totalSellers' => SellerProfile::count(),
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'pendingVerification' => SellerProfile::where('is_verified', false)->count(),
            'recentOrders' => Order::with('user', 'seller')->latest()->limit(5)->get(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
        ];
    }
};

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Users</p>
                            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                        </div>
                        <span class="text-4xl">👥</span>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Sellers</p>
                            <p class="text-3xl font-bold">{{ $totalSellers }}</p>
                        </div>
                        <span class="text-4xl">🏪</span>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Products</p>
                            <p class="text-3xl font-bold">{{ $totalProducts }}</p>
                        </div>
                        <span class="text-4xl">📦</span>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Orders</p>
                            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
                        </div>
                        <span class="text-4xl">🛒</span>
                    </div>
                </div>
            </div>

            <!-- Revenue & Pending Verification -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Total Pendapatan</h3>
                    <p class="text-4xl font-bold text-green-600">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Verifikasi Tertunda</h3>
                    <p class="text-4xl font-bold text-orange-600">{{ $pendingVerification }}</p>
                    @if ($pendingVerification > 0)
                        <a href="{{ route('admin.sellers') }}" class="text-orange-600 hover:text-orange-700 text-sm mt-2 inline-block">
                            Lihat Semua →
                        </a>
                    @endif
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold">Pesanan Terbaru</h3>
                </div>
                @if ($recentOrders->count() > 0)
                    <div class="divide-y">
                        @foreach ($recentOrders as $order)
                            <div class="p-4 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->user->name }} → {{ $order->seller->shop_name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <span class="inline-block px-2 py-1 text-xs rounded-full
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">Belum ada pesanan</div>
                @endif
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Menu Admin</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.users') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">👥</span>
                        <span class="font-semibold">Users</span>
                    </a>
                    <a href="{{ route('admin.sellers') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">🏪</span>
                        <span class="font-semibold">Sellers</span>
                    </a>
                    <a href="{{ route('admin.products') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">📦</span>
                        <span class="font-semibold">Produk</span>
                    </a>
                    <a href="{{ route('admin.categories') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">🏷️</span>
                        <span class="font-semibold">Kategori</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
