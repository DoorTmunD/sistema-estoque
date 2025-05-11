#!/usr/bin/env bash
set -e

# --- prepara .env ---
if [ ! -f .env ]; then
    cp .env.example .env
fi
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
sed -i '/^DB_HOST=/d;/^DB_PORT=/d;/^DB_DATABASE=/d;/^DB_USERNAME=/d;/^DB_PASSWORD=/d' .env

# --- gera chave e migra ---
if [[ -z "$APP_KEY" || "$APP_KEY" == base64:* ]]; then
    php artisan key:generate --force
fi
php artisan migrate --force

# --- inicia serviços ---
exec /usr/bin/supervisord -n