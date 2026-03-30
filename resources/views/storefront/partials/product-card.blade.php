@php
    $reviewCount = (int) $product->listings->sum('review_count');
    $averageScore = round((float) ($product->listings->avg('performance_score') ?? 0), 1);
@endphp

<article class="product-card">
    <div class="product-surface">
        <span class="surface-tag">{{ $product->category }}</span>
        <strong>{{ $product->sku }}</strong>
        <p>{{ $product->lead_time_days }} 天发货</p>
    </div>

    <div class="product-card-body">
        <div class="product-card-head">
            <div>
                <h3>{{ $product->name }}</h3>
                <p>{{ $product->supplier?->name ?? '精选供应商' }}</p>
            </div>
            <strong class="price-mark">${{ number_format((float) $product->target_price, 2) }}</strong>
        </div>

        <div class="rating-row">
            <span class="rating-stars">★★★★★</span>
            <strong>{{ number_format($averageScore, 1) }}</strong>
            <span>{{ $reviewCount }} 条反馈</span>
        </div>

        <p class="product-card-copy">{{ $product->selling_points }}</p>

        <div class="pill-row">
            <span class="metric-pill">现货 {{ $product->availableInventory() }}</span>
            <span class="metric-pill">评分 {{ number_format($averageScore, 1) }}</span>
        </div>

        <div class="card-actions">
            <a class="secondary-button" href="{{ route('storefront.products.show', ['product' => $product->sku]) }}">查看详情</a>

            <form method="post" action="{{ route('storefront.plan.store', ['product' => $product]) }}">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="primary-button">加入购物袋</button>
            </form>
        </div>
    </div>
</article>
