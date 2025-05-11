#!/usr/bin/env bash
set -e

# Cria .env se não existir
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Gera APP_KEY se faltar
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --force
fi

# Migrations
php artisan migrate --force

# Inicia PHP-FPM + Nginx
exec /usr/bin/supervisord -n -c /opt/docker/etc/supervisord.conf