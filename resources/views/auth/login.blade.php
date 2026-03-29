<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 | 商运后台</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-body">
    <div class="login-shell">
        <section class="login-hero">
            <p class="page-kicker">内部系统</p>
            <h1>把商品、库存、渠道和订单放到同一个控制台里处理。</h1>
            <p class="page-copy">
                这个项目是中性的真实需求演示，不映射任何具体雇主信息。界面语言默认中文，
                入口仅保留登录页，后台功能通过会话与接口令牌保护。
            </p>

            <div class="hero-matrix">
                <article>
                    <strong>任务优先</strong>
                    <p>以筛选、表格、推荐动作和批量处理为中心，而不是宣传式落地页。</p>
                </article>
                <article>
                    <strong>权限边界</strong>
                    <p>后台页面需登录访问，接口需管理员会话或 Bearer Token。</p>
                </article>
                <article>
                    <strong>异步执行</strong>
                    <p>渠道同步进入队列，由 worker 消费，避免阻塞浏览器请求。</p>
                </article>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-head">
                <div>
                    <p class="page-kicker">账号登录</p>
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
