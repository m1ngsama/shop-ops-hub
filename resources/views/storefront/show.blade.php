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
            <p>{{ $product->lead_time_days }} 天交期</p>
        </div>

        <div class="detail-copy">
            <p class="hero-kicker">Product</p>
            <h1>{{ $product->name }}</h1>
            <p class="hero-text">{{ $product->selling_points }}</p>

            <div class="pill-row detail-pills">
                <span class="metric-pill">目标售价 ${{ number_format((float) $product->target_price, 2) }}</span>
                <span class="metric-pill">毛利 {{ number_format($product->marginRate(), 1) }}%</span>
                <span class="metric-pill">可售 {{ $product->availableInventory() }}</span>
                <span class="metric-pill">安全库存 {{ $product->safety_stock }}</span>
            </div>

            <div class="rating-row detail-rating-row">
                <span class="rating-stars">★★★★★</span>
                <strong>{{ number_format($averageScore, 1) }}</strong>
                <span>{{ $reviewCount }} 条反馈 · 平均转化 {{ number_format($averageConversion, 1) }}%</span>
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
            <span class="surface-tag">Supplier</span>
            <h2>供应商</h2>
            <p>{{ $product->supplier?->quality_score ?? '--' }} 分 · {{ $product->supplier?->name ?? '待分配' }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Channels</span>
            <h2>渠道</h2>
            <p>{{ $product->listings->count() }} 个渠道 · {{ $channelCoverage }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Reviews</span>
            <h2>口碑</h2>
            <p>{{ $reviewCount }} 条评价 · 表现分 {{ number_format($averageScore, 1) }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Stock</span>
            <h2>库存</h2>
            <p>{{ $product->availableInventory() > $product->safety_stock ? '库存健康，可直接上架。' : '库存偏紧，建议谨慎放量。' }}</p>
        </article>
    </section>

    <section class="detail-layout">
        <article class="storefront-panel">
            <div class="section-heading compact-heading">
                <div>
                    <p class="hero-kicker">Supply</p>
                    <h2>核心数据</h2>
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
                    <p class="hero-kicker">Channels</p>
                    <h2>刊登情况</h2>
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
                <p class="hero-kicker">Styled With</p>
                <h2>搭配推荐</h2>
            </div>
        </div>

        <div class="product-grid product-grid-compact">
            @foreach ($companionProducts as $companionProduct)
                @include('storefront.partials.product-card', ['product' => $companionProduct])
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">More</p>
                <h2>更多同类商品</h2>
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
