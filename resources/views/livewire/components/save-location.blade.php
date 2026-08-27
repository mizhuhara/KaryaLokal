<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $latitude = null;
    public $longitude = null;
    public $locationName = '';
    public $showForm = false;
    public $saved = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user->latitude && $user->longitude) {
            $this->latitude = $user->latitude;
            $this->longitude = $user->longitude;
            $this->locationName = $user->saved_location_name ?? 'Lokasi Tersimpan';
        }
    }

    public function requestLocation()
    {
        $this->showForm = true;
    }

    public function saveLocation()
    {
        $this->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'locationName' => 'nullable|string|max:100',
        ]);

        Auth::user()->update([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'saved_location_name' => $this->locationName ?: 'Lokasi Saya',
        ]);

        $this->saved = true;
        $this->showForm = false;
        $this->dispatch('notify', message: 'Lokasi berhasil disimpan');
    }

    public function clearLocation()
    {
        Auth::user()->update([
            'latitude' => null,
            'longitude' => null,
            'saved_location_name' => null,
        ]);

        $this->latitude = null;
        $this->longitude = null;
        $this->locationName = '';
        $this->saved = false;
        $this->dispatch('notify', message: 'Lokasi dihapus');
    }
};

?>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold mb-4">📍 Lokasi Saya</h3>

    @if ($latitude && $longitude)
        <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">
            <p class="text-green-800 font-semibold">{{ $locationName }}</p>
            <p class="text-sm text-green-600 mt-1">Lat: {{ number_format($latitude, 5) }} | Lng: {{ number_format($longitude, 5) }}</p>
        </div>

        <div class="flex gap-2">
            <button wire:click="requestLocation" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold">
                Perbarui Lokasi
            </button>
            <button wire:click="clearLocation" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold">
                Hapus
            </button>
        </div>
    @else
        <p class="text-gray-600 text-sm mb-4">Simpan lokasi Anda untuk pencarian lebih cepat</p>
        <button wire:click="requestLocation" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
            📍 Simpan Lokasi Saya
        </button>
    @endif

    @if ($showForm)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <label class="block text-sm font-medium mb-2">Nama Lokasi (Opsional)</label>
            <input type="text" wire:model="locationName" placeholder="Rumah, Kantor, dll" class="w-full px-3 py-2 border rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-orange-400"/>

            <button
                onclick="navigator.geolocation.getCurrentPosition(
                    pos => {
                        @this.latitude = pos.coords.latitude;
                        @this.longitude = pos.coords.longitude;
                    },
                    () => alert('Tidak bisa mendapatkan lokasi')
                )"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold mb-2"
            >
                Dapatkan Lokasi Saat Ini
            </button>

            @if ($latitude && $longitude)
                <div class="text-sm text-gray-600 mb-2 p-2 bg-white rounded border">
                    Lat: {{ number_format($latitude, 5) }}<br/>
                    Lng: {{ number_format($longitude, 5) }}
                </div>
                <button wire:click="saveLocation" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    ✓ Simpan Lokasi
                </button>
            @endif
        </div>
    @endif
</div>
