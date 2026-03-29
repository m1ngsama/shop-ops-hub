<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '前台选品站' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
    <header class="storefront-header">
        <div class="storefront-brand">
            <a href="{{ route('storefront.home') }}">选品前台</a>
            <span>公开浏览与意向协同</span>
        </div>

        <nav class="storefront-nav">
            <a href="{{ route('storefront.home') }}" @class(['is-active' => request()->routeIs('storefront.home')])>首页</a>
            <a href="{{ route('storefront.catalog') }}" @class(['is-active' => request()->routeIs('storefront.catalog') || request()->routeIs('storefront.products.show')])>商品目录</a>
            <a href="{{ route('storefront.plan.index') }}" @class(['is-active' => request()->routeIs('storefront.plan.*')])>意向清单</a>
        </nav>

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
            <strong>中性样板项目</strong>
            <p>仅用于展示真实电商场景下的前台与后台协同，不映射任何具体公司或业务主体。</p>
        </div>

        <div class="footer-links">
            <a href="{{ route('storefront.catalog') }}">浏览商品目录</a>
            <a href="{{ route('storefront.plan.index') }}">查看意向清单</a>
            <a href="{{ route('login') }}">登录后台</a>
        </div>
    </footer>
</body>
</html>
