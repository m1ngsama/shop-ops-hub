<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_page_requires_authentication(): void
    {
        $response = $this->get('/admin/audit');

        $response->assertRedirect('/login');
    }

    public function test_operator_can_view_audit_page(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $user = User::query()->where('role', 'operator')->firstOrFail();

        $response = $this->actingAs($user)->get('/admin/audit');

        $response->assertOk();
        $response->assertSee('操作审计');
        $response->assertSee('sync.queued');
    }

    public function test_analyst_cannot_view_audit_page(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $user = User::query()->where('role', 'analyst')->firstOrFail();

        $response = $this->actingAs($user)->get('/admin/audit');

        $response->assertForbidden();
    }
}
