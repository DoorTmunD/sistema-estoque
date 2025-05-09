#!/usr/bin/env bash
set -e

# Gera APP_KEY se ainda não existir
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --force
fi

# Aplica migrations sempre que o contêiner inicia
php artisan migrate --force

# Inicia PHP-FPM + Nginx (já configurados na imagem)
exec /usr/bin/supervisord -n -c /opt/docker/etc/supervisord.conf