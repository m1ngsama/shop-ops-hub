<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Services\ChannelSyncService;
use Illuminate\Http\JsonResponse;

class ChannelSyncController extends Controller
{
    public function __invoke(Channel $channel, ChannelSyncService $channelSyncService): JsonResponse
    {
        $run = $channelSyncService->sync($channel, 'api');

        return response()->json([
            'channel' => $channel->only(['id', 'code', 'name']),
            'sync_run_id' => $run->id,
            'status' => $run->status,
            'processed_count' => $run->processed_count,
            'finished_at' => optional($run->finished_at)->toIso8601String(),
            'notes' => $run->notes,
        ]);
    }
}
