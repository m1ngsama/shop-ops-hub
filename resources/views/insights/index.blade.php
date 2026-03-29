@extends('layouts.app', ['title' => '经营可视化 | 商运后台'])

@section('page_kicker', '数据驾驶舱')
@section('page_title', '经营可视化')
@section('page_copy', '把营收、毛利、订单结构、渠道贡献、低库存和任务执行时间线放到同一套可视化里。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('storefront.home') }}" target="_blank" rel="noreferrer">前台预览</a>
    <a class="secondary-button" href="{{ route('admin.dashboard') }}">返回总览</a>
@endsection

@section('content')
    @php
        $statusMap = ['processing' => '处理中', 'shipped' => '已发货', 'delivered' => '已签收'];
        $statusColors = ['processing' => '#f59e0b', 'shipped' => '#2563eb', 'delivered' => '#16a34a'];
        $syncStatusMap = ['queued' => '排队中', 'running' => '执行中', 'completed' => '已完成', 'failed' => '失败'];
        $syncToneMap = ['queued' => 'warning', 'running' => 'info', 'completed' => 'success', 'failed' => 'danger'];
        $triggerMap = ['manual' => '手动', 'api' => '接口', 'scheduler' => '调度'];
        $coverageMap = ['healthy' => '安全', 'warning' => '注意', 'danger' => '风险'];
        $coverageToneMap = ['healthy' => 'success', 'warning' => 'warning', 'danger' => 'danger'];

        $financialMax = max((float) $financialTrend->max('revenue'), (float) $financialTrend->max('profit'), 1);
        $pointDivisor = max($financialTrend->count() - 1, 1);
        $seriesPoints = function (string $key) use ($financialTrend, $financialMax, $pointDivisor): string {
            return $financialTrend->values()->map(function (array $point, int $index) use ($key, $financialMax, $pointDivisor): string {
                $x = 6 + (($index / $pointDivisor) * 88);
                $y = 92 - (($point[$key] / $financialMax) * 78);

                return round($x, 2).','.round($y, 2);
            })->implode(' ');
        };

        $revenuePoints = $seriesPoints('revenue');
        $profitPoints = $seriesPoints('profit');

        $totalOrders = max((int) $orderStatusBreakdown->sum('total'), 1);
        $offset = 0.0;
        $statusSlices = $orderStatusBreakdown->map(function ($item) use (&$offset, $totalOrders, $statusColors): array {
            $share = ((int) $item->total / $totalOrders) * 100;
            $start = $offset;
            $offset += $share;

            return [
                'status' => $item->status,
                'total' => (int) $item->total,
                'share' => round($share, 1),
                'start' => $start,
                'end' => $offset,
                'color' => $statusColors[$item->status] ?? '#64748b',
            ];
        });
        $donutGradient = $statusSlices
            ->map(fn (array $slice): string => "{$slice['color']} {$slice['start']}% {$slice['end']}%")
            ->implode(', ');

        $channelRevenueTotal = max((float) $channelPerformance->sum('revenue'), 1);
        $breakdownBase = max((float) $profitBreakdown['revenue'], 1);
        $breakdownSegments = collect([
            ['label' => '货品成本', 'key' => 'product_cost', 'tone' => 'brand'],
            ['label' => '广告花费', 'key' => 'ad_spend', 'tone' => 'warning'],
            ['label' => '渠道费用', 'key' => 'channel_fee', 'tone' => 'info'],
            ['label' => '毛利', 'key' => 'gross_profit', 'tone' => 'success'],
        ])->map(function (array $segment) use ($profitBreakdown, $breakdownBase): array {
            $value = (float) ($profitBreakdown[$segment['key']] ?? 0);

            return [
                'label' => $segment['label'],
                'tone' => $segment['tone'],
                'value' => round($value, 2),
                'percentage' => round(($value / $breakdownBase) * 100, 1),
                'size_class' => match (true) {
                    (($value / $breakdownBase) * 100) >= 35 => 'composition-segment-xl',
                    (($value / $breakdownBase) * 100) >= 24 => 'composition-segment-lg',
                    (($value / $breakdownBase) * 100) >= 14 => 'composition-segment-md',
                    default => 'composition-segment-sm',
                },
            ];
        });
        $channelProfitBase = max((float) $channelProfitability->max('gross_profit'), 1);

        $statusSlices = $statusSlices->map(function (array $slice): array {
            $slice['tone'] = match ($slice['status']) {
                'processing' => 'warning',
                'shipped' => 'info',
                'delivered' => 'success',
                default => 'neutral',
            };

            return $slice;
        });
    @endphp

    <section class="admin-hero-grid">
        <article class="admin-callout">
            <p class="page-kicker">Business Signals</p>
            <h2>把财务走势、订单结构、渠道质量和执行轨迹组织成一套可读的经营叙事。</h2>
            <p>
                可视化页不只负责“展示数据”，而是帮助运营快速理解：钱从哪里来，利润流向哪里，风险接下来会落在哪。
            </p>
        </article>

        <article class="admin-stat-ribbon">
            <div>
                <span>近 7 日销售额</span>
                <strong>${{ number_format($summary['weekly_revenue'], 2) }}</strong>
            </div>
            <div>
                <span>综合毛利率</span>
                <strong>{{ number_format($summary['gross_margin_rate'], 1) }}%</strong>
            </div>
            <div>
                <span>低库存风险</span>
                <strong>{{ $summary['low_stock_count'] }}</strong>
            </div>
        </article>
    </section>

    <section class="metrics-grid">
        <article class="metric-card">
            <span>近 7 日销售额</span>
            <strong>${{ number_format($summary['weekly_revenue'], 2) }}</strong>
        </article>
        <article class="metric-card">
            <span>综合毛利率</span>
            <strong>{{ number_format($summary['gross_margin_rate'], 1) }}%</strong>
        </article>
        <article class="metric-card">
            <span>平均客单</span>
            <strong>${{ number_format($visualSummary['average_order_value'], 2) }}</strong>
        </article>
        <article class="metric-card">
            <span>已履约占比</span>
            <strong>{{ number_format($visualSummary['fulfilled_ratio'], 1) }}%</strong>
        </article>
        <article class="metric-card">
            <span>低库存风险</span>
            <strong>{{ $summary['low_stock_count'] }}</strong>
        </article>
        <article class="metric-card">
            <span>排队任务</span>
            <strong>{{ $summary['queued_sync_runs'] }}</strong>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">财务走势</p>
                    <h2>近 7 日营收与毛利</h2>
                </div>
            </div>

            <div class="chart-legend">
                <span><i class="legend-dot revenue-dot"></i> 营收</span>
                <span><i class="legend-dot profit-dot"></i> 毛利</span>
            </div>

            <div class="sparkline-shell">
                <svg class="sparkline" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polyline class="sparkline-path revenue-path" points="{{ $revenuePoints }}"></polyline>
                    <polyline class="sparkline-path profit-path" points="{{ $profitPoints }}"></polyline>
                </svg>
            </div>

            <div class="trend-grid">
                @foreach ($financialTrend as $point)
                    <article>
                        <span>{{ $point['label'] }}</span>
                        <strong>${{ number_format($point['revenue'], 0) }}</strong>
                        <p>毛利 ${{ number_format($point['profit'], 0) }}</p>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">订单结构</p>
                    <h2>状态分布</h2>
                </div>
            </div>

            <div class="donut-layout">
                <div class="donut-ring donut-ring-static">
                    <div class="donut-core">
                        <strong>{{ $totalOrders }}</strong>
                        <span>订单总量</span>
                    </div>
                </div>

                <div class="legend-list">
                    @foreach ($statusSlices as $slice)
                        <article class="legend-row">
                            <div class="legend-main">
                                <i class="legend-dot tone-{{ $slice['tone'] }}"></i>
                                <strong>{{ $statusMap[$slice['status']] ?? $slice['status'] }}</strong>
                            </div>
                            <div class="row-meta">
                                <span>{{ $slice['total'] }} 单</span>
                                <span>{{ number_format($slice['share'], 1) }}%</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">利润拆解</p>
                    <h2>收入最终变成了什么</h2>
                </div>
            </div>

            <div class="composition-shell">
                <div class="composition-bar">
                    @foreach ($breakdownSegments as $segment)
                        <span class="composition-segment {{ $segment['size_class'] }} tone-{{ $segment['tone'] }}"></span>
                    @endforeach
                </div>

                <div class="legend-list">
                    @foreach ($breakdownSegments as $segment)
                        <article class="legend-row">
                            <div class="legend-main">
                                <i class="legend-dot tone-{{ $segment['tone'] }}"></i>
                                <strong>{{ $segment['label'] }}</strong>
                            </div>
                            <div class="row-meta">
                                <span>${{ number_format($segment['value'], 2) }}</span>
                                <span>{{ number_format($segment['percentage'], 1) }}%</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">渠道盈利</p>
                    <h2>哪些渠道是真的赚钱</h2>
                </div>
            </div>

            <div class="share-stack">
                @foreach ($channelProfitability as $channel)
                    @php
                        $profitRatio = ($channel->gross_profit / $channelProfitBase) * 100;
                        $profitBarClass = match (true) {
                            $profitRatio >= 85 => 'share-bar-xl',
                            $profitRatio >= 65 => 'share-bar-lg',
                            $profitRatio >= 40 => 'share-bar-md',
                            $profitRatio >= 18 => 'share-bar-sm',
                            default => 'share-bar-xs',
                        };
                    @endphp
                    <article class="share-row">
                        <div class="share-head">
                            <strong>{{ $channel->name }}</strong>
                            <span>{{ number_format((float) $channel->margin_rate, 1) }}%</span>
                        </div>
                        <div class="share-bar">
                            <span class="{{ $profitBarClass }}"></span>
                        </div>
                        <p class="table-subtext">
                            销售额 ${{ number_format((float) $channel->revenue, 2) }}
                            · 毛利 ${{ number_format((float) $channel->gross_profit, 2) }}
                            · {{ $channel->order_count }} 单
                        </p>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">刊登矩阵</p>
                    <h2>转化与表现分布</h2>
                </div>
            </div>

            <div class="matrix-board">
                @foreach ($listingPerformance as $listing)
                    @php
                        $xBucket = match (true) {
                            (float) $listing->conversion_rate >= 7.5 => 'x4',
                            (float) $listing->conversion_rate >= 5.5 => 'x3',
                            (float) $listing->conversion_rate >= 3.5 => 'x2',
                            default => 'x1',
                        };
                        $yBucket = match (true) {
                            (float) $listing->performance_score >= 85 => 'y1',
                            (float) $listing->performance_score >= 70 => 'y2',
                            (float) $listing->performance_score >= 55 => 'y3',
                            default => 'y4',
                        };
                    @endphp
                    <span class="matrix-dot {{ $xBucket }} {{ $yBucket }}" title="{{ $listing->product->name }} · {{ $listing->channel->name }}"></span>
                @endforeach

                <span class="matrix-axis matrix-axis-x">转化率</span>
                <span class="matrix-axis matrix-axis-y">表现分</span>
            </div>

            <div class="legend-list">
                @foreach ($listingPerformance as $listing)
                    <article class="legend-row">
                        <div class="legend-main">
                            <strong>{{ $listing->product->name }}</strong>
                            <span class="table-subtext">{{ $listing->channel->name }}</span>
                        </div>
                        <div class="row-meta">
                            <span>{{ number_format((float) $listing->conversion_rate, 1) }}%</span>
                            <span>{{ number_format((float) $listing->performance_score, 1) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">库存覆盖</p>
                    <h2>库存还能撑多久</h2>
                </div>
            </div>

            <div class="legend-list">
                @foreach ($inventoryCoverage as $item)
                    <article class="legend-row">
                        <div class="legend-main">
                            <strong>{{ $item['product']->name }}</strong>
                            <span class="table-subtext">{{ $item['product']->sku }} · 日均消耗估值 {{ number_format($item['estimated_daily_burn'], 1) }}</span>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-{{ $coverageToneMap[$item['risk']] ?? 'neutral' }}">{{ $coverageMap[$item['risk']] ?? $item['risk'] }}</span>
                            <span>{{ number_format($item['cover_days'], 1) }} 天</span>
                        </div>
                        <div class="coverage-meta">
                            <span>交期 {{ $item['product']->lead_time_days }} 天</span>
                            <span>缺口/冗余 {{ $item['coverage_gap'] >= 0 ? '+' : '' }}{{ number_format($item['coverage_gap'], 1) }} 天</span>
                            <span>在途 {{ $item['inbound_days'] !== null ? $item['inbound_days'].' 天后到仓' : '暂无' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">执行时间线</p>
                    <h2>最近同步任务</h2>
                </div>
            </div>

            <div class="timeline-list">
                @foreach ($recentSyncRuns as $run)
                    <article class="timeline-item">
                        <span class="timeline-marker tone-{{ $syncToneMap[$run->status] ?? 'neutral' }}"></span>
                        <div class="timeline-copy">
                            <strong>{{ $run->channel->name }}</strong>
                            <p>{{ $triggerMap[$run->trigger_type] ?? strtoupper($run->trigger_type) }} · {{ $run->created_at?->format('m-d H:i') }} @if($run->user) · {{ $run->user->name }} @endif</p>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-{{ $syncToneMap[$run->status] ?? 'neutral' }}">{{ $syncStatusMap[$run->status] ?? $run->status }}</span>
                            <span>{{ $run->processed_count }} 条</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">重点商品</p>
                    <h2>营收贡献商品</h2>
                </div>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>商品</th>
                            <th>类目</th>
                            <th>销售额</th>
                            <th>销量</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $product)
                            <tr>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category }}</td>
                                <td>${{ number_format((float) $product->revenue, 2) }}</td>
                                <td>{{ $product->units_sold }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">上架准备度</p>
                    <h2>哪些商品适合继续扩量</h2>
                </div>
            </div>

            <div class="legend-list">
                @foreach ($assortmentReadiness as $item)
                    <article class="legend-row">
                        <div class="legend-main">
                            <strong>{{ $item['product']->name }}</strong>
                            <span class="table-subtext">{{ $item['product']->sku }} · 评价 {{ $item['review_count'] }} · 毛利 {{ number_format($item['margin_rate'], 1) }}%</span>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-success">准备度 {{ number_format($item['readiness_score'], 1) }}</span>
                            <span>可售 {{ $item['inventory'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="supplier-summary-grid">
                @foreach ($supplierPulse as $supplier)
                    <article class="summary-card">
                        <span>{{ $supplier->name }}</span>
                        <strong>质量 {{ $supplier->quality_score }}</strong>
                        <p>{{ $supplier->products_count }} 个 SKU · 交期 {{ $supplier->lead_time_days }} 天</p>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel insight-panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">审计轨迹</p>
                    <h2>最近关键动作</h2>
                </div>
            </div>

            <div class="timeline-list">
                @foreach ($recentAuditLogs as $log)
                    <article class="timeline-item">
                        <span class="timeline-marker tone-brand"></span>
                        <div class="timeline-copy">
                            <strong>{{ $log->event }}</strong>
                            <p>{{ $log->created_at?->format('m-d H:i') }} · {{ $log->user?->name ?? $log->actor_label ?? 'system' }}</p>
                        </div>
                        <div class="row-meta">
                            <span class="status-chip tone-neutral">{{ $log->subject_type ?? '系统' }}</span>
                            <span>{{ $log->subject_id ? '#'.$log->subject_id : '无对象' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>
@endsection
