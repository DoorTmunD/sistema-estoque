#!/usr/bin/env bash
set -e

# --------------------------------------------------------------------------
# 1. Prepara .env
# --------------------------------------------------------------------------
if [ ! -f .env ]; then
    cp .env.example .env
fi

# força PostgreSQL
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env

# se estiver no Render, limpa host/porta/credenciais (virão de envVars)
if [[ "$RENDER" == "true" ]]; then
    sed -i '/^DB_HOST=/d;/^DB_PORT=/d;/^DB_DATABASE=/d;/^DB_USERNAME=/d;/^DB_PASSWORD=/d' .env
    sed -i "s~^APP_URL=.*~APP_URL=https://$RENDER_EXTERNAL_HOSTNAME~" .env
fi

# --------------------------------------------------------------------------
# 2. Gera chave, migra e seed
# --------------------------------------------------------------------------
if [[ -z "$APP_KEY" || "$APP_KEY" == base64:* ]]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force

# >>> executa agora, com storage/config completos
php artisan package:discover --ansi

# --------------------------------------------------------------------------
# 3. Limpa e recompila caches
# --------------------------------------------------------------------------
php artisan optimize:clear
php artisan config:cache route:cache view:cache    # warm-up

# --------------------------------------------------------------------------
# 4. Inicia serviços supervisord (nginx + php-fpm)
# --------------------------------------------------------------------------
exec /usr/bin/supervisord -n