#######################################################################
# 1) STAGE: BUILD ASSETS (Node + Vite)
#######################################################################
FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-progress

COPY vite.config.js ./
COPY resources ./resources
RUN npm run build                 # → public/build/*


#######################################################################
# 2) STAGE: COMPOSER (instala vendor, SEM scripts)
#######################################################################
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev --no-interaction --no-progress \
    --prefer-dist --optimize-autoloader --no-scripts


#######################################################################
# 3) STAGE: RUNTIME (PHP-FPM + Nginx)
#######################################################################
FROM webdevops/php-nginx:8.2-alpine

# Garante extensões necessárias para Postgres
RUN docker-php-ext-install pdo pdo_pgsql

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# 3.1  Copia código-fonte completo
COPY . .

# 3.2  Vendor + assets vindos dos stages anteriores
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# 3.3  Permissões para php-fpm/nginx (usuário "application")
RUN mkdir -p storage/framework/{cache,data,sessions,views} storage/logs bootstrap/cache \
 && chown -R application:application storage bootstrap/cache public/build

# 3.4  Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["docker-entrypoint.sh"]
