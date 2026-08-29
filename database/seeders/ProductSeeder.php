<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = SellerProfile::query()->first();
        if (! $seller) {
            return;
        }

        $data = [
            ['name' => 'Bucket Bunga Mawar Merah', 'category' => 'Bucket Bunga', 'price' => 150000, 'stock' => 12],
            ['name' => 'Bouquet Wisuda Mini', 'category' => 'Bouquet Wisuda', 'price' => 85000, 'stock' => 20],
            ['name' => 'Bucket Snack Kekinian', 'category' => 'Bucket Snack', 'price' => 120000, 'stock' => 8],
            ['name' => 'Hampers Lebaran 2026', 'category' => 'Hampers', 'price' => 200000, 'stock' => 5],
            ['name' => 'Gift Box Ulang Tahun', 'category' => 'Gift Box', 'price' => 95000, 'stock' => 15],
            ['name' => 'Boneka Crochet Unik', 'category' => 'Crochet / Rajutan', 'price' => 175000, 'stock' => 6],
            ['name' => 'Tas Makrame Handmade', 'category' => 'Macrame', 'price' => 250000, 'stock' => 4],
            ['name' => 'Asbak Resin Custom', 'category' => 'Resin', 'price' => 65000, 'stock' => 18],
        ];

        foreach ($data as $item) {
            $category = Category::query()->where('name', $item['category'])->first();
            if (! $category) {
                continue;
            }

            Product::query()->create([
                'seller_profile_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $item['name'],
                'slug' => str()->slug($item['name']),
                'description' => 'Product handmade ' . $item['name'] . ' dari seller lokal.',
                'price' => $item['price'],
                'stock' => $item['stock'],
                'is_customizable' => true,
                'is_ready_stock' => true,
                'is_active' => true,
            ]);
        }
    }
}