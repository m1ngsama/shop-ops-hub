<?php

namespace App\Services;

use App\Jobs\RunChannelSync;
use App\Models\Channel;
use App\Models\Listing;
use App\Models\Order;
use App\Models\SyncRun;
use App\Models\User;
use App\Services\Channels\AmazonChannelDriver;
use App\Services\Channels\ChannelDriver;
use App\Services\Channels\WalmartChannelDriver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChannelSyncService
{
    public function queue(Channel $channel, string $triggerType = 'manual', ?User $user = null): SyncRun
    {
        $run = SyncRun::query()->create([
            'channel_id' => $channel->id,
            'user_id' => $user?->id,
            'trigger_type' => $triggerType,
            'status' => 'queued',
            'notes' => $user
                ? "Queued by {$user->name}."
                : 'Queued by API token.',
        ]);

        RunChannelSync::dispatch($run->id);
        Cache::forget('dashboard:summary');

        return $run->fresh(['channel', 'user']);
    }

    public function sync(Channel $channel, string $triggerType = 'manual', ?User $user = null): SyncRun
    {
        $run = SyncRun::query()->create([
            'channel_id' => $channel->id,
            'user_id' => $user?->id,
            'trigger_type' => $triggerType,
            'status' => 'running',
        ]);

        return $this->process($run);
    }

    public function processQueuedRun(SyncRun $run): SyncRun
    {
        if ($run->status === 'completed') {
            return $run->fresh(['channel', 'user']);
        }

        $run->update([
            'status' => 'running',
            'notes' => $run->notes ?: 'Connector handshake started.',
        ]);

        return $this->process($run);
    }

    private function process(SyncRun $run): SyncRun
    {
        $channel = $run->channel()->firstOrFail();

        try {
            $payload = $this->resolveDriver($channel)->pull($channel);

            $processedCount = DB::transaction(function () use ($channel, $payload): int {
                $listingCount = 0;
                $orderCount = 0;

                foreach ($payload['listings'] as $listingData) {
                    Listing::query()->updateOrCreate(
                        [
                            'product_id' => $listingData['product_id'],
                            'channel_id' => $channel->id,
                        ],
                        $listingData
                    );

                    $listingCount++;
                }

                foreach ($payload['orders'] as $orderData) {
                    Order::query()->updateOrCreate(
                        ['external_order_no' => $orderData['external_order_no']],
                        $orderData
                    );

                    $orderCount++;
                }

                return $listingCount + $orderCount;
            });

            $run->update([
                'status' => 'completed',
                'processed_count' => $processedCount,
                'notes' => $payload['notes'] ?? null,
                'finished_at' => now(),
            ]);

            Cache::forget('dashboard:summary');

            return $run->fresh(['channel', 'user']);
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'notes' => $exception->getMessage(),
                'finished_at' => now(),
            ]);

            throw $exception;
        }
    }

    private function resolveDriver(Channel $channel): ChannelDriver
    {
        return match ($channel->code) {
            'amazon_us' => app(AmazonChannelDriver::class),
            'walmart_us' => app(WalmartChannelDriver::class),
            default => app(WalmartChannelDriver::class),
        };
    }
}
