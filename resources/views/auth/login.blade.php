<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 | 商运后台</title>
    @include('partials.style-entry')
</head>
<body class="login-body">
    <div class="login-shell">
        <section class="login-hero">
            <span class="brand-badge">Shop Ops Hub</span>
            <p class="page-kicker">Operations Console</p>
            <h1>把商品、渠道、库存与订单，组织成一套真正可用的运营工作台。</h1>
            <p class="page-copy">
                这次重构不再把后台做成拥挤的信息墙，而是参考 Apple Developer 的视觉节奏，
                用更清晰的区块、标题和层次承接商品管理、履约执行、渠道同步与经营分析。
            </p>

            <div class="overview-strip overview-strip-3 compact-overview-strip">
                <article>
                    <span>统一视图</span>
                    <strong>商品 + 渠道 + 订单</strong>
                    <p>减少在多个工具间来回切换。</p>
                </article>
                <article>
                    <span>决策重点</span>
                    <strong>先风险 后增长</strong>
                    <p>库存、同步、履约异常优先暴露。</p>
                </article>
                <article>
                    <span>体验目标</span>
                    <strong>移动与桌面</strong>
                    <p>在保持信息密度的同时，保证阅读和扫描效率。</p>
                </article>
            </div>

            <div class="hero-matrix">
                <article>
                    <strong>前台选品</strong>
                    <p>公开站点负责浏览、比较与意向汇总，不再只是登录前占位页。</p>
                </article>
                <article>
                    <strong>权限边界</strong>
                    <p>后台页面需登录访问，接口需管理员会话或 Bearer Token。</p>
                </article>
                <article>
                    <strong>经营可视化</strong>
                    <p>控制台把订单结构、财务走势和库存风险集中到同一驾驶舱里。</p>
                </article>
            </div>

            <div class="card-actions login-links">
                <a class="secondary-button" href="{{ route('storefront.home') }}">打开前台站点</a>
                <a class="secondary-button" href="{{ route('storefront.catalog') }}">浏览商品目录</a>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-head">
                <div>
                    <p class="page-kicker">Sign In</p>
                    <h2>进入后台</h2>
                </div>
            </div>

            <form method="post" action="{{ route('login.store') }}" class="form-stack">
                @csrf

                <label class="field">
                    <span>邮箱</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </label>

                <label class="field">
                    <span>密码</span>
                    <input type="password" name="password" required autocomplete="current-password">
                </label>

                <label class="checkbox-line">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>记住当前设备</span>
                </label>

                @if ($errors->any())
                    <div class="message-banner error-banner">{{ $errors->first() }}</div>
                @endif

                <button type="submit" class="primary-button full-width">登录后台</button>
            </form>
        </section>
    </div>
</body>
</html>
