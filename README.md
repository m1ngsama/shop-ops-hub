# Shop Ops Hub

Laravel application for cross-border commerce operations.

## Why this product exists

The target business profile is a cross-border B2C company selling large catalogs through Amazon and similar marketplaces. Public company copy on `yswg.com.cn` describes:

- Cross-border B2C operations.
- Product lines spanning beauty, home, apparel, accessories, and jewelry.
- Internal product development and sales management systems.
- Marketplace-driven global sales, especially through Amazon.

This repository turns that profile into a working demo product that matches the preferred engineering stack from the job description:

- PHP 8.4
- Laravel 13
- Composer
- MySQL-ready schema design
- Redis-friendly caching
- Marketplace adapter layer for Amazon/Walmart style API sync
- Docker-based deployment

## What the app does

Shop Ops Hub is a lightweight operations cockpit for marketplace teams:

- Product master with supplier, target pricing, fulfillment fee, and safety stock.
- Marketplace listings across Amazon US, Walmart US, and TikTok Shop US.
- Inventory batches with replenishment alerts.
- Order ledger with revenue, ad spend, channel fee, and gross profit.
- Dashboard metrics cached through Laravel cache.
- Mock sync endpoints that simulate marketplace deltas through service adapters.

## Main routes

- `/` dashboard
- `/products` product master
- `/products/{id}` SKU detail
- `/channels` channel adapters and manual sync
- `/orders` order ledger

## API routes

- `GET /api/dashboard/metrics`
- `POST /api/channels/{channel}/sync`

## Local development

The repository can be initialized and tested without a host PHP install by using Docker:

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan migrate:fresh --seed
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan test
```

The checked-in `.env` uses SQLite for low-friction local verification. Production uses MySQL and Redis through `deploy/production.env.example`.

## Production architecture

Production uses Docker Compose with four services:

- `app`: PHP-FPM Laravel container
- `web`: Nginx container serving the Laravel public directory
- `mysql`: MySQL 8.4
- `redis`: Redis 7

The compose stack listens only on `127.0.0.1:18081`. A host-level Nginx server block proxies `shop.m1ng.space` to that internal port.

## Deployment files

- `compose.yaml`
- `Dockerfile`
- `docker/entrypoint.sh`
- `docker/nginx/default.conf`
- `docker/php/conf.d/opcache.ini`
- `deploy/production.env.example`
- `deploy/shop.m1ng.space.conf`
- `deploy/oss-bootstrap.sh`

## Quick deploy outline

```bash
cp deploy/production.env.example .env.production
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production exec app php artisan migrate --force --seed
```

On the target host, install the provided host Nginx config and provision a certificate for `shop.m1ng.space`.
