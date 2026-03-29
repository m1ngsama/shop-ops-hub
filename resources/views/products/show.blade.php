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

    <section class="admin-hero-grid">
        <article class="admin-callout">
            <p class="page-kicker">Product Detail</p>
            <h2>围绕单个商品组织供给、刊登、库存与成交，减少来回跳转。</h2>
            <p>
                这个页面负责回答一个具体问题：这款商品现在是否值得继续卖、补货或扩量，以及问题究竟出在供给、刊登还是履约侧。
            </p>
        </article>

        <article class="admin-stat-ribbon">
            <div>
                <span>目标售价</span>
                <strong>${{ number_format((float) $product->target_price, 2) }}</strong>
            </div>
            <div>
                <span>毛利率</span>
                <strong>{{ number_format($product->marginRate(), 1) }}%</strong>
            </div>
            <div>
                <span>可售库存</span>
                <strong>{{ $product->availableInventory() }}</strong>
            </div>
        </article>
    </section>

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
                    <p class="page-copy">把经营定位、供应商关系和交期前置，便于快速判断这款商品的基本可操作性。</p>
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
                    <p class="page-copy">直接查看在库、占用和在途数量，判断短期履约压力是否可控。</p>
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
                <p class="page-copy">集中看外部 SKU、价格、评论、转化和表现分，不用切去渠道页再核对。</p>
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
                <p class="page-copy">帮助回看这款商品近期的成交质量，而不仅仅是静态商品信息。</p>
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
