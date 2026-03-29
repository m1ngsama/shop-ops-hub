@extends('layouts.storefront', ['title' => $product->name.' | 前台选品站'])

@section('content')
    @php
        $averageScore = round((float) ($product->listings->avg('performance_score') ?? 0), 1);
        $averageConversion = round((float) ($product->listings->avg('conversion_rate') ?? 0), 1);
        $reviewCount = (int) $product->listings->sum('review_count');
        $channelCoverage = $product->listings->pluck('channel.name')->implode(' / ');
    @endphp

    <section class="detail-hero">
        <div class="detail-surface">
            <span class="surface-tag">{{ $product->category }}</span>
            <strong>{{ $product->sku }}</strong>
            <p>{{ $product->marketplace_focus }}</p>
        </div>

        <div class="detail-copy">
            <p class="hero-kicker">商品详情</p>
            <h1>{{ $product->name }}</h1>
            <p class="hero-text">{{ $product->selling_points }}</p>

            <div class="pill-row detail-pills">
                <span class="metric-pill">目标售价 ${{ number_format((float) $product->target_price, 2) }}</span>
                <span class="metric-pill">毛利 {{ number_format($product->marginRate(), 1) }}%</span>
                <span class="metric-pill">可售 {{ $product->availableInventory() }}</span>
                <span class="metric-pill">安全库存 {{ $product->safety_stock }}</span>
            </div>

            <form method="post" action="{{ route('storefront.plan.store', ['product' => $product]) }}" class="detail-form">
                @csrf
                <label class="field field-inline">
                    <span>加入数量</span>
                    <input type="number" min="1" name="quantity" value="{{ max(1, $selectedQuantity ?: 1) }}">
                </label>
                <button type="submit" class="primary-button">加入意向清单</button>
                <a class="secondary-button" href="{{ route('storefront.catalog') }}">返回目录</a>
            </form>
        </div>
    </section>

    <section class="feature-grid feature-grid-4">
        <article class="feature-card compact-card">
            <span class="surface-tag">供给</span>
            <h2>供应质量</h2>
            <p>{{ $product->supplier?->quality_score ?? '--' }} 分 · {{ $product->supplier?->name ?? '待分配' }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">渠道</span>
            <h2>刊登覆盖</h2>
            <p>{{ $product->listings->count() }} 个渠道 · {{ $channelCoverage }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">口碑</span>
            <h2>评价规模</h2>
            <p>{{ $reviewCount }} 条评价 · 表现分 {{ number_format($averageScore, 1) }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">执行</span>
            <h2>采购判断</h2>
            <p>{{ $product->availableInventory() > $product->safety_stock ? '库存安全，可加快试单。' : '库存偏紧，应先评估补货。' }}</p>
        </article>
    </section>

    <section class="editorial-band">
        <article class="editorial-card editorial-card-strong">
            <p class="hero-kicker">经营判断</p>
            <h2>{{ $product->marketplace_focus }}</h2>
            <p>{{ $product->selling_points }}</p>
        </article>
        <article class="editorial-card">
            <span class="surface-tag">当前建议</span>
            <strong>{{ $product->lead_time_days <= 18 ? '适合快速试投与组合陈列' : '更适合计划性备货与节奏化投放' }}</strong>
            <p>结合交期、库存与渠道覆盖，先在高匹配场景下做小批量验证，再决定是否扩量。</p>
        </article>
    </section>

    <section class="detail-layout">
        <article class="storefront-panel">
            <div class="section-heading compact-heading">
                <div>
                    <p class="hero-kicker">供给能力</p>
                    <h2>核心经营数据</h2>
                </div>
            </div>

            <div class="summary-stack">
                <article class="summary-card">
                    <span>供应商</span>
                    <strong>{{ $product->supplier?->name ?? '未分配' }}</strong>
                </article>
                <article class="summary-card">
                    <span>交期</span>
                    <strong>{{ $product->lead_time_days }} 天</strong>
                </article>
                <article class="summary-card">
                    <span>平均表现分</span>
                    <strong>{{ number_format($averageScore, 1) }}</strong>
                </article>
                <article class="summary-card">
                    <span>平均转化率</span>
                    <strong>{{ number_format($averageConversion, 1) }}%</strong>
                </article>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>仓库</th>
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

        <article class="storefront-panel">
            <div class="section-heading compact-heading">
                <div>
                    <p class="hero-kicker">渠道准备度</p>
                    <h2>当前刊登情况</h2>
                </div>
            </div>

            <div class="channel-strip channel-strip-compact">
                @foreach ($product->listings as $listing)
                    <article class="channel-tile">
                        <div>
                            <span class="surface-tag">{{ $listing->channel->marketplace }}</span>
                            <strong>{{ $listing->channel->name }}</strong>
                        </div>
                        <p>{{ $listing->external_sku }}</p>
                        <div class="pill-row">
                            <span class="metric-pill">价格 ${{ number_format((float) $listing->price, 2) }}</span>
                            <span class="metric-pill">转化 {{ number_format((float) $listing->conversion_rate, 1) }}%</span>
                            <span class="metric-pill">评分 {{ number_format((float) $listing->performance_score, 1) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">相关商品</p>
                <h2>同类目推荐</h2>
            </div>
        </div>

        <div class="product-grid">
            @forelse ($relatedProducts as $relatedProduct)
                @include('storefront.partials.product-card', ['product' => $relatedProduct])
            @empty
                <article class="empty-panel">
                    <strong>当前没有更多同类商品。</strong>
                    <p>可以返回目录浏览其它类目。</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection
