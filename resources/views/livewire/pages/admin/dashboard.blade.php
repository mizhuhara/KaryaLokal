<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Report;
use App\Models\SellerVisit;

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
            'totalVisitors' => SellerVisit::count(),
            'uniqueVisitors' => SellerVisit::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'recentVisitors' => SellerVisit::with('user', 'sellerProfile')->latest()->limit(8)->get(),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">⚙️ Admin Dashboard</h1>
                <p class="text-gray-600 text-sm">Pantau dan kelola seluruh aktivitas platform</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 kl-stagger">
                <div class="kl-card p-5 animate-fade-in-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Users</p>
                            <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalUsers }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light))">👥</div>
                    </div>
                </div>
                <div class="kl-card p-5 animate-fade-in-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Sellers</p>
                            <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalSellers }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, var(--kl-secondary), #40916C)">🏪</div>
                    </div>
                </div>
                <div class="kl-card p-5 animate-fade-in-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Produk</p>
                            <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalProducts }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, #F4A261, #FFD166)">📦</div>
                    </div>
                </div>
                <div class="kl-card p-5 animate-fade-in-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pesanan</p>
                            <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalOrders }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, #5B21B6, #7C3AED)">🛒</div>
                    </div>
                </div>
            </div>

            <!-- Revenue & Alerts -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Pendapatan</p>
                    <p class="text-2xl font-bold" style="color: var(--kl-secondary)">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">30 hari: Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Verifikasi Tertunda</p>
                    <p class="text-2xl font-bold" style="color: var(--kl-primary)">{{ $pendingVerification }}</p>
                    @if ($pendingVerification > 0)
                        <a href="{{ route('admin.sellers') }}" wire:navigate class="text-xs font-semibold mt-1 inline-block" style="color: var(--kl-primary)">Lihat Semua →</a>
                    @endif
                </div>
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Laporan Pending</p>
                    <p class="text-2xl font-bold text-red-600">{{ $totalReports }}</p>
                    @if ($totalReports > 0)
                        <a href="{{ route('admin.reports') }}" wire:navigate class="text-xs font-semibold text-red-600 mt-1 inline-block">Lihat Semua →</a>
                    @endif
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="kl-card mb-8">
                <div class="px-6 py-4 border-b border-kl">
                    <h3 class="text-lg font-bold font-jakarta">📋 Pesanan Terbaru</h3>
                </div>
                @if ($recentOrders->count() > 0)
                    <div class="divide-y divide-kl">
                        @foreach ($recentOrders as $order)
                            <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold text-white shrink-0"
                                         style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light))">
                                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm font-jakarta">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->user->name }} → {{ $order->seller->shop_name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-sm" style="color: var(--kl-primary)">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold
                                        @if ($order->status === 'completed') bg-emerald-100 text-emerald-800 border border-emerald-200
                                        @elseif ($order->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada pesanan</div>
                @endif
            </div>

            <!-- Recent Visitors -->
            <div class="kl-card mb-8">
                <div class="px-6 py-4 border-b border-kl flex items-center justify-between">
                    <h3 class="text-lg font-bold font-jakarta flex items-center gap-2">
                        <x-icon name="users" class="w-5 h-5" style="color: var(--kl-primary)" /> Pengunjung Terbaru
                    </h3>
                    <span class="text-xs text-gray-500">{{ $uniqueVisitors }} user unik · {{ $totalVisitors }} total kunjungan</span>
                </div>
                @if ($recentVisitors->count() > 0)
                    <div class="divide-y divide-kl">
                        @foreach ($recentVisitors as $visit)
                            <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                         style="background: linear-gradient(135deg, {{ $visit->user_id ? 'var(--kl-primary), var(--kl-primary-light)' : '#6B7280, #9CA3AF' }})">
                                        {{ $visit->user_id ? strtoupper(substr($visit->user->name ?? '?', 0, 1)) : 'G' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-sm text-gray-800 truncate">
                                            {{ $visit->user_id ? $visit->user->name : 'Pengunjung (Guest)' }}
                                            @if (!$visit->user_id) 
                                                <span class="text-[10px] font-medium bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full ml-1">IP: {{ $visit->ip_address }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $visit->sellerProfile->shop_name ?? '—' }}
                                            @if ($visit->user_agent)
                                                <span class="text-gray-400"> · {{ substr($visit->user_agent, 0, 60) }}{{ strlen($visit->user_agent) > 60 ? '...' : '' }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-4">
                                    <p class="text-xs text-gray-500">{{ $visit->created_at->format('d M H:i') }}</p>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-orange-50 text-kl-primary border border-orange-100">{{ $visit->page }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-8 text-center">
                        <x-icon name="users" class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                        <p class="text-sm text-gray-500">Belum ada kunjungan</p>
                    </div>
                @endif
            </div>

            <!-- Admin Menu -->
            <div class="kl-card p-6">
                <h3 class="text-lg font-bold font-jakarta mb-4">⚡ Menu Admin</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <a href="{{ route('admin.users') }}" wire:navigate class="kl-card p-4 text-center kl-hover-lift group">
                        <span class="text-2xl block mb-2">👥</span>
                        <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Users</span>
                    </a>
                    <a href="{{ route('admin.sellers') }}" wire:navigate class="kl-card p-4 text-center kl-hover-lift group">
                        <span class="text-2xl block mb-2">🏪</span>
                        <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Sellers</span>
                    </a>
                    <a href="{{ route('admin.products') }}" wire:navigate class="kl-card p-4 text-center kl-hover-lift group">
                        <span class="text-2xl block mb-2">📦</span>
                        <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Produk</span>
                    </a>
                    <a href="{{ route('admin.orders') }}" wire:navigate class="kl-card p-4 text-center kl-hover-lift group">
                        <span class="text-2xl block mb-2">🛒</span>
                        <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Pesanan</span>
                    </a>
                    <a href="{{ route('admin.reviews') }}" wire:navigate class="kl-card p-4 text-center kl-hover-lift group">
                        <span class="text-2xl block mb-2">⭐</span>
                        <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Review</span>
                    </a>
                    <a href="{{ route('admin.reports') }}" wire:navigate class="kl-card p-4 text-center kl-hover-lift group">
                        <span class="text-2xl block mb-2">🚨</span>
                        <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
