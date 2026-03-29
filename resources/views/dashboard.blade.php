@extends('layouts.app', ['title' => 'Dashboard | Shop Ops Hub'])

@section('content')
    <section class="hero-panel">
        <div>
            <p class="eyebrow">Cross-border commerce operations cockpit</p>
            <h1>Operate beauty, home, apparel, accessories, and jewelry catalogs across Amazon, Walmart, and social commerce.</h1>
            <p class="hero-copy">
                The product is modeled after a fast-moving B2C export team: supplier-linked SKUs, marketplace listings,
                replenishment alerts, margin-aware orders, and one-click channel syncs through a Laravel service layer.
            </p>
        </div>

        <div class="hero-aside">
            <div class="metric-chip">
                <span>Latest sync</span>
                <strong>{{ $summary['latest_sync_at'] ?? 'Not synced yet' }}</strong>
            </div>
            <div class="api-card">
                <p>Public API</p>
                <code>POST /api/channels/{channel}/sync</code>
                <code>GET /api/dashboard/metrics</code>
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
            <span>Recent orders</span>
            <strong>{{ $summary['recent_orders'] }}</strong>
        </article>
    </section>

    <section class="panel two-column">
        <div>
            <div class="panel-head">
                <h2>Low-stock watchlist</h2>
                <p>Safety stock is evaluated from available plus inbound inventory.</p>
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
                                <td>{{ $alert['product']->sku }}</td>
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
        </div>

        <div>
            <div class="panel-head">
                <h2>Channel performance</h2>
                <p>Revenue is aggregated from synced order flow.</p>
            </div>

            <div class="channel-stack">
                @foreach ($channels as $channel)
                    <article class="channel-card">
                        <div>
                            <p>{{ $channel->name }}</p>
                            <span>{{ $channel->marketplace }} · {{ $channel->region }}</span>
                        </div>
                        <div>
                            <strong>${{ number_format($channel->revenue, 2) }}</strong>
                            <span>{{ $channel->order_count }} orders</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel two-column">
        <div>
            <div class="panel-head">
                <h2>Top products</h2>
                <p>Sales leaders across seeded marketplace activity.</p>
            </div>

            <div class="list-stack">
                @foreach ($topProducts as $product)
                    <a class="list-row" href="{{ route('products.show', $product->id) }}">
                        <div>
                            <strong>{{ $product->name }}</strong>
                            <span>{{ $product->sku }} · {{ $product->category }}</span>
                        </div>
                        <div>
                            <strong>${{ number_format($product->revenue, 2) }}</strong>
                            <span>{{ $product->units_sold }} units</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <div class="panel-head">
                <h2>Recent sync runs</h2>
                <p>Manual and API-triggered connector jobs.</p>
            </div>

            <div class="list-stack">
                @foreach ($recentSyncRuns as $run)
                    <article class="list-row">
                        <div>
                            <strong>{{ $run->channel->name }}</strong>
                            <span>{{ strtoupper($run->trigger_type) }} · {{ $run->created_at?->diffForHumans() }}</span>
                        </div>
                        <div>
                            <strong>{{ ucfirst($run->status) }}</strong>
                            <span>{{ $run->processed_count }} updates</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Recent orders</h2>
            <p>Revenue, ad spend, and channel fees are stored for margin-aware operations.</p>
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
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
