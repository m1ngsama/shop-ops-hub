@extends('layouts.storefront', ['title' => '购物袋 | Shop Ops Hub'])

@section('content')
    <section class="catalog-hero">
        <div>
            <p class="hero-kicker">Bag</p>
            <h1>你的购物袋。</h1>
            <p class="hero-text">确认商品、数量和金额，然后继续购物。</p>
        </div>

        <div class="catalog-summary">
            <article>
                <span>商品</span>
                <strong>{{ $summary['line_count'] }}</strong>
            </article>
            <article>
                <span>数量</span>
                <strong>{{ $summary['total_quantity'] }}</strong>
            </article>
            <article>
                <span>金额</span>
                <strong>${{ number_format((float) $summary['estimated_value'], 2) }}</strong>
            </article>
        </div>
    </section>

    @if ($items->isEmpty())
        <section class="empty-panel empty-panel-large">
            <strong>购物袋还是空的。</strong>
            <p>去商店挑几件喜欢的商品吧。</p>
            <a class="primary-button" href="{{ route('storefront.catalog') }}">去购物</a>
        </section>
    @else
        <section class="catalog-layout">
            <div class="catalog-main">
                <article class="storefront-panel">
                    <div class="section-heading">
                        <div>
                            <p class="hero-kicker">Items</p>
                            <h2>{{ $summary['line_count'] }} 件商品</h2>
                        </div>
                    </div>

                    <div class="table-shell">
                        <table>
                            <thead>
                                <tr>
                                    <th>商品</th>
                                    <th>品牌</th>
                                    <th>现货</th>
                                    <th>数量</th>
                                    <th>小计</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('storefront.products.show', ['product' => $item['product']->sku]) }}">{{ $item['product']->name }}</a>
                                            <div class="table-subtext">{{ $item['product']->sku }} · {{ $item['product']->category }}</div>
                                        </td>
                                        <td>{{ $item['product']->supplier?->name ?? '精选供应商' }}</td>
                                        <td>{{ $item['available_inventory'] }}</td>
                                        <td>
                                            <form method="post" action="{{ route('storefront.plan.update', ['product' => $item['product']]) }}" class="inline-form">
                                                @csrf
                                                @method('patch')
                                                <input type="number" min="1" name="quantity" value="{{ $item['quantity'] }}">
                                                <button type="submit" class="secondary-button">更新</button>
                                            </form>
                                        </td>
                                        <td>${{ number_format((float) $item['estimated_value'], 2) }}</td>
                                        <td>
                                            <form method="post" action="{{ route('storefront.plan.destroy', ['product' => $item['product']]) }}">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="secondary-button">移除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <aside class="catalog-sidebar">
                <article class="storefront-panel">
                    <div class="section-heading compact-heading">
                        <div>
                            <p class="hero-kicker">Summary</p>
                            <h2>摘要</h2>
                        </div>
                    </div>

                    <div class="summary-stack">
                        <article class="summary-card">
                            <span>商品</span>
                            <strong>{{ $summary['line_count'] }}</strong>
                        </article>
                        <article class="summary-card">
                            <span>数量</span>
                            <strong>{{ $summary['total_quantity'] }}</strong>
                        </article>
                        <article class="summary-card">
                            <span>金额</span>
                            <strong>${{ number_format((float) $summary['estimated_value'], 2) }}</strong>
                        </article>
                        <article class="summary-card">
                            <span>预计发货</span>
                            <strong>{{ $summary['fastest_lead_time'] ?? '--' }} 天</strong>
                        </article>
                    </div>

                    <form method="post" action="{{ route('storefront.plan.clear') }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="secondary-button full-width">清空购物袋</button>
                    </form>

                    <a class="primary-button full-width" href="{{ route('storefront.catalog') }}">继续选购</a>
                </article>

                <article class="storefront-panel">
                    <div class="section-heading compact-heading">
                        <div>
                            <p class="hero-kicker">Next steps</p>
                            <h2>如何提交意向单</h2>
                        </div>
                    </div>
                    <p style="font-size:0.9rem;line-height:1.7;color:#55616d;margin-bottom:14px;">确认商品无误后，截图或导出本页面，通过联系页面发送给我们，我们会在 24 小时内回复并确认采购细节。</p>
                    <p style="font-size:0.85rem;color:#8a95a0;line-height:1.6;">部分商品存在搭配优惠，建议完成全部选品后统一提交，以获得更优的采购条件。</p>
                </article>
            </aside>
        </section>
    @endif
@endsection
