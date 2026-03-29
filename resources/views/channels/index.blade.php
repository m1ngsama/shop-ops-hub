@extends('layouts.app', ['title' => '渠道中心 | 商运后台'])

@section('page_kicker', '渠道模块')
@section('page_title', '渠道中心')
@section('page_copy', '以渠道为单位查看营收、订单、刊登、最近同步和排队任务状态。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.orders.index') }}">查看订单</a>
@endsection

@section('content')
    @php
        $syncStatusMap = ['queued' => '排队中', 'running' => '执行中', 'completed' => '已完成', 'failed' => '失败'];
        $triggerMap = ['manual' => '手动', 'api' => '接口', 'scheduler' => '调度'];
        $toneMap = ['queued' => 'warning', 'running' => 'info', 'completed' => 'success', 'failed' => 'danger'];
    @endphp

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">接口边界</p>
                <h2>同步接口默认受保护</h2>
            </div>
        </div>

        <div class="detail-grid">
            <article class="detail-card">
                <span>指标接口</span>
                <strong>GET /api/dashboard/metrics</strong>
            </article>
            <article class="detail-card">
                <span>同步接口</span>
                <strong>POST /api/channels/{channel}/sync</strong>
            </article>
            <article class="detail-card">
                <span>鉴权方式</span>
                <strong>运营权限会话或 Bearer Token</strong>
            </article>
            <article class="detail-card">
                <span>执行方式</span>
                <strong>队列入队，worker 异步消费</strong>
            </article>
        </div>
    </section>

    <section class="channel-grid">
        @foreach ($channels as $channel)
            @php
                $latestRun = $channel->syncRuns->first();
                $channelPerformance = $performance->get($channel->id);
            @endphp

            <article class="channel-card">
                <div class="channel-card-head">
                    <div>
                        <p class="page-kicker">{{ $channel->marketplace }}</p>
                        <h2>{{ $channel->name }}</h2>
                        <p class="page-copy">{{ $channel->region }} · 费率 {{ number_format((float) $channel->fee_percentage, 1) }}% · {{ $channel->currency }}</p>
                    </div>
                    <span class="status-chip tone-{{ $channel->is_active ? 'success' : 'warning' }}">{{ $channel->is_active ? '启用中' : '已停用' }}</span>
                </div>

                <div class="mini-metrics">
                    <article>
                        <span>销售额</span>
                        <strong>${{ number_format((float) ($channelPerformance?->revenue ?? 0), 2) }}</strong>
                    </article>
                    <article>
                        <span>订单</span>
                        <strong>{{ $channel->orders_count }}</strong>
                    </article>
                    <article>
                        <span>刊登</span>
                        <strong>{{ $channel->listings_count }}</strong>
                    </article>
                    <article>
                        <span>最近同步</span>
                        <strong>{{ $latestRun?->created_at?->format('m-d H:i') ?? '暂无' }}</strong>
                    </article>
                </div>

                <div class="card-actions">
                    @if (auth()->user()?->canManageOperations())
                        <form method="post" action="{{ route('admin.channels.sync', $channel) }}">
                            @csrf
                            <button type="submit" class="primary-button">加入同步队列</button>
                        </form>
                    @else
                        <span class="status-chip tone-neutral">当前账号仅可查看</span>
                    @endif
                </div>

                <div class="row-list compact-list">
                    @forelse ($channel->syncRuns->take(3) as $run)
                        <article class="row-card">
                            <div class="row-main">
                                <strong>#{{ $run->id }}</strong>
                                <p>{{ $triggerMap[$run->trigger_type] ?? $run->trigger_type }} · {{ $run->created_at?->format('m-d H:i') }} @if($run->user) · {{ $run->user->name }} @endif</p>
                            </div>
                            <div class="row-meta">
                                <span class="status-chip tone-{{ $toneMap[$run->status] ?? 'neutral' }}">{{ $syncStatusMap[$run->status] ?? $run->status }}</span>
                                <span>{{ $run->processed_count }} 条</span>
                            </div>
                        </article>
                    @empty
                        <p class="table-subtext">暂无同步记录。</p>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
@endsection
