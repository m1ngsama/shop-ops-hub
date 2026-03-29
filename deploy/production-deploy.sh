#!/usr/bin/env sh
set -eu

APP_DIR="${APP_DIR:-/opt/shop-ops-hub}"
BRANCH="${BRANCH:-main}"
ENV_FILE="${ENV_FILE:-.env.production}"
SKIP_GIT_SYNC="${SKIP_GIT_SYNC:-0}"

if [ ! -d "$APP_DIR/.git" ]; then
    echo "Missing git repository at $APP_DIR" >&2
    exit 1
fi

cd "$APP_DIR"

if [ ! -f "$ENV_FILE" ]; then
    echo "Missing production env file: $APP_DIR/$ENV_FILE" >&2
    exit 1
fi

if [ "$SKIP_GIT_SYNC" != "1" ]; then
    git fetch origin "$BRANCH"
    git checkout "$BRANCH"
    git pull --ff-only origin "$BRANCH"
fi

docker compose --env-file "$ENV_FILE" up -d --build
docker compose --env-file "$ENV_FILE" restart web
docker compose --env-file "$ENV_FILE" exec -T app php artisan migrate --force
docker compose --env-file "$ENV_FILE" exec -T app php artisan config:cache
docker compose --env-file "$ENV_FILE" exec -T app php artisan route:cache
docker compose --env-file "$ENV_FILE" exec -T app php artisan view:cache
docker compose --env-file "$ENV_FILE" exec -T app php artisan ops:check
docker compose --env-file "$ENV_FILE" ps
