@extends('layouts.storefront', ['title' => '零售样板站'])

@section('content')
    <section class="hero-shell">
        <div class="hero-copy">
            <p class="hero-kicker">Merchandising Frontend</p>
            <h1>重新组织商品前台，让浏览、比较和协同看起来更像一套完整产品，而不是后台附属页。</h1>
            <p class="hero-text">
                这一版参考 Apple Developer 首页的信息节奏，强调清晰的分区、大尺度标题、轻量卡片和更干净的留白，
                让公开浏览、经营判断与后台运营回到同一条连续的成交路径上。
            </p>

            <div class="pill-row hero-pills">
                <span class="metric-pill">活跃商品 {{ $heroSummary['active_products'] }}</span>
                <span class="metric-pill">可售库存 {{ $heroSummary['available_inventory'] }}</span>
                <span class="metric-pill">平均毛利 {{ number_format($heroSummary['average_margin'], 1) }}%</span>
                <span class="metric-pill">最快交期 {{ $heroSummary['fastest_lead_time'] }} 天</span>
            </div>

            <div class="hero-actions">
                <a class="primary-button" href="{{ route('storefront.catalog') }}">浏览商品目录</a>
                <a class="secondary-button" href="{{ route('storefront.plan.index') }}">查看意向清单</a>
            </div>
        </div>

        <div class="hero-stage">
            <article class="hero-panel">
                <div class="hero-panel-head">
                    <span class="surface-tag">当前意向</span>
                    <strong>{{ $planSummary['line_count'] }} 个条目</strong>
                </div>

                <div class="hero-panel-metrics">
                    <article>
                        <span>总数量</span>
                        <strong>{{ $planSummary['total_quantity'] }}</strong>
                    </article>
                    <article>
                        <span>预估金额</span>
                        <strong>${{ number_format((float) $planSummary['estimated_value'], 2) }}</strong>
                    </article>
                    <article>
                        <span>平均毛利</span>
                        <strong>{{ number_format((float) $planSummary['average_margin'], 1) }}%</strong>
                    </article>
                    <article>
                        <span>最快交期</span>
                        <strong>{{ $planSummary['fastest_lead_time'] ?? '--' }} 天</strong>
                    </article>
                </div>
            </article>

            <div class="hero-mini-grid">
                @foreach ($featuredProducts->take(3) as $product)
                    <article class="hero-mini-card">
                        <span class="surface-tag">{{ $product->category }}</span>
                        <strong>{{ $product->name }}</strong>
                        <p>{{ $product->sku }} · ${{ number_format((float) $product->target_price, 2) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="service-band">
        @foreach ($servicePromises as $promise)
            <article class="service-band-card">
                <span class="surface-tag">前台能力</span>
                <strong>{{ $promise['title'] }}</strong>
                <p>{{ $promise['copy'] }}</p>
                <b>{{ $promise['value'] }}</b>
            </article>
        @endforeach
    </section>

    <section class="feature-grid">
        <article class="feature-card">
            <span class="surface-tag">搜索</span>
            <h2>先搜索，再浏览</h2>
            <p>顶部搜索、目录筛选和商品比较同时存在，让站点更接近真正的零售前台而不是单页展示。</p>
        </article>
        <article class="feature-card">
            <span class="surface-tag">详情</span>
            <h2>详情页承担成交解释</h2>
            <p>每个商品详情页都同时回答价格、供给、交期、评价规模和经营建议，减少跳转损耗。</p>
        </article>
        <article class="feature-card">
            <span class="surface-tag">协同</span>
            <h2>前台与后台不是两套系统</h2>
            <p>同一套商品、库存、渠道和订单数据同时驱动公开前台和后台控制台，避免展示与执行割裂。</p>
        </article>
    </section>

    <section class="editorial-band">
        <article class="editorial-card editorial-card-strong">
            <p class="hero-kicker">陈列策略</p>
            <h2>前台不是宣传页，而是能支持成交决策的商品场。</h2>
            <p>页面结构围绕“搜商品、看详情、比方案、加意向、回后台执行”展开，不把用户停留在空洞视觉层。</p>
        </article>
        <article class="editorial-card">
            <span class="surface-tag">协同目标</span>
            <strong>前台负责发现与比较，后台负责定价、库存与执行。</strong>
            <p>这套程序把商品浏览、买家判断、运营执行和任务回溯全部建立在同一份数据模型上。</p>
        </article>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">类目视图</p>
                <h2>用类目看选品节奏</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">查看完整目录</a>
        </div>

        <div class="category-grid">
            @foreach ($categoryHighlights as $category)
                <article class="category-card">
                    <span class="surface-tag">{{ $category['name'] }}</span>
                    <strong>{{ $category['sku_count'] }} 个 SKU</strong>
                    <p>平均毛利 {{ number_format($category['average_margin'], 1) }}% · 平均交期 {{ $category['average_lead_time'] }} 天</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">场景组合</p>
                <h2>按经营目标打包候选商品</h2>
            </div>
        </div>

        <div class="collection-grid">
            @foreach ($curatedCollections as $collection)
                <article class="collection-card">
                    <div class="collection-head">
                        <span class="surface-tag">{{ $collection['tag'] }}</span>
                        <strong>{{ $collection['title'] }}</strong>
                    </div>
                    <p>{{ $collection['copy'] }}</p>

                    <div class="pill-row">
                        <span class="metric-pill">组合客单 ${{ number_format((float) $collection['estimated_ticket'], 2) }}</span>
                        <span class="metric-pill">平均毛利 {{ number_format($collection['average_margin'], 1) }}%</span>
                        <span class="metric-pill">平均交期 {{ $collection['average_lead_time'] }} 天</span>
                    </div>

                    <div class="collection-products">
                        @foreach ($collection['products'] as $product)
                            <a class="collection-product" href="{{ route('storefront.products.show', ['product' => $product->sku]) }}">
                                <span>{{ $product->category }}</span>
                                <strong>{{ $product->name }}</strong>
                                <p>{{ $product->sku }} · ${{ number_format((float) $product->target_price, 2) }}</p>
                            </a>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">推荐陈列</p>
                <h2>像成熟站点一样展示候选商品</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">全部商品</a>
        </div>

        <div class="product-grid">
            @foreach ($featuredProducts as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">商品比较</p>
                <h2>像采购台一样横向比较候选商品</h2>
            </div>
            <a class="text-link" href="{{ route('storefront.catalog') }}">进入完整目录</a>
        </div>

        <div class="comparison-shell">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>比较项</th>
                        @foreach ($comparisonProducts as $product)
                            <th>{{ $product->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>目标售价</td>
                        @foreach ($comparisonProducts as $product)
                            <td>${{ number_format((float) $product->target_price, 2) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>毛利率</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ number_format($product->marginRate(), 1) }}%</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>可售库存</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->availableInventory() }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>交期</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->lead_time_days }} 天</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>渠道覆盖</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->listings->pluck('channel.name')->implode(' / ') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>评价规模</td>
                        @foreach ($comparisonProducts as $product)
                            <td>{{ $product->listings->sum('review_count') }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">买家反馈摘要</p>
                <h2>把评价与转化信号也前置到首页</h2>
            </div>
        </div>

        <div class="voice-grid">
            @foreach ($buyerVoices as $voice)
                <article class="voice-card">
                    <div class="voice-head">
                        <div>
                            <span class="surface-tag">{{ $voice['product']->category }}</span>
                            <strong>{{ $voice['product']->name }}</strong>
                        </div>
                        <span class="voice-score">{{ number_format($voice['score'], 1) }}</span>
                    </div>
                    <p>“{{ $voice['quote'] }}”</p>
                    <div class="pill-row">
                        <span class="metric-pill">评价 {{ $voice['review_count'] }}</span>
                        <span class="metric-pill">转化 {{ number_format($voice['conversion'], 1) }}%</span>
                        <span class="metric-pill">{{ $voice['product']->sku }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">渠道准备度</p>
                <h2>前台也能表达供给和履约能力</h2>
            </div>
        </div>

        <div class="channel-strip">
            @foreach ($channelHighlights as $channel)
                <article class="channel-tile">
                    <div>
                        <span class="surface-tag">{{ $channel->region }}</span>
                        <strong>{{ $channel->name }}</strong>
                    </div>
                    <p>{{ $channel->marketplace }} · {{ $channel->currency }}</p>
                    <div class="pill-row">
                        <span class="metric-pill">刊登 {{ $channel->listings_count }}</span>
                        <span class="metric-pill">订单 {{ $channel->orders_count }}</span>
                        <span class="metric-pill">费率 {{ number_format((float) $channel->fee_percentage, 1) }}%</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">供应协同</p>
                <h2>供应商稳定性应该被看见</h2>
            </div>
        </div>

        <div class="supplier-grid">
            @foreach ($supplierHighlights as $supplier)
                <article class="supplier-card">
                    <div class="collection-head">
                        <span class="surface-tag">质量 {{ $supplier['quality_score'] ?? '--' }}</span>
                        <strong>{{ $supplier['name'] }}</strong>
                    </div>
                    <p>{{ $supplier['categories'] }} · 平均交期 {{ $supplier['average_lead_time'] }} 天</p>
                    <div class="pill-row">
                        <span class="metric-pill">SKU {{ $supplier['sku_count'] }}</span>
                        <span class="metric-pill">质量分 {{ $supplier['quality_score'] ?? '--' }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="storefront-section">
        <div class="section-heading">
            <div>
                <p class="hero-kicker">成交路径</p>
                <h2>前台到后台的协同流程</h2>
            </div>
        </div>

        <div class="journey-grid">
            <article class="journey-card">
                <span>01</span>
                <strong>浏览与比较</strong>
                <p>先通过目录、组合与比较矩阵筛掉不符合毛利和交期要求的商品。</p>
            </article>
            <article class="journey-card">
                <span>02</span>
                <strong>加入意向清单</strong>
                <p>把保留下来的商品加入意向清单，记录数量、预估金额和供给能力。</p>
            </article>
            <article class="journey-card">
                <span>03</span>
                <strong>回到后台执行</strong>
                <p>后台继续处理库存覆盖、渠道刊登、订单执行和利润分析，不在前台停留。</p>
            </article>
        </div>
    </section>
@endsection
