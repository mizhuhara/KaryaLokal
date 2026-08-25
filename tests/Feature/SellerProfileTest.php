<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class SellerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_register_as_seller(): void
    {
        $user = User::factory()->create(['role' => UserRole::Buyer->value]);

        $this->actingAs($user);

        Volt::test('pages.seller.register')
            ->set('shop_name', 'Toko Karya Indah')
            ->set('description', 'Menjual barang rajutan.')
            ->call('register')
            ->assertRedirect(route('seller.dashboard', absolute: false));

        $this->assertDatabaseHas('seller_profiles', [
            'user_id' => $user->id,
            'shop_name' => 'Toko Karya Indah',
            'description' => 'Menjual barang rajutan.',
        ]);

        $this->assertEquals(UserRole::Seller, $user->refresh()->role);
    }

    public function test_seller_cannot_access_register_page(): void
    {
        $user = User::factory()->seller()->create();

        $this->actingAs($user);

        Volt::test('pages.seller.register')
            ->assertRedirect(route('seller.dashboard', absolute: false));
    }

    public function test_seller_can_update_profile_and_location(): void
    {
        $user = User::factory()->seller()->create();
        $profile = $user->sellerProfile()->create([
            'shop_name' => 'Toko Awal',
            'slug' => 'toko-awal-123',
        ]);

        $this->actingAs($user);

        Volt::test('pages.seller.profile')
            ->set('shop_name', 'Toko Baru')
            ->set('description', 'Deskripsi baru')
            ->set('address', 'Jalan Merdeka No 1')
            ->set('latitude', -6.200000)
            ->set('longitude', 106.816666)
            ->set('pickup_available', true)
            ->call('updateProfile')
            ->assertDispatched('profile-updated');

        $this->assertDatabaseHas('seller_profiles', [
            'id' => $profile->id,
            'shop_name' => 'Toko Baru',
            'description' => 'Deskripsi baru',
            'latitude' => '-6.2000000',
            'longitude' => '106.8166660',
            'pickup_available' => true,
        ]);
    }
}
