<?php

namespace Tests\Feature;

use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_the_operations_cockpit(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Cross-border commerce operations cockpit');
        $response->assertSee('Amazon US');
        $response->assertSee('Velvet Repair Hair Mask');
    }
}
