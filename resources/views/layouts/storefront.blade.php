<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shop Ops Hub' }}</title>
    @include('partials.style-entry')
</head>
@php
    $planSummary = $planSummary ?? [
        'line_count' => 0,
        'total_quantity' => 0,
        'estimated_value' => 0,
        'average_margin' => 0,
        'fastest_lead_time' => null,
    ];
@endphp
<body class="storefront-body">
    <div class="storefront-utility">
        <span>Shop Ops Hub</span>
        <span>Curated merchandise</span>
    </div>

    <header class="storefront-header">
        <div class="storefront-brand">
            <a href="{{ route('storefront.home') }}">Shop Ops Hub</a>
            <span>Daily essentials</span>
        </div>

        <nav class="storefront-nav">
            <a href="{{ route('storefront.home') }}" @class(['is-active' => request()->routeIs('storefront.home')])>首页</a>
            <a href="{{ route('storefront.catalog') }}" @class(['is-active' => request()->routeIs('storefront.catalog') || request()->routeIs('storefront.products.show')])>商店</a>
            <a href="{{ route('storefront.plan.index') }}" @class(['is-active' => request()->routeIs('storefront.plan.*')])>购物袋</a>
        </nav>

        <form class="storefront-search" method="get" action="{{ route('storefront.catalog') }}">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="搜索商品">
            <button type="submit">搜索</button>
        </form>

        <div class="storefront-actions">
            <a class="plan-badge" href="{{ route('storefront.plan.index') }}">
                <span>{{ $planSummary['total_quantity'] }} 件商品</span>
                <strong>购物袋</strong>
            </a>

            @auth
                <a class="secondary-button storefront-admin-link" href="{{ route('admin.dashboard') }}">后台</a>
            @else
                <a class="secondary-button storefront-admin-link" href="{{ route('login') }}">登录</a>
            @endauth
        </div>
    </header>

    <main class="storefront-shell">
        @if (session('status'))
            <div class="message-banner success-banner storefront-banner">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="message-banner error-banner storefront-banner">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="storefront-footer">
        <div>
            <strong>Shop Ops Hub</strong>
            <p>Curated daily essentials.</p>
        </div>

        <div class="footer-links">
            <a href="{{ route('storefront.catalog') }}">浏览商品</a>
            <a href="{{ route('storefront.plan.index') }}">购物袋</a>
            <a href="{{ route('login') }}">登录</a>
        </div>
    </footer>
</body>
</html>
