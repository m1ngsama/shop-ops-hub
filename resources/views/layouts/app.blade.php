<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription ?? '商运后台 — 商品、渠道、订单与经营数据的统一管理平台。' }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $title ?? '商运后台' }}</title>
    @include('partials.style-entry')
</head>
<body class="console-body">
    <div class="console-utility-bar">
        <span>Operations System</span>
        <span>以更清晰的层级组织商品、渠道、订单、同步与经营判断</span>
    </div>

    <header class="console-topbar">
        <div class="brand-group">
            <span class="brand-badge">Shop Ops Hub</span>
            <a class="brand-mark" href="{{ route('admin.dashboard') }}">Operations Console</a>
            <span class="brand-subtitle">面向商品、履约、渠道与经营判断的统一控制台</span>
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
            <div class="sidebar-intro">
                <p>Console Navigation</p>
                <strong>把风险、执行与增长信号收进同一视图。</strong>
            </div>

            <nav class="side-nav">
                <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>总览</a>
                <a href="{{ route('admin.insights') }}" @class(['is-active' => request()->routeIs('admin.insights')])>可视化</a>
                <a href="{{ route('admin.products.index') }}" @class(['is-active' => request()->routeIs('admin.products.*')])>商品</a>
                <a href="{{ route('admin.channels.index') }}" @class(['is-active' => request()->routeIs('admin.channels.*')])>渠道</a>
                <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>订单</a>
                @if (auth()->user()?->canManageOperations())
                    <a href="{{ route('admin.audit.index') }}" @class(['is-active' => request()->routeIs('admin.audit.*')])>审计</a>
                @endif
            </nav>

            <div class="sidebar-card">
                <span class="sidebar-label">当前账号</span>
                <strong>{{ auth()->user()?->name }}</strong>
                <p>{{ auth()->user()?->email }}</p>
                <span class="status-chip tone-info sidebar-role">{{ auth()->user()?->roleLabel() }}</span>
            </div>

            <div class="sidebar-card">
                <span class="sidebar-label">系统能力</span>
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

            <section class="page-header-band">
                <article>
                    <span>响应节奏</span>
                    <strong>先处理风险，再放大增长</strong>
                </article>
                <article>
                    <span>信息结构</span>
                    <strong>同屏组织商品、渠道、订单与同步</strong>
                </article>
                <article>
                    <span>当前角色</span>
                    <strong>{{ auth()->user()?->roleLabel() ?? '访客' }}</strong>
                </article>
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
