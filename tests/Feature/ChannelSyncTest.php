<?php

namespace Tests\Feature;

use App\Models\Channel;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_sync_endpoint_records_a_completed_run(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $channel = Channel::query()->where('code', 'amazon_us')->firstOrFail();

        $response = $this->postJson("/api/channels/{$channel->id}/sync");

        $response->assertOk();
        $response->assertJsonPath('status', 'completed');
        $this->assertDatabaseHas('sync_runs', [
            'channel_id' => $channel->id,
            'trigger_type' => 'api',
            'status' => 'completed',
        ]);
    }
}
