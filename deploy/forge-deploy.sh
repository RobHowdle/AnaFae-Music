#!/usr/bin/env bash
set -euo pipefail

# Intended for Laravel Forge atomic deployment usage.

echo "Deploy started: $(date)"

$CREATE_RELEASE()

cd "$FORGE_RELEASE_DIRECTORY"

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# If using SQLite, the DB file is not committed (gitignored). Ensure it exists.
mkdir -p database
touch database/database.sqlite

$FORGE_PHP artisan optimize:clear
$FORGE_PHP artisan storage:link || true
$FORGE_PHP artisan migrate --force
$FORGE_PHP artisan db:seed --class=HomePageDefaultsSeeder --force

npm ci || npm install
npm run build

$FORGE_PHP artisan optimize

$ACTIVATE_RELEASE()

$RESTART_QUEUES()

echo "Deploy finished: $(date)"
