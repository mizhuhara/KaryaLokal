<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_cannot_access_seller_or_admin_dashboards(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::Buyer]);

        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertForbidden();
        $this->actingAs($buyer)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_seller_can_access_seller_dashboard_but_not_admin(): void
    {
        $seller = User::factory()->seller()->create();

        $this->actingAs($seller)->get(route('seller.dashboard'))->assertOk();
        $this->actingAs($seller)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard_but_not_seller(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('seller.dashboard'))->assertForbidden();
    }

    public function test_login_redirects_seller_to_seller_dashboard(): void
    {
        $seller = User::factory()->seller()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $seller->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertRedirect(route('seller.dashboard', absolute: false));
    }

    public function test_login_redirects_admin_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $admin->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }
}
