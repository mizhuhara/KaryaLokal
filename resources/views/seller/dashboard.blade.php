<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Dashboard Penjual
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $seller = auth()->user()->sellerProfile;
                $totalProducts = $seller?->products()->count() ?? 0;
                $totalProductsWithImages = $seller?->products()->whereHas('images')->count() ?? 0;
                $totalOrders = $seller?->products()->with('orderItems')->get()->sum(fn($p) => $p->orderItems->count()) ?? 0;
                $avgRating = $seller?->products()->with('reviews')->get()->flatMap(fn($p) => $p->reviews)->avg('rating') ?? 0;
                $totalVisitors = $seller?->visits()->count() ?? 0;
                $monthlyVisitors = $seller?->visits()->where('created_at', '>=', now()->subDays(30))->count() ?? 0;
                $dailyVisitors = $seller?->visits()->where('created_at', '>=', now()->subDays(7))
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get() ?? collect();
            @endphp

            @if (!$seller)
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <p class="text-neutral-600 mb-4">Anda belum mendaftar sebagai penjual.</p>
                    <a href="{{ route('seller.register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Daftar Sebagai Penjual
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-neutral-600 text-sm">Total Produk</p>
                                <p class="text-3xl font-bold text-neutral-900">{{ $totalProducts }}</p>
                            </div>
                            <div class="text-4xl text-blue-500">📦</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-neutral-600 text-sm">Total Pesanan</p>
                                <p class="text-3xl font-bold text-neutral-900">{{ $totalOrders }}</p>
                            </div>
                            <div class="text-4xl text-green-500">📊</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-neutral-600 text-sm">Rating Rata-rata</p>
                                <p class="text-3xl font-bold text-neutral-900">{{ number_format($avgRating, 1) }}</p>
                                <div class="flex gap-1 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="text-sm {{ $i <= round($avgRating) ? '⭐' : '☆' }}"></span>
                                    @endfor
                                </div>
                            </div>
                            <div class="text-4xl">⭐</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-neutral-600 text-sm">Pengunjung</p>
                                <p class="text-3xl font-bold text-neutral-900">{{ $totalVisitors }}</p>
                                <p class="text-xs text-gray-500 mt-1">30 hari: {{ $monthlyVisitors }}</p>
                            </div>
                            <div class="text-4xl text-purple-500">👁️</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Informasi Toko</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-neutral-600">Nama Toko</p>
                                <p class="font-medium">{{ $seller->shop_name }}</p>
                            </div>
                            <div>
                                <p class="text-neutral-600">Lokasi</p>
                                <p class="font-medium">{{ $seller->city }}, {{ $seller->province }}</p>
                            </div>
                            <div>
                                <p class="text-neutral-600">Layanan</p>
                                <p class="text-xs space-x-2">
                                    @if ($seller->pickup_available)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded">Pickup</span>
                                    @endif
                                    @if ($seller->delivery_available)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded">Delivery</span>
                                    @endif
                                    @if ($seller->custom_order_available)
                                        <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded">Custom</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('seller.profile') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            Edit Profil
                        </a>
                    </div>

                    <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">📊 Pengunjung 7 Hari</h3>
                        @if ($dailyVisitors->count() > 0)
                            <div class="space-y-2">
                                @foreach ($dailyVisitors as $day)
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 w-16">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-4 overflow-hidden">
                                            <div class="bg-orange-500 h-full rounded-full" style="width: {{ min(($day->count / max($dailyVisitors->max('count'), 1)) * 100, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold w-8 text-right">{{ $day->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Belum ada data pengunjung</p>
                        @endif
                    </div>
                </div>

                <!-- Manajemen -->
                <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Manajemen</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <a href="{{ route('seller.products') }}" class="block px-4 py-3 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 text-center font-semibold">
                            📦 Produk
                        </a>
                        <a href="{{ route('seller.orders') }}" class="block px-4 py-3 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 text-center font-semibold">
                            📋 Pesanan
                        </a>
                        <a href="{{ route('seller.verification') }}" class="block px-4 py-3 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 text-center font-semibold">
                            ✓ Verifikasi
                        </a>
                    </div>
                </div>

                @if ($totalProducts === 0)
                    <div class="bg-blue-50 border border-blue-200 overflow-hidden shadow-card sm:rounded-lg p-6">
                        <h3 class="font-semibold text-blue-900 mb-2">Mulai Berjualan</h3>
                        <p class="text-blue-800 text-sm mb-4">Tambahkan produk pertama Anda untuk mulai berjualan.</p>
                        <a href="{{ route('seller.products') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Tambah Produk
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
