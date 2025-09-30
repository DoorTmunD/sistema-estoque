#######################################################################
# 1) STAGE: BUILD ASSETS (Node + Vite)
#######################################################################
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-progress
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

#######################################################################
# 2) STAGE: COMPOSER (vendor, sem scripts)
#######################################################################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress \
    --prefer-dist --optimize-autoloader --no-scripts

#######################################################################
# 3) STAGE: RUNTIME (PHP-FPM + Nginx)
#######################################################################
FROM webdevops/php-nginx:8.2-alpine

# 🔧 deps para compilar/ext PDO_PGSQL no Alpine
RUN apk add --no-cache postgresql-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo pdo_pgsql \
 && apk del $PHPIZE_DEPS

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# Código + vendors + assets
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Permissões
RUN mkdir -p storage/framework/{cache,data,sessions,views} storage/logs bootstrap/cache \
 && chown -R application:application storage bootstrap/cache public/build

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["docker-entrypoint.sh"]
