#!/usr/bin/env sh
set -e

APP_DIR="${APP_DIR:-/opt/shop-ops-hub}"
REPO_URL="${REPO_URL:-https://github.com/m1ngsama/shop-ops-hub.git}"

if [ ! -d "$APP_DIR/.git" ]; then
    git clone "$REPO_URL" "$APP_DIR"
fi

cd "$APP_DIR"

if [ ! -f .env.production ]; then
    cp deploy/production.env.example .env.production
    echo "Created .env.production from template. Update passwords and APP_KEY before first launch."
fi

docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production exec app php artisan migrate --force --seed
