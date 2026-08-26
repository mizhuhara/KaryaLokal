<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;

class RecommendationService
{
    public static function getSimilarProducts(Product $product, int $limit = 6): \Illuminate\Support\Collection
    {
        return Product::where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where('is_active', true)
            ->with('primaryImage', 'sellerProfile')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public static function getTrendingProducts(int $limit = 8): \Illuminate\Support\Collection
    {
        $recentOrders = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(30))
            ->with('items.product')
            ->get();

        $productIds = $recentOrders->flatMap->items->pluck('product_id')
            ->countBy()
            ->sortDesc()
            ->take($limit * 2)
            ->keys()
            ->toArray();

        if (empty($productIds)) {
            return Product::where('is_active', true)
                ->with('primaryImage', 'sellerProfile')
                ->latest()
                ->limit($limit)
                ->get();
        }

        return Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->with('primaryImage', 'sellerProfile')
            ->limit($limit)
            ->get();
    }

    public static function getRecommendedForUser(int $userId, int $limit = 8): \Illuminate\Support\Collection
    {
        $purchasedCategoryIds = Order::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->with('items.product')
            ->get()
            ->flatMap->items
            ->pluck('product_id')
            ->unique();

        $purchasedProductIds = Order::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->with('items')
            ->get()
            ->flatMap->items
            ->pluck('product_id')
            ->unique();

        if ($purchasedCategoryIds->isEmpty()) {
            return Product::where('is_active', true)
                ->with('primaryImage', 'sellerProfile')
                ->latest()
                ->limit($limit)
                ->get();
        }

        $categoryIds = Product::whereIn('id', $purchasedCategoryIds)->pluck('category_id')->unique()->toArray();

        return Product::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $purchasedProductIds->toArray())
            ->where('is_active', true)
            ->with('primaryImage', 'sellerProfile')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public static function getFromSameSeller(Product $product, int $limit = 4): \Illuminate\Support\Collection
    {
        return Product::where('seller_profile_id', $product->seller_profile_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('primaryImage')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public static function getPopularInArea(float $lat, float $lng, float $radius = 50, int $limit = 8): \Illuminate\Support\Collection
    {
        $sellerIds = \App\Models\SellerProfile::where('is_verified', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->filter(function ($seller) use ($lat, $lng, $radius) {
                $distance = self::calculateDistance($lat, $lng, $seller->latitude, $seller->longitude);
                return $distance <= $radius;
            })
            ->pluck('id')
            ->toArray();

        if (empty($sellerIds)) {
            return collect([]);
        }

        return Product::whereIn('seller_profile_id', $sellerIds)
            ->where('is_active', true)
            ->with('primaryImage', 'sellerProfile')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private static function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        return $earthRadius * $c;
    }
}