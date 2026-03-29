@extends('layouts.storefront', ['title' => '商品目录 | 前台选品站'])

@section('content')
    <section class="catalog-hero">
        <div>
            <p class="hero-kicker">商品目录</p>
            <h1>按类目、价格、毛利和交期，快速筛出值得继续看的商品。</h1>
            <p class="hero-text">目录页承担真正的浏览任务：搜关键词、看筛选、做比较，再把候选商品带去详情页和意向清单。</p>
        </div>

        <div class="catalog-summary">
            <article>
                <span>匹配商品</span>
                <strong>{{ $products->total() }}</strong>
            </article>
            <article>
                <span>已选条目</span>
                <strong>{{ $planSummary['line_count'] }}</strong>
            </article>
            <article>
                <span>预估金额</span>
                <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
            </article>
        </div>
    </section>

    <section class="storefront-editorial-split storefront-editorial-split-compact">
        <article class="editorial-card editorial-card-strong">
            <p class="hero-kicker">Browse With Intent</p>
            <h2>先缩小选择范围，再进入真正值得花时间比较的商品。</h2>
            <p>目录页承担的是筛选和取舍，不是堆满所有商品。关键词、类目、排序和意向摘要应该一起工作。</p>
        </article>

        <article class="editorial-card">
            <span class="surface-tag">当前视图</span>
            <strong>共 {{ $products->total() }} 个候选商品，当前排序为 {{ ['recommended' => '推荐优先', 'margin' => '毛利优先', 'lead_time' => '交期优先', 'price_low' => '价格从低到高', 'price_high' => '价格从高到低'][$filters['sort']] ?? '推荐优先' }}</strong>
            <p>把浏览任务压缩到更小、更清晰的一组结果里，再进入详情页做解释和比较。</p>
        </article>
    </section>

    <section class="catalog-chip-strip">
        <a class="catalog-chip @if($filters['category'] === '') is-active @endif" href="{{ route('storefront.catalog') }}">全部类目</a>
        @foreach ($categories as $category)
            <a
                class="catalog-chip @if($filters['category'] === $category) is-active @endif"
                href="{{ route('storefront.catalog', array_filter(['category' => $category, 'search' => $filters['search'], 'sort' => $filters['sort']])) }}"
            >
                {{ $category }}
            </a>
        @endforeach
    </section>

    <section class="catalog-layout">
        <aside class="catalog-sidebar">
            <article class="storefront-panel">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="hero-kicker">筛选</p>
                        <h2>过滤商品</h2>
                        <p class="page-copy">从关键词、类目和排序切入，快速形成更聚焦的商品池。</p>
                    </div>
                </div>

                <form method="get" class="form-stack">
                    <label class="field">
                        <span>关键词</span>
                        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="SKU、商品名、卖点">
                    </label>

                    <label class="field">
                        <span>类目</span>
                        <select name="category">
                            <option value="">全部</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="field">
                        <span>排序</span>
                        <select name="sort">
                            <option value="recommended" @selected($filters['sort'] === 'recommended')>推荐优先</option>
                            <option value="margin" @selected($filters['sort'] === 'margin')>毛利优先</option>
                            <option value="lead_time" @selected($filters['sort'] === 'lead_time')>交期优先</option>
                            <option value="price_low" @selected($filters['sort'] === 'price_low')>价格从低到高</option>
                            <option value="price_high" @selected($filters['sort'] === 'price_high')>价格从高到低</option>
                        </select>
                    </label>

                    <div class="filter-actions">
                        <button type="submit" class="primary-button">应用筛选</button>
                        <a class="secondary-button" href="{{ route('storefront.catalog') }}">重置</a>
                    </div>
                </form>
            </article>

            <article class="storefront-panel">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="hero-kicker">意向清单</p>
                        <h2>当前摘要</h2>
                        <p class="page-copy">把浏览结果和后续动作挂钩，而不是看完就散。</p>
                    </div>
                </div>

                <div class="summary-stack">
                    <article class="summary-card">
                        <span>条目数</span>
                        <strong>{{ $planSummary['line_count'] }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>总数量</span>
                        <strong>{{ $planSummary['total_quantity'] }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>预估金额</span>
                        <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
                    </article>
                    <article class="summary-card">
                        <span>平均毛利</span>
                        <strong>{{ number_format((float) $planSummary['average_margin'], 1) }}%</strong>
                    </article>
                </div>

                <a class="secondary-button full-width" href="{{ route('storefront.plan.index') }}">打开意向清单</a>
            </article>
        </aside>

        <div class="catalog-main">
            <div class="section-heading">
                <div>
                    <p class="hero-kicker">结果列表</p>
                    <h2>共 {{ $products->total() }} 个候选商品</h2>
                    <p class="page-copy">当前排序：{{ ['recommended' => '推荐优先', 'margin' => '毛利优先', 'lead_time' => '交期优先', 'price_low' => '价格从低到高', 'price_high' => '价格从高到低'][$filters['sort']] ?? '推荐优先' }}</p>
                </div>
            </div>

            <div class="product-grid">
                @forelse ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @empty
                    <article class="empty-panel">
                        <strong>当前没有符合条件的商品。</strong>
                        <p>可以尝试放宽关键词或切换类目。</p>
                    </article>
                @endforelse
            </div>

            @include('partials.pagination', ['paginator' => $products])
        </div>
    </section>
@endsection
