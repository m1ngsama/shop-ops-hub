@extends('layouts.app', ['title' => 'Orders | Shop Ops Hub'])

@section('content')
    <section class="hero-panel compact">
        <div>
            <p class="eyebrow">Order intelligence</p>
            <h1>Margin-aware order stream for commerce and ERP workflows.</h1>
            <p class="hero-copy">Each order stores channel fees and ad spend so operational teams can work from contribution profit, not vanity revenue.</p>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Order ledger</h2>
            <p>Recent synced orders across every active channel.</p>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Channel</th>
                        <th>SKU</th>
                        <th>Status</th>
                        <th>Qty</th>
                        <th>Revenue</th>
                        <th>Ad spend</th>
                        <th>Fees</th>
                        <th>Gross profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td>{{ $order->product->sku }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->ad_spend, 2) }}</td>
                            <td>${{ number_format($order->channel_fee, 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
