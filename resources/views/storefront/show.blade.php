@extends('layouts.storefront', [
    'title' => $product->name.' | Shop Ops Hub',
    'metaDescription' => $product->selling_points.' — '.$product->category.'，预计 '.$product->lead_time_days.' 天发货。',
])

@section('content')
    @php
        $averageScore = round((float) ($product->listings->avg('performance_score') ?? 0), 1);
        $reviewCount = (int) $product->listings->sum('review_count');
        $starRating = $averageScore > 0 ? round($averageScore / 20, 1) : 0.0;
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
                <span class="rating-stars">★</span>
                <strong>{{ number_format($starRating, 1) }}</strong>
                <span>{{ $reviewCount }} 条反馈</span>
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
            <p>{{ $reviewCount }} 条评价 · {{ number_format($starRating, 1) }}/5</p>
        </article>
        <article class="feature-card compact-card">
            <span class="surface-tag">Availability</span>
            <h2>库存状态</h2>
            <p>{{ $product->availableInventory() > $product->safety_stock ? '现货充足，可立即购买。' : '库存有限，建议尽快下单。' }}</p>
        </article>
    </section>

    <section class="storefront-editorial-split storefront-editorial-split-compact">
        <article class="editorial-card editorial-card-strong">
            <p class="hero-kicker">About this item</p>
            <h2>{{ $product->marketplace_focus }}</h2>
            <p>{{ $product->selling_points }}</p>
        </article>

        <article class="editorial-card">
            <span class="surface-tag">Why you'll love it</span>
            <strong>{{ $product->availableInventory() > $product->safety_stock ? '现货充足，适合立即下单。' : '热门商品，建议尽快加入购物袋。' }}</strong>
            <p>{{ $reviewCount }} 条真实反馈，综合评分 {{ number_format($starRating, 1) }}/5。</p>
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

    @if ($faqItems->isNotEmpty())
    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">FAQ</p>
                <h2>常见问题</h2>
            </div>
        </div>

        <div class="faq-grid">
            @foreach ($faqItems as $faq)
                <article class="faq-card">
                    <strong>{{ $faq['question'] }}</strong>
                    <p>{{ $faq['answer'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
    @endif
@endsection
