@extends('layouts.app', ['title' => '控制台总览 | 商运后台'])

@section('page_kicker', '控制台首页')
@section('page_title', '控制台总览')
@section('page_copy', '优先查看风险、订单、渠道状态与关键趋势。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.insights') }}">查看可视化</a>
    <a class="secondary-button" href="{{ route('storefront.home') }}" target="_blank" rel="noreferrer">前台预览</a>
    <a class="secondary-button" href="{{ route('admin.channels.index') }}">查看渠道</a>
    @if (auth()->user()?->canManageOperations())
        <a class="secondary-button" href="{{ route('admin.audit.index') }}">操作审计</a>
    @endif
    <a class="primary-button" href="{{ route('admin.orders.index') }}">查看订单</a>
@endsection

@section('content')
    @php
        $maxRevenue = max((float) $revenueTrend->max('revenue'), 1);
        $syncStatusMap = ['queued' => '排队中', 'running' => '执行中', 'completed' => '已完成', 'failed' => '失败'];
        $orderStatusMap = ['processing' => '处理中', 'shipped' => '已发货', 'delivered' => '已签收'];
        $statusTone = ['queued' => 'warning', 'running' => 'info', 'completed' => 'success', 'failed' => 'danger', 'processing' => 'warning', 'shipped' => 'info', 'delivered' => 'success'];
        $triggerMap = ['manual' => '手动', 'api' => '接口', 'scheduler' => '调度'];
        $topRisk = $riskBoard[0] ?? null;
        $barClassMap = $revenueTrend->map(function ($point) use ($maxRevenue) {
            $ratio = ($point['revenue'] / $maxRevenue) * 100;

            return match (true) {
                $ratio >= 85 => 'bar-fill-xl',
                $ratio >= 65 => 'bar-fill-lg',
                $ratio >= 45 => 'bar-fill-md',
                $ratio >= 25 => 'bar-fill-sm',
                default => 'bar-fill-xs',
            };
        });
    @endphp

    <section class="hero-dashboard-panel">
        <div class="hero-dashboard-copy">
            <p class="page-kicker">Operations</p>
            <h2>把风险、成交与状态压缩到一个决策首屏。</h2>
            <p>进入后台后，先看最需要被处理的事。</p>

            <div class="hero-dashboard-actions">
                <a class="primary-button" href="{{ route('admin.orders.index') }}">进入订单执行</a>
                <a class="secondary-button" href="{{ route('admin.products.index') }}">查看商品状态</a>
            </div>
        </div>

        <div class="hero-dashboard-stack">
            @if ($topRisk)
                <article class="hero-signal-card tone-{{ $topRisk['tone'] }}">
                    <span>当前最高优先级</span>
                    <strong>{{ $topRisk['title'] }}</strong>
                    <p>{{ $topRisk['copy'] }}</p>
                    <b>{{ $topRisk['value'] }}</b>
                </article>
            @endif

            <article class="hero-mini-grid">
                <div>
                    <span>近 7 日营收</span>
                    <strong>${{ number_format($summary['weekly_revenue'], 0) }}</strong>
                </div>
                <div>
                    <span>待处理预警</span>
                    <strong>{{ $summary['low_stock_count'] + $summary['queued_sync_runs'] }}</strong>
                </div>
                <div>
                    <span>活跃渠道</span>
                    <strong>{{ $summary['healthy_channels'] }}</strong>
                </div>
                <div>
                    <span>活跃商品</span>
                    <strong>{{ $summary['active_products'] }}</strong>
                </div>
            </article>
        </div>
    </section>

    <section class="metrics-grid">
        <article class="metric-card">
            <span>近 7 日销售额</span>
            <strong>${{ number_format($summary['weekly_revenue'], 2) }}</strong>
        </article>
        <article class="metric-card">
            <span>毛利率</span>
            <strong>{{ number_format($summary['gross_margin_rate'], 1) }}%</strong>
        </article>
        <article class="metric-card">
            <span>低库存预警</span>
            <strong>{{ $summary['low_stock_count'] }}</strong>
        </article>
        <article class="metric-card">
            <span>排队任务</span>
            <strong>{{ $summary['queued_sync_runs'] }}</strong>
        </article>
        <article class="metric-card">
            <span>活跃商品</span>
            <strong>{{ $summary['active_products'] }}</strong>
        </article>
        <article class="metric-card">
            <span>活跃渠道</span>
            <strong>{{ $summary['healthy_channels'] }}</strong>
        </article>
    </section>

    <section class="risk-board-grid">
        @foreach ($riskBoard as $item)
            <article class="risk-board-card tone-{{ $item['tone'] }}">
                <span>{{ $item['title'] }}</span>
                <strong>{{ $item['value'] }}</strong>
                <p>{{ $item['copy'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">成交趋势</p>
                    <h2>近 7 日销售脉冲</h2>
                </div>
            </div>

            <div class="bar-chart bar-chart-hero">
                @foreach ($revenueTrend as $point)
                    <div class="bar-item">
                        <div class="bar-rail">
                            <div class="bar-fill {{ $barClassMap[$loop->index] }}"></div>
                        </div>
                        <strong>${{ number_format($point['revenue'], 0) }}</strong>
                        <span>{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">执行热区</p>
                    <h2>订单与任务状态</h2>
                </div>
            </div>

            <div class="pipeline-grid pipeline-grid-dense">
                @foreach ($orderPipeline as $item)
                    <article class="pipeline-card">
                        <span class="status-chip tone-{{ $item['tone'] }}">{{ $item['label'] }}</span>
                        <strong>{{ $item['count'] }} 单</strong>
                        <p>销售额 ${{ number_format($item['revenue'], 0) }}</p>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">供应脉冲</p>
                    <h2>供应商质量与供给</h2>
                </div>
            </div>

            <div class="row-list">
                @foreach ($supplierPulse as $item)
                    <article class="row-card">
                        <div class="row-main">
                            <strong>{{ $item['supplier']->name }}</strong>
                            <p>{{ $item['product_count'] }} 个 SKU · 平均毛利 {{ number_format($item['average_margin'], 1) }}% · 可售 {{ $item['available_inventory'] }}</p>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-success">质量 {{ $item['supplier']->quality_score }}</span>
                            <span>{{ $item['supplier']->lead_time_days }} 天</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">待处理事项</p>
                    <h2>今天优先处理</h2>
                </div>
            </div>

            <div class="action-list">
                @foreach ($lowStockAlerts->take(3) as $alert)
                    <article class="action-item">
                        <div>
                            <strong>{{ $alert['product']->name }}</strong>
                            <p>{{ $alert['product']->sku }} 当前可售 {{ $alert['available_units'] }}，低于安全库存 {{ $alert['product']->safety_stock }}</p>
                        </div>
                        <a class="text-link" href="{{ route('admin.products.show', $alert['product']) }}">查看</a>
                    </article>
                @endforeach

                @foreach ($channelHealth->where('is_stale', true)->take(2) as $health)
                    <article class="action-item">
                        <div>
                            <strong>{{ $health['channel']->name }}</strong>
                            <p>最近同步时间过旧，建议检查链路状态并重新发起任务。</p>
                        </div>
                        <a class="text-link" href="{{ route('admin.channels.index') }}">处理</a>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">成交机会</p>
                    <h2>品类与渠道关注点</h2>
                </div>
            </div>

            <div class="action-list">
                @foreach ($categoryPerformance->take(3) as $category)
                    <article class="action-item">
                        <div>
                            <strong>{{ $category->category }}</strong>
                            <p>{{ $category->sku_count }} 个 SKU · 销售额 ${{ number_format((float) $category->revenue, 2) }}</p>
                        </div>
                        <span class="status-chip tone-info">观察中</span>
                    </article>
                @endforeach

                @foreach ($channelHealth->take(2) as $health)
                    <article class="action-item">
                        <div>
                            <strong>{{ $health['channel']->name }}</strong>
                            <p>{{ $health['channel']->marketplace }} · 收入 ${{ number_format($health['revenue'], 2) }}</p>
                        </div>
                        <span class="status-chip tone-{{ $health['is_stale'] ? 'warning' : 'success' }}">{{ $health['is_stale'] ? '需关注' : '稳定' }}</span>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">渠道健康</p>
                    <h2>渠道与同步状态</h2>
                </div>
            </div>

            <div class="row-list">
                @foreach ($channelHealth as $health)
                    <article class="row-card">
                        <div class="row-main">
                            <strong>{{ $health['channel']->name }}</strong>
                            <p>{{ $health['channel']->marketplace }} · {{ $health['channel']->region }} · 最近同步 {{ optional($health['latest_sync'])->format('m-d H:i') ?? '暂无' }}</p>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-{{ $health['is_stale'] ? 'warning' : ($statusTone[$health['status']] ?? 'neutral') }}">{{ $health['is_stale'] ? '过期' : ($syncStatusMap[$health['status']] ?? $health['status']) }}</span>
                            <span>${{ number_format($health['revenue'], 2) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">类目表现</p>
                    <h2>收入分布</h2>
                </div>
            </div>

            <div class="row-list">
                @foreach ($categoryPerformance as $category)
                    <article class="row-card">
                        <div class="row-main">
                            <strong>{{ $category->category }}</strong>
                            <p>{{ $category->sku_count }} 个 SKU</p>
                        </div>
                        <div class="row-meta">
                            <span>${{ number_format((float) $category->revenue, 2) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">补货监控</p>
                    <h2>低库存列表</h2>
                </div>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>商品</th>
                            <th>可售</th>
                            <th>安全库存</th>
                            <th>缺口</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockAlerts as $alert)
                            <tr>
                                <td><a href="{{ route('admin.products.show', $alert['product']) }}">{{ $alert['product']->sku }}</a></td>
                                <td>{{ $alert['product']->name }}</td>
                                <td>{{ $alert['available_units'] }}</td>
                                <td>{{ $alert['product']->safety_stock }}</td>
                                <td>{{ $alert['gap'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">当前没有低库存风险。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">最近同步</p>
                    <h2>执行记录</h2>
                </div>
            </div>

            <div class="row-list">
                @foreach ($recentSyncRuns as $run)
                    <article class="row-card">
                        <div class="row-main">
                            <strong>{{ $run->channel->name }}</strong>
                            <p>{{ $triggerMap[$run->trigger_type] ?? strtoupper($run->trigger_type) }} · {{ $run->created_at?->format('m-d H:i') }} @if($run->user) · {{ $run->user->name }} @endif</p>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-{{ $statusTone[$run->status] ?? 'neutral' }}">{{ $syncStatusMap[$run->status] ?? $run->status }}</span>
                            <span>{{ $run->processed_count }} 条</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">最近订单</p>
                <h2>订单执行面板</h2>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>渠道</th>
                        <th>商品</th>
                        <th>状态</th>
                        <th>销售额</th>
                        <th>毛利</th>
                        <th>下单时间</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td>{{ $order->product->name }}</td>
                            <td><span class="status-chip tone-{{ $statusTone[$order->status] ?? 'neutral' }}">{{ $orderStatusMap[$order->status] ?? $order->status }}</span></td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                            <td>{{ $order->ordered_at?->format('m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">公开前台</p>
                    <h2>选品展示优先级</h2>
                </div>
                <a class="text-link" href="{{ route('storefront.catalog') }}" target="_blank" rel="noreferrer">打开前台目录</a>
            </div>

            <div class="row-list">
                @foreach ($topProducts as $product)
                    <article class="row-card">
                        <div class="row-main">
                            <strong>{{ $product->name }}</strong>
                            <p>{{ $product->sku }} · {{ $product->category }}</p>
                        </div>
                        <div class="row-meta">
                            <span>${{ number_format((float) $product->revenue, 2) }}</span>
                            <span>{{ $product->units_sold }} 件</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">可视化入口</p>
                    <h2>驾驶舱看板</h2>
                </div>
                <a class="text-link" href="{{ route('admin.insights') }}">进入看板</a>
            </div>

            <div class="action-list">
                <article class="action-item">
                    <div>
                        <strong>财务走势</strong>
                        <p>把近 7 日营收与毛利放到同一张折线图里，便于发现渠道拉动和利润拐点。</p>
                    </div>
                    <a class="text-link" href="{{ route('admin.insights') }}">查看</a>
                </article>
                <article class="action-item">
                    <div>
                        <strong>订单结构</strong>
                        <p>用状态占比和渠道贡献拆解订单结构，不再只靠表格滚动查看。</p>
                    </div>
                    <a class="text-link" href="{{ route('admin.insights') }}">查看</a>
                </article>
                <article class="action-item">
                    <div>
                        <strong>补货与同步</strong>
                        <p>把低库存和任务执行时间线放到同一屏里，处理优先级更清晰。</p>
                    </div>
                    <a class="text-link" href="{{ route('admin.insights') }}">查看</a>
                </article>
            </div>
        </article>
    </section>
@endsection
