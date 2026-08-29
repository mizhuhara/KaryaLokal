<?php

use Livewire\Volt\Component;
use App\Models\Subscription;

new class extends Component {
    public $currentPlan = 'basic';

    public function mount()
    {
        $seller = auth()->user()->sellerProfile;
        $sub = $seller->activeSubscription();
        if ($sub) {
            $this->currentPlan = $sub->plan;
        }
    }

    public function subscribe($plan)
    {
        $seller = auth()->user()->sellerProfile;
        $config = Subscription::PLANS[$plan];

        $existing = $seller->subscription()->first();

        if ($existing && $existing->status === 'active') {
            $existing->update(['status' => 'cancelled']);
        }

        Subscription::create([
            'seller_profile_id' => $seller->id,
            'plan' => $plan,
            'price' => $config['price'],
            'product_limit' => $config['product_limit'],
            'priority_listing' => $config['priority_listing'],
            'analytics_access' => $config['analytics_access'],
            'verified_badge' => $config['verified_badge'],
            'starts_at' => now(),
            'ends_at' => $plan === 'basic' ? null : now()->addMonth(),
            'status' => 'active',
        ]);

        if ($config['verified_badge'] && !$seller->is_verified) {
            $seller->update(['is_verified' => true, 'verified_at' => now()]);
        }

        $this->currentPlan = $plan;
        $this->dispatch('notify', message: 'Berhasil upgrade ke ' . $config['name'] . '!');
    }

    public function cancelSubscription()
    {
        $seller = auth()->user()->sellerProfile;
        $sub = $seller->activeSubscription();
        if ($sub) {
            $sub->update(['status' => 'cancelled']);
            $this->currentPlan = 'basic';
            $this->dispatch('notify', message: 'Subscription dibatalkan');
        }
    }

    public function with()
    {
        $seller = auth()->user()->sellerProfile;
        $activeSub = $seller->activeSubscription();
        return [
            'plans' => Subscription::PLANS,
            'activeSubscription' => $activeSub,
            'currentProductCount' => $seller->products()->count(),
        ];
    }
};

?>

    <div>
<x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">Subscription Toko</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Current Plan -->
            @if ($activeSubscription)
                <div class="bg-gradient-to-r from-orange-400 to-red-400 text-white rounded-lg p-6 mb-8">
                    <h3 class="text-2xl font-bold mb-2">Paket Aktif: {{ ucfirst($activeSubscription->plan) }}</h3>
                    <p>Berlaku hingga {{ $activeSubscription->ends_at ? $activeSubscription->ends_at->format('d M Y') : 'Selamanya' }}</p>
                </div>
            @endif

            <!-- Plans -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($plans as $key => $plan)
                    <div class="bg-white rounded-lg shadow {{ $currentPlan === $key ? 'ring-2 ring-orange-500' : '' }} overflow-hidden">
                        <div class="p-6 text-center {{ $key === 'pro' ? 'bg-gradient-to-r from-orange-400 to-red-400 text-white' : '' }}">
                            <h3 class="text-2xl font-bold">{{ $plan['name'] }}</h3>
                            <p class="text-4xl font-bold mt-2">Rp {{ number_format($plan['price'], 0, ',', '.') }}</p>
                            <p class="text-sm {{ $key === 'pro' ? 'text-white' : 'text-gray-500' }}">/bulan</p>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                <span>{{ $plan['product_limit'] === -1 ? 'Unlimited' : $plan['product_limit'] }} produk</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="{{ $plan['priority_listing'] ? 'text-green-500' : 'text-gray-300' }}">{{ $plan['priority_listing'] ? '✓' : '✕' }}</span>
                                <span>Prioritas listing</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="{{ $plan['analytics_access'] ? 'text-green-500' : 'text-gray-300' }}">{{ $plan['analytics_access'] ? '✓' : '✕' }}</span>
                                <span>Analytics</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="{{ $plan['verified_badge'] ? 'text-green-500' : 'text-gray-300' }}">{{ $plan['verified_badge'] ? '✓' : '✕' }}</span>
                                <span>Badge terverifikasi</span>
                            </div>

                            <div class="pt-4">
                                @if ($currentPlan === $key)
                                    <div class="text-center font-semibold text-orange-600">Paket Aktif</div>
                                @elseif ($key === 'basic')
                                    <button wire:click="subscribe('basic')" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg hover:bg-gray-50">Aktifkan</button>
                                @else
                                    <button wire:click="subscribe('{{ $key }}')" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">Upgrade</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Product Count -->
            <div class="bg-white rounded-lg shadow p-6 mt-8">
                <h3 class="font-semibold mb-4">Penggunaan Produk</h3>
                <p>Produk saat ini: <strong>{{ $currentProductCount }}</strong></p>
                <p>Batas paket: <strong>{{ auth()->user()->sellerProfile->getProductLimit() === -1 ? 'Unlimited' : auth()->user()->sellerProfile->getProductLimit() }}</strong></p>
                @if ($currentProductCount > 0 && auth()->user()->sellerProfile->getProductLimit() > 0)
                    @php $percent = ($currentProductCount / auth()->user()->sellerProfile->getProductLimit()) * 100; @endphp
                    <div class="w-full h-3 bg-gray-200 rounded-full mt-3">
                        <div class="h-full bg-orange-500 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
