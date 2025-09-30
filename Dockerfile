#######################################################################
# 1) STAGE: BUILD ASSETS (Node + Vite)
#######################################################################
FROM node:20-alpine AS assets
WORKDIR /app

# Instala deps do frontend
COPY package.json package-lock.json ./
RUN npm ci --no-progress

# Vite/Tailwind configs + fontes do front
COPY vite.config.js ./
COPY tailwind.config.js postcss.config.js ./
COPY resources ./resources

# 🔨 Builda para public/build com manifest na raiz (public/build/manifest.json)
# (garanta que seu vite.config.js está com:
#  build.outDir='public/build', build.manifest=true, build.manifestDir='.')
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

# 🔧 Extensões necessárias para Postgres no Alpine
RUN apk add --no-cache postgresql-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo pdo_pgsql \
 && apk del $PHPIZE_DEPS

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# Código + vendors + assets prontos
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Permissões para cache/logs/sessions
RUN mkdir -p storage/framework/{cache,data,sessions,views} storage/logs bootstrap/cache \
 && chown -R application:application storage bootstrap/cache public/build \
 && chmod -R ug+rwX storage bootstrap/cache

# Entrypoint Laravel (gera APP_KEY, migra e seed, ajusta APP_URL/ASSET_URL)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["docker-entrypoint.sh"]
