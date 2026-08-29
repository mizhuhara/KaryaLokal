<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        SellerProfile::query()->create([
            'user_id' => $user->id,
            'shop_name' => 'Toko Test',
            'slug' => 'toko-test',
            'description' => 'Toko handmade lokal',
            'is_verified' => true,
            'pickup_available' => true,
            'delivery_available' => true,
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
