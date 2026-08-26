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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">Analytics</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini: Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Pesanan</p>
                    <p class="text-2xl font-bold">{{ $totalOrders }}</p>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini: {{ $monthlyOrders }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 mt-1">Baru bulan ini: {{ $newUsersThisMonth }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Produk Aktif</p>
                    <p class="text-2xl font-bold">{{ $activeProducts }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total: {{ $totalProducts }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Orders by Status -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-4">Pesanan per Status</h3>
                    @foreach ($ordersByStatus as $status => $count)
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm capitalize">{{ $status }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-32 h-2 bg-gray-200 rounded-full">
                                    <div class="h-full bg-orange-500 rounded-full" style="width: {{ $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0 }}%"></div>
                                </div>
                                <span class="font-semibold text-sm w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Revenue by Month -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-4">Pendapatan Bulanan</h3>
                    @if ($revenueByMonth->count() > 0)
                        @foreach ($revenueByMonth as $item)
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm">{{ $item->month }}</span>
                                <span class="font-semibold text-green-600">Rp {{ number_format($item->revenue, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">Belum ada data</p>
                    @endif
                </div>

                <!-- Top Sellers -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-4">Top Sellers (Produk)</h3>
                    @foreach ($topSellers as $seller)
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <p class="font-semibold text-sm">{{ $seller->shop_name }}</p>
                                <p class="text-xs text-gray-500">{{ $seller->city }}</p>
                            </div>
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">{{ $seller->products_count }} produk</span>
                        </div>
                    @endforeach
                </div>

                <!-- Seller Stats -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-4">Seller Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm">Total Seller</span>
                            <span class="font-bold">{{ $totalSellers }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Terverifikasi</span>
                            <span class="font-bold text-green-600">{{ $verifiedSellers }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Belum Verifikasi</span>
                            <span class="font-bold text-yellow-600">{{ $totalSellers - $verifiedSellers }}</span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 rounded-full">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $totalSellers > 0 ? ($verifiedSellers / $totalSellers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold text-lg mb-4">Pesanan Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b">
                            <tr>
                                <th class="text-left py-2">Nomor</th>
                                <th class="text-left py-2">Pembeli</th>
                                <th class="text-left py-2">Penjual</th>
                                <th class="text-left py-2">Total</th>
                                <th class="text-left py-2">Status</th>
                                <th class="text-left py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td class="py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                    <td class="py-2">{{ $order->user->name }}</td>
                                    <td class="py-2">{{ $order->seller->shop_name ?? '-' }}</td>
                                    <td class="py-2 font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        ">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td class="py-2 text-gray-500">{{ $order->created_at->format('d M') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>