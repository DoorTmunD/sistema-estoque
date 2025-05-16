#!/usr/bin/env bash
set -e

# --------------------------------------------------------------------------
# 1. Prepara .env
# --------------------------------------------------------------------------
if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env

# Se estiver no Render, substitui host público e zera credenciais locais
if [[ "$RENDER" == "true" ]]; then
    sed -i '/^DB_HOST=/d;/^DB_PORT=/d;/^DB_DATABASE=/d;/^DB_USERNAME=/d;/^DB_PASSWORD=/d' .env
    sed -i "s~^APP_URL=.*~APP_URL=https://$RENDER_EXTERNAL_HOSTNAME~" .env
    sed -i "s~^ASSET_URL=.*~ASSET_URL=https://$RENDER_EXTERNAL_HOSTNAME~" .env   # ← NOVO
fi

# --------------------------------------------------------------------------
# 2. Gera chave, migra e seed
# --------------------------------------------------------------------------
if [[ -z "$APP_KEY" || "$APP_KEY" == base64:* ]]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force

# Descobre pacotes agora que storage/config existem
php artisan package:discover --ansi

# --------------------------------------------------------------------------
# 3. Limpa e recompila caches
# --------------------------------------------------------------------------
php artisan optimize:clear      # limpa config/route/view
php artisan optimize            # recompila config + route cache
php artisan view:cache          # pré-compila blades

# --------------------------------------------------------------------------
# 4. Inicia Nginx + PHP-FPM via supervisord
# --------------------------------------------------------------------------
exec /usr/bin/supervisord -n