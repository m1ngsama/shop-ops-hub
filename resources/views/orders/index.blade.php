@extends('layouts.app', ['title' => 'Orders | Shop Ops Hub'])

@section('page_kicker', 'Order ledger')
@section('page_title', 'Marketplace orders')
@section('page_copy', 'Track recent order flow, filter by status or channel, and keep gross profit visible.')

@section('content')
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Filter orders</p>
                <h2>Search demand across channels</h2>
            </div>
            <p class="section-copy">Use order number, SKU, product name, channel, or status to narrow the ledger.</p>
        </div>

        <form method="get" class="filter-grid">
            <label class="field">
                <span>Search</span>
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Order no, SKU, product">
            </label>

            <label class="field">
                <span>Status</span>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="processing" @selected($filters['status'] === 'processing')>Processing</option>
                    <option value="shipped" @selected($filters['status'] === 'shipped')>Shipped</option>
                    <option value="delivered" @selected($filters['status'] === 'delivered')>Delivered</option>
                </select>
            </label>

            <label class="field">
                <span>Channel</span>
                <select name="channel">
                    <option value="">All channels</option>
                    @foreach ($channels as $channel)
                        <option value="{{ $channel->id }}" @selected((int) ($filters['channel'] ?? 0) === $channel->id)>{{ $channel->name }}</option>
                    @endforeach
                </select>
            </label>

            <div class="filter-actions">
                <button type="submit" class="primary-button">Apply</button>
                <a class="ghost-button" href="{{ route('admin.orders.index') }}">Reset</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="section-kicker">Order table</p>
                <h2>{{ $orders->total() }} orders matched</h2>
            </div>
            <p class="section-copy">The ledger keeps demand volume and gross profit visible at the row level.</p>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Channel</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Quantity</th>
                        <th>Revenue</th>
                        <th>Gross profit</th>
                        <th>Ordered at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td>
                                <a href="{{ route('admin.products.show', $order->product) }}">{{ $order->product->name }}</a>
                                <div class="table-subcopy">{{ $order->product->sku }}</div>
                            </td>
                            <td>
                                <span class="status-pill" data-tone="{{ match($order->status) {
                                    'delivered' => 'success',
                                    'processing' => 'warning',
                                    default => 'info',
                                } }}">{{ strtoupper($order->status) }}</span>
                            </td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                            <td>{{ $order->ordered_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No orders match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $orders])
    </section>
@endsection
