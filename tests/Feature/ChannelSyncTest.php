<?php

namespace Tests\Feature;

use App\Jobs\RunChannelSync;
use App\Models\Channel;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ChannelSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_sync_endpoint_requires_token(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $channel = Channel::query()->where('code', 'amazon_us')->firstOrFail();

        $response = $this->postJson("/api/channels/{$channel->id}/sync");

        $response->assertUnauthorized();
    }

    public function test_api_sync_endpoint_queues_a_run_with_valid_token(): void
    {
        config()->set('shop_ops.api_token', 'test-token');
        Queue::fake();

        $this->seed(CommerceOpsSeeder::class);

        $channel = Channel::query()->where('code', 'amazon_us')->firstOrFail();

        $response = $this
            ->withHeader('Authorization', 'Bearer test-token')
            ->postJson("/api/channels/{$channel->id}/sync");

        $response->assertAccepted();
        $response->assertJsonPath('status', 'queued');
        $this->assertDatabaseHas('sync_runs', [
            'channel_id' => $channel->id,
            'trigger_type' => 'api',
            'status' => 'queued',
        ]);
        Queue::assertPushed(RunChannelSync::class);
    }
}
