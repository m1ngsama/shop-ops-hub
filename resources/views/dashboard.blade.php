@extends('layouts.app', ['title' => 'Overview | Shop Ops Hub'])

@section('page_kicker', 'Admin overview')
@section('page_title', 'Marketplace operations command')
@section('page_copy', 'A private workspace for trading health, inventory pressure, connector queue, and margin-aware order flow.')

@section('content')
    @php
        $maxRevenue = max((float) $revenueTrend->max('revenue'), 1);
        $statusTone = [
            'completed' => 'success',
            'queued' => 'warning',
            'running' => 'info',
            'failed' => 'danger',
            'idle' => 'neutral',
            'processing' => 'warning',
            'shipped' => 'info',
            'delivered' => 'success',
        ];
    @endphp

    <section class="hero-panel">
        <div>
            <p class="section-kicker">Control room</p>
            <h2>Keep channel demand, fulfillment pressure, and sync reliability in one frame.</h2>
            <p class="hero-copy">
                This dashboard is now private by default, queue-aware, and structured like an internal commerce system instead of a public demo page.
            </p>
        </div>

        <div class="hero-sidecar">
            <div class="metric-badge">
                <span>Queue backlog</span>
                <strong>{{ $summary['queued_sync_runs'] }}</strong>
            </div>
            <div class="metric-badge">
                <span>Last sync</span>
                <strong>{{ $summary['latest_sync_at'] ?? 'Pending' }}</strong>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <span>Active products</span>
            <strong>{{ $summary['active_products'] }}</strong>
        </article>
        <article class="stat-card">
            <span>Active listings</span>
            <strong>{{ $summary['active_listings'] }}</strong>
        </article>
        <article class="stat-card">
            <span>Low-stock alerts</span>
            <strong>{{ $summary['low_stock_count'] }}</strong>
        </article>
        <article class="stat-card">
            <span>Last 7 days revenue</span>
            <strong>${{ number_format($summary['weekly_revenue'], 2) }}</strong>
        </article>
        <article class="stat-card">
            <span>Gross margin rate</span>
            <strong>{{ number_format($summary['gross_margin_rate'], 1) }}%</strong>
        </article>
        <article class="stat-card">
            <span>Healthy channels</span>
            <strong>{{ $summary['healthy_channels'] }}</strong>
        </article>
    </section>

    <section class="grid-two">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Revenue pulse</p>
                    <h2>Seven-day trading trend</h2>
                </div>
                <p class="section-copy">Daily gross revenue across synced marketplace orders.</p>
            </div>

            <div class="chart-shell">
                @foreach ($revenueTrend as $point)
                    <div class="chart-bar">
                        <div class="chart-bar-fill" style="height: {{ max(($point['revenue'] / $maxRevenue) * 100, 8) }}%"></div>
                        <strong>${{ number_format($point['revenue'], 0) }}</strong>
                        <span>{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Execution mix</p>
                    <h2>Order status balance</h2>
                </div>
                <p class="section-copy">Current load across order states.</p>
            </div>

            <div class="stack-list">
                @foreach ($orderStatusBreakdown as $row)
                    <article class="list-row">
                        <div>
                            <strong>{{ ucfirst($row->status) }}</strong>
                            <span>{{ $row->total }} orders</span>
                        </div>
                        <span class="status-pill" data-tone="{{ $statusTone[$row->status] ?? 'neutral' }}">{{ strtoupper($row->status) }}</span>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="grid-two">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Channel health</p>
                    <h2>Connector freshness and trading output</h2>
                </div>
                <p class="section-copy">Signals from the latest run, order volume, and revenue per channel.</p>
            </div>

            <div class="stack-list">
                @foreach ($channelHealth as $health)
                    <article class="channel-health-card">
                        <div class="channel-health-head">
                            <div>
                                <strong>{{ $health['channel']->name }}</strong>
                                <span>{{ $health['channel']->marketplace }} · {{ $health['channel']->region }}</span>
                            </div>
                            <span class="status-pill" data-tone="{{ $health['is_stale'] ? 'warning' : ($statusTone[$health['status']] ?? 'neutral') }}">
                                {{ $health['is_stale'] ? 'STALE' : strtoupper($health['status']) }}
                            </span>
                        </div>

                        <div class="micro-stats">
                            <div>
                                <span>Revenue</span>
                                <strong>${{ number_format($health['revenue'], 2) }}</strong>
                            </div>
                            <div>
                                <span>Orders</span>
                                <strong>{{ $health['orders'] }}</strong>
                            </div>
                            <div>
                                <span>Listings</span>
                                <strong>{{ $health['listing_count'] }}</strong>
                            </div>
                            <div>
                                <span>Latest sync</span>
                                <strong>{{ optional($health['latest_sync'])->diffForHumans() ?? 'No run yet' }}</strong>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Category mix</p>
                    <h2>Revenue concentration by category</h2>
                </div>
                <p class="section-copy">Highlights where the seeded assortment is actually monetizing.</p>
            </div>

            <div class="stack-list">
                @foreach ($categoryPerformance as $category)
                    <article class="list-row">
                        <div>
                            <strong>{{ $category->category }}</strong>
                            <span>{{ $category->sku_count }} SKUs</span>
                        </div>
                        <div class="list-row-meta">
                            <strong>${{ number_format((float) $category->revenue, 2) }}</strong>
                            <span>Revenue</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="grid-two">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Inventory risk</p>
                    <h2>Low-stock watchlist</h2>
                </div>
                <p class="section-copy">Available plus inbound inventory compared against safety stock.</p>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Available</th>
                            <th>Safety stock</th>
                            <th>Gap</th>
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
                                <td colspan="5">No replenishment risk detected.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Top movers</p>
                    <h2>Products driving the mix</h2>
                </div>
                <p class="section-copy">Revenue leaders across marketplace activity.</p>
            </div>

            <div class="stack-list">
                @foreach ($topProducts as $product)
                    <a class="list-row" href="{{ route('admin.products.show', $product) }}">
                        <div>
                            <strong>{{ $product->name }}</strong>
                            <span>{{ $product->sku }} · {{ $product->category }}</span>
                        </div>
                        <div class="list-row-meta">
                            <strong>${{ number_format((float) $product->revenue, 2) }}</strong>
                            <span>{{ $product->units_sold }} units</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </article>
    </section>

    <section class="grid-two">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Recent orders</p>
                    <h2>Margin-aware order feed</h2>
                </div>
                <p class="section-copy">Latest marketplace demand with gross profit context.</p>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Channel</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Revenue</th>
                            <th>Gross profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td>{{ $order->external_order_no }}</td>
                                <td>{{ $order->channel->name }}</td>
                                <td>{{ $order->product->name }}</td>
                                <td><span class="status-pill" data-tone="{{ $statusTone[$order->status] ?? 'neutral' }}">{{ strtoupper($order->status) }}</span></td>
                                <td>${{ number_format($order->revenue(), 2) }}</td>
                                <td>${{ number_format($order->grossProfit(), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Connector activity</p>
                    <h2>Latest sync runs</h2>
                </div>
                <p class="section-copy">Queued and completed connector jobs by source.</p>
            </div>

            <div class="stack-list">
                @foreach ($recentSyncRuns as $run)
                    <article class="list-row">
                        <div>
                            <strong>{{ $run->channel->name }}</strong>
                            <span>
                                {{ strtoupper($run->trigger_type) }}
                                @if ($run->user)
                                    · {{ $run->user->name }}
                                @endif
                                · {{ $run->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <div class="list-row-meta">
                            <span class="status-pill" data-tone="{{ $statusTone[$run->status] ?? 'neutral' }}">{{ strtoupper($run->status) }}</span>
                            <span>{{ $run->processed_count }} updates</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>
@endsection
