#!/usr/bin/env bash
set -e

# --------------------------------------------------------------------------
# 1. Prepara .env
# --------------------------------------------------------------------------
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Força pgsql no container de produção
# (para desenvolvimento local, você continua com sqlite no .env local)
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env || true

# Se o Render expuser o hostname público, ajusta URLs
if [ -n "$RENDER_EXTERNAL_HOSTNAME" ]; then
  sed -i "s~^APP_URL=.*~APP_URL=https://$RENDER_EXTERNAL_HOSTNAME~" .env || true
  sed -i "s~^ASSET_URL=.*~ASSET_URL=https://$RENDER_EXTERNAL_HOSTNAME~" .env || true
fi

# --------------------------------------------------------------------------
# 2. APP_KEY (gera somente se estiver vazio no .env)
# --------------------------------------------------------------------------
if ! grep -qE '^APP_KEY=.+$' .env || grep -qE '^APP_KEY=\s*$' .env; then
  php artisan key:generate --force || true
fi

# --------------------------------------------------------------------------
# 3. Migrate/Seed
# --------------------------------------------------------------------------
php artisan migrate --force
php artisan db:seed --force || true

# Descobre pacotes agora que storage/config existem
php artisan package:discover --ansi || true

# --------------------------------------------------------------------------
# 4. Caches
# --------------------------------------------------------------------------
php artisan optimize:clear || true
php artisan optimize || true
php artisan view:cache || true

# --------------------------------------------------------------------------
# 5. Inicia Nginx + PHP-FPM via supervisord
# --------------------------------------------------------------------------
exec /usr/bin/supervisord -n
