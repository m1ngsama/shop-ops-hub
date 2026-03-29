<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_sign_in(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->post('/login', [
            'email' => config('shop_ops.admin_email'),
            'password' => config('shop_ops.admin_password'),
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs(User::query()->firstOrFail());
    }
}
