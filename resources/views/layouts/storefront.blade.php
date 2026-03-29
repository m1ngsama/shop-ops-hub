<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '前台选品站' }}</title>
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
        <span>Merchandising Experience</span>
        <span>用更克制的前端语言呈现商品、供给与意向协同</span>
    </div>

    <header class="storefront-header">
        <div class="storefront-brand">
            <a href="{{ route('storefront.home') }}">Shop Ops Hub</a>
            <span>Apple Developer 风格启发下的商品陈列与协同前台</span>
        </div>

        <nav class="storefront-nav">
            <a href="{{ route('storefront.home') }}" @class(['is-active' => request()->routeIs('storefront.home')])>首页</a>
            <a href="{{ route('storefront.catalog') }}" @class(['is-active' => request()->routeIs('storefront.catalog') || request()->routeIs('storefront.products.show')])>商品目录</a>
            <a href="{{ route('storefront.plan.index') }}" @class(['is-active' => request()->routeIs('storefront.plan.*')])>意向清单</a>
        </nav>

        <form class="storefront-search" method="get" action="{{ route('storefront.catalog') }}">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="搜索商品名、SKU 或经营卖点">
            <button type="submit">搜索</button>
        </form>

        <div class="storefront-actions">
            <a class="plan-badge" href="{{ route('storefront.plan.index') }}">
                <span>已选 {{ $planSummary['total_quantity'] }} 件</span>
                <strong>{{ $planSummary['line_count'] }} 个条目</strong>
            </a>

            @auth
                <a class="secondary-button" href="{{ route('admin.dashboard') }}">进入后台</a>
            @else
                <a class="secondary-button" href="{{ route('login') }}">后台登录</a>
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
            <strong>Designed for browsing, comparison, and action.</strong>
            <p>这是一个中性开源样板，用统一数据模型串起公开商品前台、意向清单与后台运营，不映射任何具体公司或业务主体。</p>
        </div>

        <div class="footer-links">
            <a href="{{ route('storefront.catalog') }}">浏览商品目录</a>
            <a href="{{ route('storefront.plan.index') }}">查看意向清单</a>
            <a href="{{ route('login') }}">登录后台</a>
        </div>
    </footer>
</body>
</html>
