# 商运后台

一个基于 `Laravel 13 + MySQL + Redis + Docker` 的内部电商运营后台示例项目。

## 项目定位

本项目是一个中性的现实需求演示，不对应任何具体公司或业务主体。它模拟的是常见的多渠道零售运营场景：

- 商品主数据管理
- 多渠道刊登与同步
- 库存批次与补货预警
- 订单台账与毛利监控
- 受保护的后台登录与接口令牌

## 功能结构

- `/login` 登录页
- `/admin` 运营总览
- `/admin/products` 商品中心
- `/admin/products/{id}` 商品详情
- `/admin/channels` 渠道中心
- `/admin/orders` 订单中心

## API

- `GET /api/dashboard/metrics`
- `POST /api/channels/{channel}/sync`

这两个接口都需要以下任一凭证：

- 已登录的后台管理员会话
- 与 `SHOP_OPS_API_TOKEN` 匹配的 Bearer Token

## 技术栈

- PHP 8.4
- Laravel 13
- Composer
- MySQL
- Redis
- Docker Compose
- Laravel Queue Worker

## 本地验证

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan migrate:fresh --seed
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan test
```

## 部署说明

生产环境包含 5 个服务：

- `app`: PHP-FPM
- `worker`: 队列消费者
- `web`: Nginx
- `mysql`: MySQL 8.4
- `redis`: Redis 7

快速部署：

```bash
cp deploy/production.env.example .env.production
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production exec app php artisan migrate --force --seed
```

建议的生产配置：

- `APP_FORCE_HTTPS=true`
- `QUEUE_CONNECTION=redis`
- `APP_LOCALE=zh_CN`
- 设置独立的 `SHOP_OPS_ADMIN_PASSWORD`
- 设置独立的 `SHOP_OPS_API_TOKEN`
