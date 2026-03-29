@extends('layouts.app', ['title' => $product->name.' | Shop Ops Hub'])

@section('content')
    <section class="hero-panel compact">
        <div>
            <p class="eyebrow">{{ $product->sku }}</p>
            <h1>{{ $product->name }}</h1>
            <p class="hero-copy">{{ $product->selling_points }}</p>
        </div>

        <div class="hero-aside">
            <div class="metric-chip">
                <span>Supplier</span>
                <strong>{{ $product->supplier?->name }}</strong>
            </div>
            <div class="metric-chip">
                <span>Marketplace focus</span>
                <strong>{{ $product->marketplace_focus }}</strong>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <span>Target price</span>
            <strong>${{ number_format($product->target_price, 2) }}</strong>
        </article>
        <article class="stat-card">
            <span>Cost price</span>
            <strong>${{ number_format($product->cost_price, 2) }}</strong>
        </article>
        <article class="stat-card">
            <span>Fulfillment fee</span>
            <strong>${{ number_format($product->fulfillment_fee, 2) }}</strong>
        </article>
        <article class="stat-card">
            <span>Margin rate</span>
            <strong>{{ number_format($product->marginRate(), 1) }}%</strong>
        </article>
        <article class="stat-card">
            <span>Available inventory</span>
            <strong>{{ $product->availableInventory() }}</strong>
        </article>
        <article class="stat-card">
            <span>Safety stock</span>
            <strong>{{ $product->safety_stock }}</strong>
        </article>
    </section>

    <section class="panel two-column">
        <div>
            <div class="panel-head">
                <h2>Listings</h2>
                <p>Channel distribution and performance score.</p>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>External SKU</th>
                            <th>Price</th>
                            <th>Conversion</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->listings as $listing)
                            <tr>
                                <td>{{ $listing->channel->name }}</td>
                                <td>{{ $listing->external_sku }}</td>
                                <td>${{ number_format($listing->price, 2) }}</td>
                                <td>{{ number_format($listing->conversion_rate, 1) }}%</td>
                                <td>{{ number_format($listing->performance_score, 1) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="panel-head">
                <h2>Inventory batches</h2>
                <p>Warehouse visibility for replenishment timing.</p>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>Warehouse</th>
                            <th>Batch</th>
                            <th>On hand</th>
                            <th>Reserved</th>
                            <th>Inbound</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->inventoryBatches as $batch)
                            <tr>
                                <td>{{ $batch->warehouse_code }}</td>
                                <td>{{ $batch->batch_code }}</td>
                                <td>{{ $batch->quantity_on_hand }}</td>
                                <td>{{ $batch->quantity_reserved }}</td>
                                <td>{{ $batch->quantity_inbound }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Recent orders</h2>
            <p>Order-level margin tracking for the SKU.</p>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Revenue</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->orders->sortByDesc('ordered_at') as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No orders have been synced for this SKU yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
