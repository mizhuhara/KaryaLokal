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
            'mapCenterLat' => $this->latitude,
            'mapCenterLng' => $this->longitude,
        ];
    }
};

?>

    <div>
<div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-white border-b p-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">Peta Pengrajin</h1>
                <a href="{{ route('nearby') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    ↔ Daftar
                </a>
            </div>
        </div>

        <!-- Location Request -->
        @if (!$hasLocation)
            <div class="bg-blue-50 border-b p-4 text-center">
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
                    📍 Aktifkan Lokasi
                </button>
            </div>
        @endif

        <!-- Map Container -->
        <div class="flex-1 relative bg-gray-200" x-data="{ mapInitialized: false }" x-init="
            if (!mapInitialized && $wire.mapCenterLat) {
                setTimeout(() => {
                    const map = L.map('map').setView([$wire.mapCenterLat, $wire.mapCenterLng], 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    L.circleMarker([$wire.mapCenterLat, $wire.mapCenterLng], {
                        radius: 8,
                        fillColor: '#3B82F6',
                        color: '#1E40AF',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map).bindPopup('📍 Lokasi Anda');

                    @foreach ($sellers as $item)
                        L.marker([{{ $item['seller']->latitude }}, {{ $item['seller']->longitude }}])
                            .addTo(map)
                            .bindPopup(
                                '<div class=\"text-sm\">' +
                                '<strong>{{ $item['seller']->shop_name }}</strong><br/>' +
                                '📍 {{ number_format($item['distance'], 1) }} km<br/>' +
                                '<a href=\"{{ route('seller-store', $item['seller']->id) }}\" class=\"text-blue-600 hover:underline\">Lihat Toko</a>' +
                                '</div>'
                            );
                    @endforeach

                    mapInitialized = true;
                }, 100);
            }
        ">
            <div id="map" class="w-full h-full"></div>

            @if (!$hasLocation)
                <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                    <div class="bg-white rounded-lg p-8 text-center max-w-md">
                        <div class="text-4xl mb-4">📍</div>
                        <h2 class="text-xl font-bold mb-2">Bagikan Lokasi Anda</h2>
                        <p class="text-gray-600 mb-6">Aktifkan akses lokasi untuk melihat peta pengrajin terdekat</p>
                        <button
                            onclick="navigator.geolocation.getCurrentPosition(
                                pos => {
                                    @this.latitude = pos.coords.latitude;
                                    @this.longitude = pos.coords.longitude;
                                    @this.loadNearby();
                                },
                                () => @this.set('locationDenied', true)
                            )"
                            class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold"
                        >
                            Aktifkan Lokasi
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Legend -->
        @if ($hasLocation && $sellers->count() > 0)
            <div class="bg-white border-t p-4">
                <div class="max-w-7xl mx-auto text-sm text-gray-600">
                    <p>
                        <span class="inline-block w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                        Lokasi Anda
                        <span class="mx-4">|</span>
                        <span class="inline-block w-3 h-3 bg-red-500 rounded mr-2"></span>
                        Pengrajin ({{ $sellers->count() }} ditemukan dalam {{ $currentRadius }} km)
                    </p>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    @endpush

</div>
