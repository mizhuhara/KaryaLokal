<?php

use Livewire\Volt\Component;
use App\Models\SellerProfile;

new class extends Component {
    public $latitude = null;
    public $longitude = null;
    public $radius = 5;
    public $sellers = [];
    public $locationDenied = false;

    public function mount()
    {
        $this->loadNearby();
    }

    public function loadNearby()
    {
        if ($this->latitude && $this->longitude) {
            $this->sellers = $this->getNearestSellers();
        }
    }

    public function getNearestSellers()
    {
        $sellers = SellerProfile::where('is_verified', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearby = $sellers->map(function ($seller) {
            $distance = $this->calculateDistance(
                $this->latitude,
                $this->longitude,
                $seller->latitude,
                $seller->longitude
            );
            return [
                'seller' => $seller,
                'distance' => $distance,
            ];
        })
        ->filter(fn($item) => $item['distance'] <= $this->radius)
        ->sortBy('distance')
        ->values();

        if ($nearby->isEmpty() && $this->radius < 50) {
            return $this->expandSearch();
        }

        return $nearby;
    }

    public function expandSearch()
    {
        $oldRadius = $this->radius;
        $this->radius = min($this->radius * 2, 500);

        $sellers = SellerProfile::where('is_verified', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return $sellers->map(function ($seller) {
            $distance = $this->calculateDistance(
                $this->latitude,
                $this->longitude,
                $seller->latitude,
                $seller->longitude
            );
            return [
                'seller' => $seller,
                'distance' => $distance,
            ];
        })
        ->filter(fn($item) => $item['distance'] <= $this->radius)
        ->sortBy('distance')
        ->values();
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return $earth_radius * $c;
    }

    public function with()
    {
        return [
            'sellers' => $this->sellers,
            'hasLocation' => $this->latitude && $this->longitude,
            'currentRadius' => $this->radius,
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-2">Pengrajin Terdekat</h1>
            <p class="text-gray-600 mb-8">Temukan pengrajin handmade di sekitar Anda</p>

            <!-- Location Request -->
            @if (!$hasLocation)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded">
                    <h3 class="font-semibold text-blue-900 mb-2">Aktifkan Lokasi</h3>
                    <p class="text-blue-800 text-sm mb-4">Izinkan akses ke lokasi Anda untuk menemukan pengrajin terdekat</p>
                    <button
                        onclick="navigator.geolocation.getCurrentPosition(
                            pos => {
                                @this.latitude = pos.coords.latitude;
                                @this.longitude = pos.coords.longitude;
                                @this.loadNearby();
                            },
                            () => @this.set('locationDenied', true)
                        )"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold"
                    >
                        <x-icon name="map-pin" class="w-4 h-4 inline-block align-text-bottom" /> Gunakan Lokasi Saya
                    </button>
                </div>

                @if ($locationDenied)
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-8 rounded">
                        <p class="text-yellow-800 text-sm font-medium flex items-center gap-2">
                            <x-icon name="map-pin" class="w-4 h-4" /> Akses lokasi ditolak. Masukkan lokasi secara manual di bawah:
                        </p>
                    </div>
                @endif
            @endif

            <!-- Manual Location Input -->
            @if (!$hasLocation)
                <div class="bg-white rounded-2xl shadow p-6 mb-8" style="border: 1px solid #F0E8E0">
                    <div class="flex items-center gap-2 mb-3">
                        <x-icon name="pencil-square" class="w-5 h-5" style="color: var(--kl-primary)" />
                        <h3 class="font-semibold font-jakarta text-gray-800">Atau masukkan lokasi manual</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="number" step="any" wire:model="latitude" placeholder="Latitude (contoh: -6.2088)"
                               class="kl-input text-sm" />
                        <div class="flex gap-2">
                            <input type="number" step="any" wire:model="longitude" placeholder="Longitude (contoh: 106.8456)"
                                   class="kl-input text-sm" />
                            <button wire:click="loadNearby"
                                    class="kl-btn-primary py-2.5 px-5 text-sm whitespace-nowrap shrink-0">
                                Cari
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Contoh: Jakarta Selatan = -6.2088, 106.8456 · Bandung = -6.9175, 107.6191</p>
                </div>
            @endif

            <!-- Results -->
            @if ($hasLocation)
                <div class="mb-8">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <p class="text-green-800 text-sm">
                            <strong>Menampilkan pengrajin dalam radius {{ $currentRadius }} km</strong>
                        </p>
                    </div>
                </div>

                @if ($sellers->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- List -->
                        <div class="lg:col-span-2">
                            <div class="space-y-4">
                                @foreach ($sellers as $item)
                                    <a href="{{ route('seller-store', $item['seller']->id) }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 class="text-lg font-semibold">{{ $item['seller']->shop_name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $item['seller']->city }}, {{ $item['seller']->province }}</p>
                                            </div>
                                            @if ($item['seller']->is_verified)
                                                <span class="kl-badge kl-badge-green">
                                                    <x-icon name="shield-check" class="w-3.5 h-3.5" /> Verified
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <p class="text-2xl font-bold text-orange-600">{{ number_format($item['distance'], 1) }} km</p>
                                            <p class="text-sm text-gray-600">{{ $item['seller']->address }}</p>
                                        </div>

                                        <div class="flex gap-2 flex-wrap mb-4">
                                            @if ($item['seller']->pickup_available)
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Pickup</span>
                                            @endif
                                            @if ($item['seller']->delivery_available)
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Delivery</span>
                                            @endif
                                            @if ($item['seller']->custom_order_available)
                                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Custom</span>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-700 line-clamp-2">{{ $item['seller']->description ?? 'Tidak ada deskripsi' }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="bg-white rounded-lg shadow p-6 h-fit">
                            <h3 class="font-semibold mb-4">Ringkasan</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600">Pengrajin Ditemukan</p>
                                    <p class="text-3xl font-bold">{{ $sellers->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Radius Pencarian</p>
                                    <p class="text-3xl font-bold">{{ $currentRadius }} km</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Terdekat</p>
                                    <p class="text-2xl font-bold text-orange-600">
                                        {{ number_format($sellers->first()['distance'] ?? 0, 1) }} km
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <x-icon name="map-pin" class="w-10 h-10 mx-auto" style="color: #D4C5BC" />
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Belum Ada Pengrajin</h3>
                        <p class="text-gray-600 text-sm">Maaf, belum ada pengrajin terverifikasi di area Anda. Coba area lain atau kembali lagi nanti.</p>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="flex items-center justify-center mb-4">
                        <x-icon name="map-pin" class="w-10 h-10" style="color: #D4C5BC" />
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Bagikan Lokasi Anda</h3>
                    <p class="text-gray-600 text-sm mb-6">Aktifkan akses lokasi untuk menemukan pengrajin terdekat</p>
                    <button
                        onclick="navigator.geolocation.getCurrentPosition(
                            pos => {
                                @this.latitude = pos.coords.latitude;
                                @this.longitude = pos.coords.longitude;
                                @this.loadNearby();
                            },
                            () => @this.set('locationDenied', true)
                        )"
                        class="px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold"
                    >
                        <x-icon name="map-pin" class="w-4 h-4 inline-block align-text-bottom" /> Aktifkan Lokasi
                    </button>
                </div>
            @endif
        </div>
    </div>

</div>
