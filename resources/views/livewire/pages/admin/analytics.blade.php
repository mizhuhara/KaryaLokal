<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\SellerProfile;

new class extends Component {
    public function with()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        return [
            'totalRevenue' => Order::where('status', 'completed')->sum('total_amount'),
            'monthlyRevenue' => Order::where('status', 'completed')->where('created_at', '>=', $startOfMonth)->sum('total_amount'),
            'totalOrders' => Order::count(),
            'monthlyOrders' => Order::where('created_at', '>=', $startOfMonth)->count(),
            'totalUsers' => User::count(),
            'newUsersThisMonth' => User::where('created_at', '>=', $startOfMonth)->count(),
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('is_active', true)->count(),
            'totalSellers' => SellerProfile::count(),
            'verifiedSellers' => SellerProfile::where('is_verified', true)->count(),
            'recentOrders' => Order::with('user', 'seller')->latest()->limit(10)->get(),
            'topSellers' => SellerProfile::withCount('products')->orderByDesc('products_count')->limit(5)->get(),
            'ordersByStatus' => Order::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status'),
            'revenueByMonth' => Order::where('status', 'completed')
                ->selectRaw('strftime("%Y-%m", created_at) as month, sum(total_amount) as revenue')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">📊 Platform Analytics</h1>
                <p class="text-gray-600 text-sm">Metrik performa dan statistik keseluruhan</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pendapatan</p>
                    <p class="text-2xl font-bold font-jakarta text-emerald-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini: Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pesanan</p>
                    <p class="text-2xl font-bold font-jakarta text-gray-900 mt-1">{{ $totalOrders }}</p>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini: {{ $monthlyOrders }}</p>
                </div>
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Users</p>
                    <p class="text-2xl font-bold font-jakarta text-gray-900 mt-1">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 mt-1">Baru bulan ini: {{ $newUsersThisMonth }}</p>
                </div>
                <div class="kl-card p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk Aktif</p>
                    <p class="text-2xl font-bold font-jakarta text-gray-900 mt-1">{{ $activeProducts }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total: {{ $totalProducts }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Orders by Status -->
                <div class="kl-card p-6">
                    <h3 class="font-bold text-lg font-jakarta mb-4">Pesanan per Status</h3>
                    <div class="space-y-3">
                        @foreach ($ordersByStatus as $status => $count)
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium capitalize text-gray-700">{{ $status }}</span>
                                <div class="flex items-center gap-3">
                                    <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-kl-primary rounded-full" style="width: {{ $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="font-semibold text-sm w-8 text-right text-gray-900">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Revenue by Month -->
                <div class="kl-card p-6">
                    <h3 class="font-bold text-lg font-jakarta mb-4">Pendapatan Bulanan</h3>
                    @if ($revenueByMonth->count() > 0)
                        <div class="space-y-3">
                            @foreach ($revenueByMonth as $item)
                                <div class="flex justify-between items-center py-1 border-b border-gray-50 last:border-0">
                                    <span class="text-sm font-medium text-gray-700">{{ $item->month }}</span>
                                    <span class="font-bold text-emerald-600">Rp {{ number_format($item->revenue, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada data pendapatan</p>
                    @endif
                </div>

                <!-- Top Sellers -->
                <div class="kl-card p-6">
                    <h3 class="font-bold text-lg font-jakarta mb-4">Top Sellers (Jumlah Produk)</h3>
                    @if ($topSellers->count() > 0)
                        <div class="space-y-3">
                            @foreach ($topSellers as $seller)
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-sm text-gray-900">{{ $seller->shop_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $seller->city }}</p>
                                    </div>
                                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-bold">{{ $seller->products_count }} produk</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Belum ada seller</p>
                    @endif
                </div>

                <!-- Seller Stats -->
                <div class="kl-card p-6">
                    <h3 class="font-bold text-lg font-jakarta mb-4">Statistik Seller</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Seller</span>
                            <span class="font-bold font-jakarta text-gray-900">{{ $totalSellers }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Terverifikasi</span>
                            <span class="font-bold font-jakarta text-emerald-600">{{ $verifiedSellers }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Belum Verifikasi</span>
                            <span class="font-bold font-jakarta text-yellow-600">{{ $totalSellers - $verifiedSellers }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $totalSellers > 0 ? ($verifiedSellers / $totalSellers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="kl-card overflow-hidden">
                <div class="px-6 py-4 border-b border-kl">
                    <h3 class="font-bold text-lg font-jakarta">Pesanan Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-kl">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-bold text-gray-700 uppercase">Nomor</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-gray-700 uppercase">Pembeli</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-gray-700 uppercase">Penjual</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-gray-700 uppercase">Total</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-gray-700 uppercase">Status</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-gray-700 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-kl">
                            @foreach ($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-900">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $order->user->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $order->seller->shop_name ?? '-' }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                        ">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
