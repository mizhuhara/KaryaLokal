<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;
    public function test_seller_can_create_product()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $seller = SellerProfile::create([
            'user_id' => $user->id,
            'shop_name' => 'Test Shop',
            'address' => 'Test Address',
            'province' => 'Test Province',
            'city' => 'Test City',
            'district' => 'Test District',
            'latitude' => -6.2749,
            'longitude' => 106.8000,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('seller.products'), [
            'name' => 'Bucket Bunga',
            'description' => 'Bucket bunga segar',
            'price' => 150000,
            'stock' => 10,
            'is_customizable' => true,
            'is_ready_stock' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'seller_profile_id' => $seller->id,
            'name' => 'Bucket Bunga',
            'price' => 150000,
        ]);
    }

    public function test_seller_can_view_products()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $seller = SellerProfile::create([
            'user_id' => $user->id,
            'shop_name' => 'Test Shop',
            'address' => 'Test Address',
            'province' => 'Test Province',
            'city' => 'Test City',
            'district' => 'Test District',
            'latitude' => -6.2749,
            'longitude' => 106.8000,
        ]);

        Product::create([
            'seller_profile_id' => $seller->id,
            'name' => 'Produk Test',
            'description' => 'Deskripsi',
            'price' => 100000,
            'stock' => 5,
        ]);

        $this->actingAs($user)
            ->get(route('seller.products'))
            ->assertStatus(200)
            ->assertSee('Produk Test');
    }

    public function test_seller_cannot_edit_other_seller_product()
    {
        $seller1 = User::factory()->create(['role' => 'seller']);
        $seller2 = User::factory()->create(['role' => 'seller']);

        $profile1 = SellerProfile::create([
            'user_id' => $seller1->id,
            'shop_name' => 'Shop 1',
            'address' => 'Test',
            'province' => 'Test',
            'city' => 'Test',
            'district' => 'Test',
            'latitude' => -6.2749,
            'longitude' => 106.8000,
        ]);

        $profile2 = SellerProfile::create([
            'user_id' => $seller2->id,
            'shop_name' => 'Shop 2',
            'address' => 'Test',
            'province' => 'Test',
            'city' => 'Test',
            'district' => 'Test',
            'latitude' => -6.2749,
            'longitude' => 106.8000,
        ]);

        $product = Product::create([
            'seller_profile_id' => $profile1->id,
            'name' => 'Produk 1',
            'description' => 'Test',
            'price' => 100000,
            'stock' => 5,
        ]);

        $this->actingAs($seller2)
            ->get(route('seller.product-images', $product->id))
            ->assertStatus(403);
    }
}
