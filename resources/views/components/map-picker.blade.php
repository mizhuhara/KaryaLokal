@props(['lat' => null, 'lng' => null])

<div
    x-data="mapPicker({
        lat: @entangle($lat),
        lng: @entangle($lng)
    })"
    x-init="initMap()"
    class="w-full relative"
>
    <!-- Map Container -->
    <div id="map-container" wire:ignore class="w-full h-[400px] rounded-md shadow-sm border border-gray-300 z-10"></div>

    <!-- Search Box Overlay -->
    <div class="absolute top-2 left-2 right-2 md:w-1/2 z-[1000]">
        <div class="relative flex items-center">
            <input 
                type="text" 
                x-model="searchQuery" 
                @keydown.enter.prevent="searchLocation()"
                placeholder="Cari lokasi (contoh: Monas, Jakarta)"
                class="w-full pl-4 pr-12 py-2 border-none rounded shadow-md focus:ring-2 focus:ring-indigo-500 bg-white"
            >
            <button 
                type="button"
                @click="searchLocation()"
                class="absolute right-0 top-0 h-full px-3 text-gray-500 hover:text-indigo-600 rounded-r focus:outline-none"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <p x-show="searchError" x-text="searchError" class="mt-1 text-sm text-red-600 bg-white px-2 py-1 rounded shadow inline-block"></p>
    </div>

    <!-- Instructions -->
    <p class="mt-2 text-sm text-gray-500">
        Geser marker atau klik pada peta untuk menentukan lokasi presisi.
    </p>

    <!-- Dependencies -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mapPicker', ({lat, lng}) => ({
                lat: lat,
                lng: lng,
                map: null,
                marker: null,
                searchQuery: '',
                searchError: '',

                initMap() {
                    // Default to Indonesia center if no location
                    const initialLat = this.lat || -2.5489;
                    const initialLng = this.lng || 118.0149;
                    const initialZoom = this.lat ? 15 : 5;

                    this.map = L.map('map-container').setView([initialLat, initialLng], initialZoom);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(this.map);

                    if (this.lat && this.lng) {
                        this.setMarker(this.lat, this.lng);
                    }

                    this.map.on('click', (e) => {
                        this.updateLocation(e.latlng.lat, e.latlng.lng);
                    });
                },

                setMarker(lat, lng) {
                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng], {draggable: true}).addTo(this.map);
                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.updateLocation(pos.lat, pos.lng);
                        });
                    }
                },

                updateLocation(lat, lng) {
                    // Truncate to 7 decimal places for our DB
                    this.lat = parseFloat(lat.toFixed(7));
                    this.lng = parseFloat(lng.toFixed(7));
                    this.setMarker(this.lat, this.lng);
                },

                async searchLocation() {
                    if (!this.searchQuery) return;
                    this.searchError = '';

                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await response.json();

                        if (data && data.length > 0) {
                            const result = data[0];
                            const rLat = parseFloat(result.lat);
                            const rLng = parseFloat(result.lon);
                            
                            this.map.setView([rLat, rLng], 15);
                            this.updateLocation(rLat, rLng);
                        } else {
                            this.searchError = 'Lokasi tidak ditemukan.';
                        }
                    } catch (error) {
                        this.searchError = 'Terjadi kesalahan saat mencari lokasi.';
                        console.error(error);
                    }
                }
            }))
        })
    </script>
</div>
