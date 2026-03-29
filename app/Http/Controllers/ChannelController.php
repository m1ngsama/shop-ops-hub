<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Services\ChannelSyncService;
use App\Services\DashboardMetricsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(DashboardMetricsService $dashboardMetricsService): View
    {
        return view('channels.index', [
            'channels' => Channel::query()
                ->with([
                    'syncRuns' => fn ($query) => $query->latest()->with('user'),
                    'listings',
                ])
                ->withCount(['listings', 'orders'])
                ->orderBy('name')
                ->get(),
            'performance' => $dashboardMetricsService->channelPerformance()->keyBy('id'),
        ]);
    }

    public function sync(Request $request, Channel $channel, ChannelSyncService $channelSyncService): RedirectResponse
    {
        $run = $channelSyncService->queue($channel, 'manual', $request->user());

        return redirect()
            ->route('admin.channels.index')
            ->with('status', "{$channel->name} 已加入同步队列，任务编号 #{$run->id}。");
    }
}
