<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_displays_the_private_workspace_positioning(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Run catalog, inventory, marketplace sync, and order margin');
        $response->assertSee('Admin sign in');
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
        $response->assertSee('Marketplace operations command');
        $response->assertSee('Low-stock watchlist');
        $response->assertSee('Amazon US');
    }
}
