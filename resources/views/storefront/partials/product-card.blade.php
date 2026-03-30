@php
    $reviewCount = (int) $product->listings->sum('review_count');
    $averageScore = round((float) ($product->listings->avg('performance_score') ?? 0), 1);
@endphp

<article class="product-card">
    <div class="product-surface">
        <span class="surface-tag">{{ $product->category }}</span>
        <strong>{{ $product->sku }}</strong>
        <p>{{ $product->lead_time_days }} 天交期</p>
    </div>

    <div class="product-card-body">
        <div class="product-card-head">
            <div>
                <h3>{{ $product->name }}</h3>
                <p>{{ $product->supplier?->name ?? '供应链待分配' }}</p>
            </div>
            <strong class="price-mark">${{ number_format((float) $product->target_price, 2) }}</strong>
        </div>

        <div class="rating-row">
            <span class="rating-stars">★★★★★</span>
            <strong>{{ number_format($averageScore, 1) }}</strong>
            <span>{{ $reviewCount }} 条反馈</span>
        </div>

        <p class="product-card-copy">{{ $product->marketplace_focus }}</p>

        <div class="pill-row">
            <span class="metric-pill">毛利 {{ number_format($product->marginRate(), 1) }}%</span>
            <span class="metric-pill">可售 {{ $product->availableInventory() }}</span>
            <span class="metric-pill">评分 {{ number_format($averageScore, 1) }}</span>
        </div>

        <div class="card-actions">
            <a class="secondary-button" href="{{ route('storefront.products.show', ['product' => $product->sku]) }}">查看详情</a>

            <form method="post" action="{{ route('storefront.plan.store', ['product' => $product]) }}">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="primary-button">加入意向清单</button>
            </form>
        </div>
    </div>
</article>
