#!/usr/bin/env bash
set -e

# --- prepara .env ----------------------------------------------------------
if [ ! -f .env ]; then
    cp .env.example .env
fi

# força conexão PostgreSQL
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env

# se estivermos no Render, remove host/porta/banco/usuário/senha
if [[ "$RENDER" == "true" ]]; then
    sed -i '/^DB_HOST=/d;/^DB_PORT=/d;/^DB_DATABASE=/d;/^DB_USERNAME=/d;/^DB_PASSWORD=/d' .env

    # sobescreve APP_URL para o hostname público
    sed -i "s~^APP_URL=.*~APP_URL=https://$RENDER_EXTERNAL_HOSTNAME~" .env
fi

# --- gera chave, migra, seed ----------------------------------------------
if [[ -z "$APP_KEY" || "$APP_KEY" == base64:* ]]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force

# --- limpa caches ----------------------------------------------------------
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# --- inicia serviços -------------------------------------------------------
exec /usr/bin/supervisord -n