#!/usr/bin/env bash
set -e

# --- prepara .env ----------------------------------------------------------
if [ ! -f .env ]; then
    cp .env.example .env
fi

# força conexão PostgreSQL
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env

# se estivermos no Render, remova host/porta/banco/usuário/senha
if [[ "$RENDER" == "true" ]]; then
    sed -i '/^DB_HOST=/d;/^DB_PORT=/d;/^DB_DATABASE=/d;/^DB_USERNAME=/d;/^DB_PASSWORD=/d' .env
fi

# --- gera chave, migra, seed ----------------------------------------------
if [[ -z "$APP_KEY" || "$APP_KEY" == base64:* ]]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force

# --- limpa todos os caches -----------------------------------------------
# Isso limpa config, route, view e otimizações para refletir arquivos novos
php artisan optimize:clear

# --- inicia serviços -------------------------------------------------------
exec /usr/bin/supervisord -n