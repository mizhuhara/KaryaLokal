@props([
    'product',
    'size' => 'default', // default|small
    'showRank' => false,
    'rank' => null,
])

@php
    $imageHeight = $size === 'small' ? 'h-40' : 'h-52';
@endphp

<div class="kl-product-card animate-fade-in-up group relative">
    @if ($showRank && $rank)
        <div class="absolute top-3 left-3 z-10 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
             style="background: linear-gradient(135deg, #E8531D, #FF7043); box-shadow: 0 2px 8px rgba(232,83,29,0.4)">
            {{ $rank }}
        </div>
    @endif
    
    <a href="{{ route('product-detail', $product->id) }}" wire:navigate class="block">
        <div class="overflow-hidden {{ $imageHeight }} relative">
            @if ($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                     alt="{{ $product->name }}" class="kl-product-img" />
            @else
                <div class="w-full {{ $imageHeight }} bg-gradient-to-br from-orange-50 to-amber-50 flex items-center justify-center">
                    <x-icon name="cube" class="w-16 h-16 opacity-20" />
                </div>
            @endif
            @if ($product->stock > 0 && $size === 'default')
                <div class="absolute top-3 right-3">
                    <span class="kl-badge kl-badge-green"><x-icon name="check-circle" class="w-3 h-3" /> Ready</span>
                </div>
            @endif
        </div>
        <div class="p-{{ $size === 'small' ? '3' : '4' }}">
            <h3 class="font-semibold text-gray-800 line-clamp-2 font-jakarta {{ $size === 'small' ? 'text-sm mb-0.5' : 'mb-1 text-base' }}">{{ $product->name }}</h3>
            <p class="font-bold {{ $size === 'small' ? 'text-sm mb-1' : 'text-lg mb-3' }}" style="color: #E8531D">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            @if ($size === 'default')
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500 truncate"><x-icon name="shopping-bag" class="w-3.5 h-3.5 inline-block" /> {{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
                    <div class="flex gap-1">
                        @if ($product->is_customizable)
                            <span class="kl-badge kl-badge-purple"><x-icon name="sparkles" class="w-3 h-3" /></span>
                        @endif
                        @if ($product->is_ready_stock)
                            <span class="kl-badge kl-badge-green"><x-icon name="check" class="w-3 h-3" /></span>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 truncate">{{ $product->sellerProfile->shop_name ?? 'Toko' }}</p>
            @endif
        </div>
    </a>
    
    @if ($size === 'default')
        <button class="absolute top-3 left-3 p-2 rounded-full bg-white/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-white hover:scale-110 shadow-md"
                @click="$wire.toggleWishlist({{ $product->id }})">
            <x-icon name="heart" class="w-5 h-5 text-gray-400 hover:text-red-500" />
        </button>
    @endif
</div>
