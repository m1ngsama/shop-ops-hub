#!/bin/sh
set -eu

php artisan key:generate --force
php artisan migrate:fresh --seed --force
php artisan route:cache
php artisan view:cache
php artisan config:clear
php artisan dev:check
vendor/bin/phpunit tests/Feature/StorefrontTest.php
npm run build
