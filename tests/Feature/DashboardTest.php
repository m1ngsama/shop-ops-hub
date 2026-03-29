<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_storefront_home(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('选品前台');
        $response->assertSee('浏览商品目录');
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $user = User::query()->firstOrFail();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('运营总览');
        $response->assertSee('低库存列表');
        $response->assertSee('平台一-北美');
    }
}
