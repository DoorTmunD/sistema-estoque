#!/usr/bin/env bash
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env

if [ -z "$APP_KEY" ] || [[ "$APP_KEY" == "base64:"* ]]; then
    php artisan key:generate --force
fi

php artisan migrate --force

exec /usr/bin/supervisord -n