@extends('layouts.app', ['title' => $product->name.' | 商运后台'])

@section('page_kicker', '商品详情')
@section('page_title', $product->name)
@section('page_copy', $product->sku.' · 查看供货、库存批次、刊登状态与订单记录。')
@section('page_actions')
    <a class="secondary-button" href="{{ route('admin.products.index') }}">返回列表</a>
@endsection

@section('content')
    @php
        $orderStatusMap = ['processing' => '处理中', 'shipped' => '已发货', 'delivered' => '已签收'];
        $statusTone = ['processing' => 'warning', 'shipped' => 'info', 'delivered' => 'success'];
    @endphp

    <section class="metrics-grid">
        <article class="metric-card">
            <span>目标售价</span>
            <strong>${{ number_format((float) $product->target_price, 2) }}</strong>
        </article>
        <article class="metric-card">
            <span>成本价</span>
            <strong>${{ number_format((float) $product->cost_price, 2) }}</strong>
        </article>
        <article class="metric-card">
            <span>履约费用</span>
            <strong>${{ number_format((float) $product->fulfillment_fee, 2) }}</strong>
        </article>
        <article class="metric-card">
            <span>毛利率</span>
            <strong>{{ number_format($product->marginRate(), 1) }}%</strong>
        </article>
        <article class="metric-card">
            <span>可售库存</span>
            <strong>{{ $product->availableInventory() }}</strong>
        </article>
        <article class="metric-card">
            <span>安全库存</span>
            <strong>{{ $product->safety_stock }}</strong>
        </article>
    </section>

    <section class="panel-grid panel-grid-2">
        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">基础信息</p>
                    <h2>经营属性</h2>
                </div>
            </div>

            <div class="detail-grid">
                <article class="detail-card">
                    <span>类目</span>
                    <strong>{{ $product->category }}</strong>
                </article>
                <article class="detail-card">
                    <span>运营策略</span>
                    <strong>{{ $product->marketplace_focus }}</strong>
                </article>
                <article class="detail-card">
                    <span>供应商</span>
                    <strong>{{ $product->supplier?->name ?? '未分配' }}</strong>
                </article>
                <article class="detail-card">
                    <span>供货邮箱</span>
                    <strong>{{ $product->supplier?->contact_email ?? '未设置' }}</strong>
                </article>
                <article class="detail-card">
                    <span>交期</span>
                    <strong>{{ $product->lead_time_days }} 天</strong>
                </article>
                <article class="detail-card">
                    <span>质量评分</span>
                    <strong>{{ $product->supplier?->quality_score ?? 'N/A' }}</strong>
                </article>
            </div>

            <div class="note-block">{{ $product->selling_points }}</div>
        </article>

        <article class="panel">
            <div class="panel-header">
                <div>
                    <p class="page-kicker">库存批次</p>
                    <h2>仓内分布</h2>
                </div>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>仓库</th>
                            <th>批次</th>
                            <th>在库</th>
                            <th>占用</th>
                            <th>在途</th>
                            <th>预计到仓</th>
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
                                <td>{{ $batch->inbound_eta?->format('Y-m-d') ?? '暂无' }}</td>
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
                <p class="page-kicker">渠道刊登</p>
                <h2>当前刊登状态</h2>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>渠道</th>
                        <th>外部 SKU</th>
                        <th>状态</th>
                        <th>价格</th>
                        <th>评论数</th>
                        <th>转化率</th>
                        <th>表现分</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product->listings as $listing)
                        <tr>
                            <td>{{ $listing->channel->name }}</td>
                            <td>{{ $listing->external_sku }}</td>
                            <td><span class="status-chip tone-{{ $listing->status === 'active' ? 'success' : 'warning' }}">{{ $listing->status === 'active' ? '上架中' : '已暂停' }}</span></td>
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
                <p class="page-kicker">订单记录</p>
                <h2>最近成交</h2>
            </div>
        </div>

        <div class="table-shell">
            <table>
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>渠道</th>
                        <th>状态</th>
                        <th>数量</th>
                        <th>销售额</th>
                        <th>毛利</th>
                        <th>下单时间</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->orders->sortByDesc('ordered_at') as $order)
                        <tr>
                            <td>{{ $order->external_order_no }}</td>
                            <td>{{ $order->channel->name }}</td>
                            <td><span class="status-chip tone-{{ $statusTone[$order->status] ?? 'neutral' }}">{{ $orderStatusMap[$order->status] ?? $order->status }}</span></td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->revenue(), 2) }}</td>
                            <td>${{ number_format($order->grossProfit(), 2) }}</td>
                            <td>{{ $order->ordered_at?->format('m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">当前没有订单记录。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
