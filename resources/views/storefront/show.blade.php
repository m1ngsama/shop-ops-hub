@extends('layouts.storefront', ['title' => $product->name.' | Shop Ops Hub'])

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
                <span class="metric-pill">售价 ${{ number_format((float) $product->target_price, 2) }}</span>
                <span class="metric-pill">现货 {{ $product->availableInventory() }}</span>
                <span class="metric-pill">{{ $product->lead_time_days }} 天发货</span>
            </div>

            <div class="rating-row detail-rating-row">
                <span class="rating-stars">★★★★★</span>
                <strong>{{ number_format($averageScore, 1) }}</strong>
                <span>{{ $reviewCount }} 条反馈 · 平均转化 {{ number_format($averageConversion, 1) }}%</span>
            </div>

            <form method="post" action="{{ route('storefront.plan.store', ['product' => $product]) }}" class="detail-form">
                @csrf
                <label class="field field-inline">
                    <span>数量</span>
                    <input type="number" min="1" name="quantity" value="{{ max(1, $selectedQuantity ?: 1) }}">
                </label>
                <button type="submit" class="primary-button">加入购物袋</button>
                <a class="secondary-button" href="{{ route('storefront.catalog') }}">继续购物</a>
            </form>
        </div>
    </section>

    <section class="feature-grid feature-grid-4">
        <article class="feature-card compact-card">
            <span class="surface-tag">Material</span>
            <h2>品牌信息</h2>
            <p>{{ $product->supplier?->name ?? '精选供应商' }}</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Shipping</span>
            <h2>配送</h2>
            <p>预计 {{ $product->lead_time_days }} 天发货</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Reviews</span>
            <h2>用户评价</h2>
            <p>{{ $reviewCount }} 条评价 · {{ number_format($averageScore, 1) }} 分</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Availability</span>
            <h2>库存状态</h2>
            <p>{{ $product->availableInventory() > $product->safety_stock ? '现货充足，可立即购买。' : '库存有限，建议尽快下单。' }}</p>
        </article>
    </section>

    <section class="detail-layout">
        <article class="storefront-panel">
            <div class="section-heading compact-heading">
                <div>
                    <p class="hero-kicker">Details</p>
                    <h2>商品信息</h2>
                </div>
            </div>

            <div class="summary-stack">
                <article class="summary-card">
                    <span>品牌</span>
                    <strong>{{ $product->supplier?->name ?? '精选供应商' }}</strong>
                </article>
                <article class="summary-card">
                    <span>发货时间</span>
                    <strong>{{ $product->lead_time_days }} 天</strong>
                </article>
                <article class="summary-card">
                    <span>评分</span>
                    <strong>{{ number_format($averageScore, 1) }}</strong>
                </article>
                <article class="summary-card">
                    <span>评价数</span>
                    <strong>{{ $reviewCount }}</strong>
                </article>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>仓库</th>
                            <th>现货</th>
                            <th>锁定</th>
                            <th>补货中</th>
                            <th>到货时间</th>
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
                    <p class="hero-kicker">Reviews</p>
                    <h2>购买参考</h2>
                </div>
            </div>

            <div class="channel-strip channel-strip-compact">
                @foreach ($product->listings as $listing)
                    <article class="channel-tile">
                        <div>
                            <span class="surface-tag">{{ $listing->channel->marketplace }}</span>
                            <strong>{{ $listing->channel->name }}</strong>
                        </div>
                        <p>{{ $listing->review_count }} 条评价</p>
                        <div class="pill-row">
                            <span class="metric-pill">售价 ${{ number_format((float) $listing->price, 2) }}</span>
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
                <h2>搭配购买</h2>
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
                <p class="hero-kicker">You may also like</p>
                <h2>你可能还喜欢</h2>
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
