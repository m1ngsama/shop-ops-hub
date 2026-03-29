<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '商运后台' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="console-body">
    <header class="console-topbar">
        <div class="brand-group">
            <a class="brand-mark" href="{{ route('admin.dashboard') }}">商运后台</a>
            <span class="brand-subtitle">内部运营控制台</span>
        </div>

        <form class="topbar-search" method="get" action="{{ route('admin.products.index') }}">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="搜索 SKU、商品名或订单线索"
            >
        </form>

        <div class="topbar-tools">
            <a class="topbar-link" href="{{ route('storefront.home') }}" target="_blank" rel="noreferrer">前台站点</a>
            <span class="topbar-pill">{{ app()->environment('production') ? '生产环境' : '开发环境' }}</span>
            <span class="topbar-time">{{ now()->format('m-d H:i') }}</span>
        </div>
    </header>

    <div class="console-shell">
        <aside class="console-sidebar">
            <nav class="side-nav">
                <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>总览</a>
                <a href="{{ route('admin.insights') }}" @class(['is-active' => request()->routeIs('admin.insights')])>可视化</a>
                <a href="{{ route('admin.products.index') }}" @class(['is-active' => request()->routeIs('admin.products.*')])>商品</a>
                <a href="{{ route('admin.channels.index') }}" @class(['is-active' => request()->routeIs('admin.channels.*')])>渠道</a>
                <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>订单</a>
            </nav>

            <div class="sidebar-card">
                <span class="sidebar-label">当前账号</span>
                <strong>{{ auth()->user()?->name }}</strong>
                <p>{{ auth()->user()?->email }}</p>
            </div>

            <div class="sidebar-card">
                <span class="sidebar-label">系统特性</span>
                <ul class="sidebar-list">
                    <li>登录保护</li>
                    <li>接口令牌</li>
                    <li>异步同步</li>
                    <li>补货预警</li>
                    <li>前台选品站</li>
                    <li>经营可视化</li>
                </ul>
            </div>

            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="secondary-button full-width">退出登录</button>
            </form>
        </aside>

        <main class="console-main">
            <section class="page-header">
                <div>
                    <p class="page-kicker">@yield('page_kicker', '运营工作台')</p>
                    <h1>@yield('page_title', '商运后台')</h1>
                    <p class="page-copy">@yield('page_copy', '聚焦商品、库存、渠道与订单执行的内部系统。')</p>
                </div>

                @hasSection('page_actions')
                    <div class="page-actions">
                        @yield('page_actions')
                    </div>
                @endif
            </section>

            @if (session('status'))
                <div class="message-banner success-banner">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="message-banner error-banner">{{ $errors->first() }}</div>
            @endif

            <div class="content-stack">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
