#!/usr/bin/env bash
set -euo pipefail

# Intended for Laravel Forge deploy script usage.
# Forge typically runs deployments from the site directory already.

echo "Deploy started: $(date)"

php artisan view:clear || true
php artisan down --render=errors.503 || true

composer install --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force

npm ci
npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up

echo "Deploy finished: $(date)"
