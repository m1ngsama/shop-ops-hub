<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Services\ChannelSyncService;
use App\Services\DashboardMetricsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ChannelController extends Controller
{
    public function index(DashboardMetricsService $dashboardMetricsService): View
    {
        return view('channels.index', [
            'channels' => Channel::query()
                ->with(['syncRuns' => fn ($query) => $query->latest(), 'listings'])
                ->orderBy('name')
                ->get(),
            'performance' => $dashboardMetricsService->channelPerformance()->keyBy('id'),
        ]);
    }

    public function sync(Channel $channel, ChannelSyncService $channelSyncService): RedirectResponse
    {
        $run = $channelSyncService->sync($channel);

        return redirect()
            ->route('channels.index')
            ->with('status', "{$channel->name} sync completed with {$run->processed_count} updates.");
    }
}
