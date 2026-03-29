<?php

namespace App\Jobs;

use App\Models\SyncRun;
use App\Services\ChannelSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunChannelSync implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 120;

    public function __construct(public int $syncRunId)
    {
    }

    public function handle(ChannelSyncService $channelSyncService): void
    {
        $run = SyncRun::query()->find($this->syncRunId);

        if (! $run) {
            return;
        }

        $channelSyncService->processQueuedRun($run);
    }
}
