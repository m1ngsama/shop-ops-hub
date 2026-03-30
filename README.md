# Shop Ops Hub

一个面向真实上线环境的 `Laravel 13 + MySQL + Redis + Docker` 零售运营系统，覆盖公开前台、后台运营台、渠道同步、库存、订单、审计与经营可视化。

## 项目边界

- 不映射任何具体公司、雇主或业务主体
- 仓库内不包含生产密钥、生产密码或真实供应数据
- 演示数据仅用于 `local / testing` 环境
- 生产环境不再通过 `db:seed` 写入演示数据

## 主要能力

- 公开前台：商品目录、搜索、详情页、意向清单
- 后台工作台：总览、可视化、商品、渠道、订单、审计
- 同步链路：受保护 API、队列消费、同步记录
- 经营分析：财务走势、渠道盈利、库存覆盖、上架准备度
- 安全边界：后台登录、接口令牌、操作审计、基础安全响应头

## 技术栈

- PHP 8.4
- Laravel 13
- MySQL 8.4
- Redis 7
- Docker Compose
- Nginx

## 本地开发

最完整的本地开发方式是直接使用 `compose.local.yaml`，这样不需要在宿主机安装 PHP 或 Composer。

1. 初始化环境文件

```bash
cp .env.example .env
```

建议把 `.env` 改成以下本地容器配置：

```bash
APP_URL=http://127.0.0.1:18080
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=shop_ops_hub_local
DB_USERNAME=shop_ops_hub
DB_PASSWORD=shop_ops_hub
DB_ROOT_PASSWORD=shop_ops_hub_root
REDIS_HOST=redis
REDIS_PORT=6379
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

2. 启动本地开发环境

```bash
docker compose -f compose.local.yaml up -d
```

3. 首次初始化数据库与密钥

```bash
docker compose -f compose.local.yaml exec app php artisan key:generate
docker compose -f compose.local.yaml exec app php artisan migrate:fresh --seed
```

4. 本地访问

```text
应用: http://127.0.0.1:18080
Vite: http://127.0.0.1:15173
MySQL: 127.0.0.1:13306
Redis: 127.0.0.1:16379
```

5. 运行一轮本地验证

```bash
sh scripts/dev-check.sh
```

本地健康检查也可以单独运行：

```bash
php artisan dev:check
```

## 轻量命令

```bash
cp .env.example .env
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan key:generate
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan migrate:fresh --seed
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan test
```

## 运维命令

初始化或校准管理员，不触碰业务数据：

```bash
php artisan ops:bootstrap-admin
php artisan ops:bootstrap-admin --email=contact@example.com --password='strong-password' --rotate-password
```

执行生产自检：

```bash
php artisan ops:check
```

## 生产部署

1. 复制环境文件并填写真实值

```bash
cp deploy/production.env.example .env.production
```

2. 构建并启动服务

```bash
docker compose --env-file .env.production up -d --build
```

3. 执行迁移

```bash
docker compose --env-file .env.production exec app php artisan migrate --force
```

4. 初始化管理员

```bash
docker compose --env-file .env.production exec app php artisan ops:bootstrap-admin
```

5. 运行生产自检

```bash
docker compose --env-file .env.production exec app php artisan ops:check
```

## 服务器更新

如果仓库已经在 VPS 上，推荐统一使用部署脚本，而不是手写多条命令：

```bash
APP_DIR=/opt/shop-ops-hub BRANCH=main sh deploy/production-deploy.sh
```

如果是像首次接管服务器、临时 `scp` 同步文件这类场景，且服务器工作树暂时不是干净状态，可以跳过 git 拉取：

```bash
APP_DIR=/opt/shop-ops-hub BRANCH=main SKIP_GIT_SYNC=1 sh deploy/production-deploy.sh
```

脚本会执行以下动作：

- 拉取指定分支最新代码
- `docker compose up -d --build`
- `docker compose restart web`，避免 PHP-FPM 容器重建后 Nginx 仍指向旧 upstream
- `php artisan migrate --force`
- 缓存配置、路由、视图
- 执行 `php artisan ops:check`

注意：生产部署不会执行 `--seed`，避免演示数据误入线上。

## GitHub Actions 自动部署

仓库已预留 `.github/workflows/deploy-production.yml`。在 GitHub 仓库 Secrets 中配置以下变量后，推送到 `main` 即可自动远程发布：

- `OSS_HOST`: VPS 地址
- `OSS_PORT`: SSH 端口，默认可填 `22`
- `OSS_USER`: SSH 用户，建议单独创建部署用户
- `OSS_SSH_KEY`: 对应私钥内容

工作流会 SSH 到服务器后执行：

```bash
APP_DIR=/opt/shop-ops-hub BRANCH=main sh /opt/shop-ops-hub/deploy/production-deploy.sh
```

## 演示数据说明

- `CommerceOpsSeeder` 仅用于本地和测试
- `DatabaseSeeder` 在生产环境不会写入演示数据
- 如果需要正式导入商品、供应商、库存或订单，应通过受控脚本或后台管理流程完成
