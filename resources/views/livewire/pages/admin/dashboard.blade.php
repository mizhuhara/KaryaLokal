<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Report;

new class extends Component {
    public function with()
    {
        return [
            'totalUsers' => User::count(),
            'totalSellers' => SellerProfile::count(),
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalReviews' => Review::count(),
            'totalReports' => Report::where('status', 'pending')->count(),
            'pendingVerification' => SellerProfile::where('is_verified', false)->count(),
            'recentOrders' => Order::with('user', 'seller')->latest()->limit(5)->get(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'monthlyOrders' => Order::where('created_at', '>=', now()->subDays(30))->count(),
            'monthlyRevenue' => Order::where('status', 'completed')->where('created_at', '>=', now()->subDays(30))->sum('total_amount'),
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
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
                            <p class="text-gray-600 text-sm">Total Produk</p>
                            <p class="text-3xl font-bold">{{ $totalProducts }}</p>
                        </div>
                        <span class="text-4xl">📦</span>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Pesanan</p>
                            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
                        </div>
                        <span class="text-4xl">🛒</span>
                    </div>
                </div>
            </div>

            <!-- Revenue & Alerts -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-2">Total Pendapatan</h3>
                    <p class="text-3xl font-bold text-green-600">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">30 hari terakhir: Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-2">Verifikasi Tertunda</h3>
                    <p class="text-3xl font-bold text-orange-600">{{ $pendingVerification }}</p>
                    @if ($pendingVerification > 0)
                        <a href="{{ route('admin.sellers') }}" class="text-orange-600 hover:text-orange-700 text-sm mt-2 inline-block">Lihat Semua →</a>
                    @endif
                </div>
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-2">Laporan Pending</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $totalReports }}</p>
                    @if ($totalReports > 0)
                        <a href="{{ route('admin.reports') }}" class="text-red-600 hover:text-red-700 text-sm mt-2 inline-block">Lihat Semua →</a>
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
                                        @if ($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif ($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
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
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <a href="{{ route('admin.users') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">👥</span>
                        <span class="font-semibold text-sm">Users</span>
                    </a>
                    <a href="{{ route('admin.sellers') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">🏪</span>
                        <span class="font-semibold text-sm">Sellers</span>
                    </a>
                    <a href="{{ route('admin.products') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">📦</span>
                        <span class="font-semibold text-sm">Produk</span>
                    </a>
                    <a href="{{ route('admin.orders') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">🛒</span>
                        <span class="font-semibold text-sm">Pesanan</span>
                    </a>
                    <a href="{{ route('admin.reviews') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">⭐</span>
                        <span class="font-semibold text-sm">Review</span>
                    </a>
                    <a href="{{ route('admin.reports') }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                        <span class="text-2xl block mb-2">🚨</span>
                        <span class="font-semibold text-sm">Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
