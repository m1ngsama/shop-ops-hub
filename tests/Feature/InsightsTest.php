<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_visualization_page_requires_authentication(): void
    {
        $response = $this->get('/admin/insights');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_view_visualization_page(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $user = User::query()->firstOrFail();

        $response = $this->actingAs($user)->get('/admin/insights');

        $response->assertOk();
        $response->assertSee('经营可视化');
        $response->assertSee('状态分布');
        $response->assertSee('利润拆解');
        $response->assertSee('库存覆盖');
    }
}
