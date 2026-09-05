<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Volt\Volt;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_product()
    {
        $user = User::factory()->seller()->create();
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

        Livewire::test('pages.seller.products')
            ->set('name', 'Bucket Bunga')
            ->set('description', 'Bucket bunga segar')
            ->set('price', 150000)
            ->set('stock', 10)
            ->set('is_customizable', true)
            ->set('is_ready_stock', true)
            ->call('save');

        $this->assertDatabaseHas('products', [
            'seller_profile_id' => $seller->id,
            'name' => 'Bucket Bunga',
            'price' => 150000,
        ]);
    }

    public function test_seller_can_view_products()
    {
        $user = User::factory()->seller()->create();
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
        $seller1 = User::factory()->seller()->create();
        $seller2 = User::factory()->seller()->create();

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
