<x-app-layout>
    <div class="min-h-screen bg-kl-warm">
        <!-- Header -->
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">🏪 Dashboard Toko</h1>
                <p class="text-gray-600 text-sm">Kelola toko dan pantau performa penjualan</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            @php
                $seller = auth()->user()?->sellerProfile;
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
                <div class="kl-card p-12 text-center">
                    <div class="text-6xl mb-4">🏪</div>
                    <h3 class="kl-section-title">Anda Belum Mendaftar Sebagai Penjual</h3>
                    <p class="text-gray-500 text-sm mb-6">Daftar sekarang untuk mulai berjualan di KaryaLokal</p>
                    <a href="{{ route('seller.register') }}" wire:navigate class="kl-btn-primary text-sm py-2.5">
                        Daftar Sebagai Penjual
                    </a>
                </div>
            @else
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 kl-stagger">
                    <div class="kl-card p-5 animate-fade-in-up">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Produk</p>
                                <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalProducts }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, var(--kl-primary), var(--kl-primary-light))">📦</div>
                        </div>
                    </div>

                    <div class="kl-card p-5 animate-fade-in-up">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pesanan</p>
                                <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalOrders }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, var(--kl-secondary), #40916C)">📋</div>
                        </div>
                    </div>

                    <div class="kl-card p-5 animate-fade-in-up">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rating Rata-rata</p>
                                <p class="text-2xl font-bold mt-1 font-jakarta">{{ number_format($avgRating, 1) }} ⭐</p>
                                <div class="flex gap-0.5 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="text-[10px] {{ $i <= round($avgRating) ? 'opacity-100' : 'opacity-30' }}">⭐</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, #F4A261, #FFD166)">⭐</div>
                        </div>
                    </div>

                    <div class="kl-card p-5 animate-fade-in-up">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengunjung</p>
                                <p class="text-2xl font-bold mt-1 font-jakarta">{{ $totalVisitors }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">30 hari: {{ $monthlyVisitors }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, #5B21B6, #7C3AED)">👁️</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Shop Info -->
                    <div class="kl-card p-6">
                        <h3 class="text-lg font-bold font-jakarta mb-4">🏪 Informasi Toko</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama Toko</span>
                                <span class="font-semibold">{{ $seller->shop_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lokasi</span>
                                <span class="font-semibold">{{ $seller->city }}, {{ $seller->province }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 pt-2">
                                @if ($seller->pickup_available)
                                    <span class="kl-badge kl-badge-blue">🏠 Pickup</span>
                                @endif
                                @if ($seller->delivery_available)
                                    <span class="kl-badge kl-badge-blue">🚚 Delivery</span>
                                @endif
                                @if ($seller->custom_order_available)
                                    <span class="kl-badge kl-badge-purple">🎨 Custom</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('seller.profile') }}" wire:navigate class="mt-4 block text-center kl-btn-ghost text-sm font-semibold" style="color: var(--kl-primary)">
                            Edit Profil →
                        </a>
                    </div>

                    <!-- Visitor Chart -->
                    <div class="kl-card p-6">
                        <h3 class="text-lg font-bold font-jakarta mb-4">📊 Pengunjung 7 Hari</h3>
                        @if ($dailyVisitors->count() > 0)
                            <div class="space-y-2.5">
                                @foreach ($dailyVisitors as $day)
                                    <div class="flex items-center gap-3">
                                        <span class="text-[11px] font-semibold text-gray-500 w-14 shrink-0">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
                                        <div class="flex-1 bg-gray-100 rounded-full h-3.5 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ min(($day->count / max($dailyVisitors->max('count'), 1)) * 100, 100) }}%; background: linear-gradient(90deg, var(--kl-primary), var(--kl-primary-light))"></div>
                                        </div>
                                        <span class="text-xs font-bold w-8 text-right" style="color: var(--kl-primary)">{{ $day->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm text-center py-6">Belum ada data pengunjung</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="kl-card p-6 mb-8">
                    <h3 class="text-lg font-bold font-jakarta mb-4">⚡ Menu Cepat</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <a href="{{ route('seller.products') }}" class="kl-card p-4 text-center kl-hover-lift group">
                            <span class="text-2xl block mb-2">📦</span>
                            <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Produk</span>
                        </a>
                        <a href="{{ route('seller.orders') }}" class="kl-card p-4 text-center kl-hover-lift group">
                            <span class="text-2xl block mb-2">📋</span>
                            <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Pesanan</span>
                        </a>
                        <a href="{{ route('seller.verification') }}" class="kl-card p-4 text-center kl-hover-lift group">
                            <span class="text-2xl block mb-2">✅</span>
                            <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Verifikasi</span>
                        </a>
                        <a href="{{ route('seller.custom-orders') }}" class="kl-card p-4 text-center kl-hover-lift group">
                            <span class="text-2xl block mb-2">🎨</span>
                            <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Custom Order</span>
                        </a>
                        <a href="{{ route('seller.subscription') }}" class="kl-card p-4 text-center kl-hover-lift group">
                            <span class="text-2xl block mb-2">💎</span>
                            <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Subscription</span>
                        </a>
                        <a href="{{ route('seller-store', $seller->id) }}" class="kl-card p-4 text-center kl-hover-lift group">
                            <span class="text-2xl block mb-2">🌐</span>
                            <span class="font-semibold text-sm text-gray-700 group-hover:text-kl-primary transition">Lihat Toko</span>
                        </a>
                    </div>
                </div>

                @if ($totalProducts === 0)
                    <div class="kl-card p-6" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE); border: 1px solid #BFDBFE">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0" style="background: var(--kl-primary); color: white">📦</div>
                            <div>
                                <h3 class="font-bold text-blue-900 mb-1 font-jakarta">Mulai Berjualan</h3>
                                <p class="text-sm text-blue-800 mb-3">Tambahkan produk pertama Anda untuk mulai berjualan di KaryaLokal.</p>
                                <a href="{{ route('seller.products') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:scale-105" style="background: var(--kl-primary)">
                                    Tambah Produk →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
