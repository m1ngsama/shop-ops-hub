<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OperationsConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_bootstrap_admin_command_creates_or_normalizes_admin_account(): void
    {
        config()->set('shop_ops.admin_email', 'ops@example.local');
        config()->set('shop_ops.admin_password', 'super-strong-password');

        $this->artisan('ops:bootstrap-admin')
            ->expectsOutputToContain('管理员账号已创建')
            ->assertExitCode(0);

        $user = User::query()->where('email', 'ops@example.local')->firstOrFail();

        $this->assertSame('admin', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('super-strong-password', $user->password));
    }

    public function test_ops_check_command_passes_when_critical_configuration_is_present(): void
    {
        config()->set('app.url', 'https://shop.m1ng.space');
        config()->set('app.key', 'base64:test-key');
        config()->set('app.debug', false);
        config()->set('session.cookie', 'shop_ops_hub_session');
        config()->set('cache.prefix', 'shop_ops_hub_cache_');
        config()->set('cache.default', 'database');
        config()->set('database.default', 'sqlite');
        config()->set('database.redis.options.prefix', 'shop_ops_hub_database_');
        config()->set('queue.default', 'database');
        config()->set('shop_ops.api_token', 'test-token');
        config()->set('shop_ops.admin_email', 'ops@example.local');

        User::factory()->create([
            'email' => 'ops@example.local',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->artisan('ops:check')
            ->assertExitCode(0);
    }
}
