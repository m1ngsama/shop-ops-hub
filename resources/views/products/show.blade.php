@extends('layouts.app', ['title' => $product->name.' | Shop Ops Hub'])

@section('page_kicker', 'SKU detail')
@section('page_title', $product->name)
@section('page_copy', 'Commercial, supply chain, listing, and order context for '.$product->sku.'.')

@section('content')
    <section class="hero-panel">
        <div>
            <p class="section-kicker">{{ $product->sku }} · {{ $product->category }}</p>
            <h2>{{ $product->name }}</h2>
            <p class="hero-copy">{{ $product->selling_points }}</p>
        </div>

        <div class="hero-sidecar">
            <div class="metric-badge">
                <span>Target price</span>
                <strong>${{ number_format((float) $product->target_price, 2) }}</strong>
            </div>
            <div class="metric-badge">
                <span>Margin rate</span>
                <strong>{{ number_format($product->marginRate(), 1) }}%</strong>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <span>Supplier</span>
            <strong>{{ $product->supplier?->name ?? 'Unassigned' }}</strong>
        </article>
        <article class="stat-card">
            <span>Available inventory</span>
            <strong>{{ $product->availableInventory() }}</strong>
        </article>
        <article class="stat-card">
            <span>Safety stock</span>
            <strong>{{ $product->safety_stock }}</strong>
        </article>
        <article class="stat-card">
            <span>Lead time</span>
            <strong>{{ $product->lead_time_days }} days</strong>
        </article>
        <article class="stat-card">
            <span>Cost price</span>
            <strong>${{ number_format((float) $product->cost_price, 2) }}</strong>
        </article>
        <article class="stat-card">
            <span>Status</span>
            <strong>{{ strtoupper($product->status) }}</strong>
        </article>
    </section>

    <section class="grid-two">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Commercial detail</p>
                    <h2>Go-to-market positioning</h2>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-card">
                    <span>Marketplace focus</span>
                    <strong>{{ $product->marketplace_focus }}</strong>
                </div>
                <div class="detail-card">
                    <span>Fulfillment fee</span>
                    <strong>${{ number_format((float) $product->fulfillment_fee, 2) }}</strong>
                </div>
                <div class="detail-card">
                    <span>Supplier email</span>
                    <strong>{{ $product->supplier?->contact_email ?? 'Not set' }}</strong>
                </div>
                <div class="detail-card">
                    <span>Quality score</span>
                    <strong>{{ $product->supplier?->quality_score ?? 'N/A' }}</strong>
                </div>
            </div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Inventory batches</p>
                    <h2>Warehouse distribution</h2>
                </div>
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
                            <th>ETA</th>
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
                                <td>{{ $batch->inbound_eta?->format('Y-m-d') ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Marketplace listings</p>
                <h2>Active channel footprint</h2>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>Channel</th>
                        <th>External SKU</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Reviews</th>
                        <th>Conversion</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product->listings as $listing)
                        <tr>
                            <td>{{ $listing->channel->name }}</td>
                            <td>{{ $listing->external_sku }}</td>
                            <td><span class="status-pill" data-tone="{{ $listing->status === 'active' ? 'success' : 'warning' }}">{{ strtoupper($listing->status) }}</span></td>
                            <td>${{ number_format((float) $listing->price, 2) }}</td>
                            <td>{{ $listing->review_count }}</td>
                            <td>{{ number_format((float) $listing->conversion_rate, 1) }}%</td>
                            <td>{{ number_format((float) $listing->performance_score, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Order history</p>
                <h2>Recent demand on this SKU</h2>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Quantity</th>
                        <th>Revenue</th>
                        <th>Gross profit</th>
                        <th>Ordered at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->orders->sortByDesc('ordered_at') as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td><span class="status-pill" data-tone="{{ in_array($order->status, ['delivered'], true) ? 'success' : (in_array($order->status, ['processing'], true) ? 'warning' : 'info') }}">{{ strtoupper($order->status) }}</span></td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                            <td>{{ $order->ordered_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No orders have been synced for this SKU yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
