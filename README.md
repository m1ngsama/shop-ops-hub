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

## 演示数据说明

- `CommerceOpsSeeder` 仅用于本地和测试
- `DatabaseSeeder` 在生产环境不会写入演示数据
- 如果需要正式导入商品、供应商、库存或订单，应通过受控脚本或后台管理流程完成
