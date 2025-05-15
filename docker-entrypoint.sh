#!/usr/bin/env bash
set -e

# --- prepara .env ----------------------------------------------------------
if [ ! -f .env ]; then
    cp .env.example .env
fi

# força conexão PostgreSQL (sempre)
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env

# se estivermos no Render, remove host/porta/banco/usuário/senha
# para usar as env vars injetadas pelo Render
if [[ "$RENDER" == "true" ]]; then
    sed -i '/^DB_HOST=/d;/^DB_PORT=/d;/^DB_DATABASE=/d;/^DB_USERNAME=/d;/^DB_PASSWORD=/d' .env

    # garante APP_URL em HTTPS no Render
    sed -i "s#^APP_URL=.*#APP_URL=https://$RENDER_EXTERNAL_HOSTNAME#" .env
fi

# --- gera chave, migra, seed ----------------------------------------------
if [[ -z "$APP_KEY" || "$APP_KEY" == base64:* ]]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force

# limpa caches para garantir que o novo APP_URL e manifest sejam usados
php artisan optimize:clear

# --- inicia serviços -------------------------------------------------------
exec /usr/bin/supervisord -n