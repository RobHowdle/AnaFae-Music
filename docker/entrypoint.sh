#!/usr/bin/env sh
set -eu

cd /var/www/html

# Ensure runtime-writable directories are owned by the web user.
# When `storage/` is a named volume, it may be created as root on first run.
chown -R www-data:www-data storage bootstrap/cache || true

mkdir -p \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

if [ "${DB_CONNECTION:-}" = "sqlite" ]; then
  DB_FILE="${DB_DATABASE:-/var/www/html/storage/database.sqlite}"
  mkdir -p "$(dirname "$DB_FILE")"
  touch "$DB_FILE"
fi

if [ "${AUTO_MIGRATE:-1}" = "1" ]; then
  php artisan migrate --force --no-interaction || true
fi

php artisan config:clear || true

exec "$@"
