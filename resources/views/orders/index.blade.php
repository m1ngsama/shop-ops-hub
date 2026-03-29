@extends('layouts.app', ['title' => '订单中心 | 商运后台'])

@section('page_kicker', '订单模块')
@section('page_title', '订单中心')
@section('page_copy', '按渠道、状态与关键词筛选订单，快速查看销售额、毛利和执行进度。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.channels.index') }}">同步渠道</a>
@endsection

@section('content')
    @php
        $orderStatusMap = ['processing' => '处理中', 'shipped' => '已发货', 'delivered' => '已签收'];
        $statusTone = ['processing' => 'warning', 'shipped' => 'info', 'delivered' => 'success'];
    @endphp

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">筛选条件</p>
                <h2>过滤订单</h2>
            </div>
        </div>

        <form method="get" class="filter-grid">
            <label class="field">
                <span>关键词</span>
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="订单号、SKU、商品名">
            </label>

            <label class="field">
                <span>状态</span>
                <select name="status">
                    <option value="">全部</option>
                    <option value="processing" @selected($filters['status'] === 'processing')>处理中</option>
                    <option value="shipped" @selected($filters['status'] === 'shipped')>已发货</option>
                    <option value="delivered" @selected($filters['status'] === 'delivered')>已签收</option>
                </select>
            </label>

            <label class="field">
                <span>渠道</span>
                <select name="channel">
                    <option value="">全部</option>
                    @foreach ($channels as $channel)
                        <option value="{{ $channel->id }}" @selected((int) ($filters['channel'] ?? 0) === $channel->id)>{{ $channel->name }}</option>
                    @endforeach
                </select>
            </label>

            <div class="filter-actions">
                <button type="submit" class="primary-button">筛选</button>
                <a class="secondary-button" href="{{ route('admin.orders.index') }}">重置</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="page-kicker">订单列表</p>
                <h2>共 {{ $orders->total() }} 条订单</h2>
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
                        <th>数量</th>
                        <th>销售额</th>
                        <th>毛利</th>
                        <th>下单时间</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td>
                                <a href="{{ route('admin.products.show', $order->product) }}">{{ $order->product->name }}</a>
                                <div class="table-subtext">{{ $order->product->sku }}</div>
                            </td>
                            <td><span class="status-chip tone-{{ $statusTone[$order->status] ?? 'neutral' }}">{{ $orderStatusMap[$order->status] ?? $order->status }}</span></td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                            <td>{{ $order->ordered_at?->format('m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">当前没有符合条件的订单。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $orders])
    </section>
@endsection
