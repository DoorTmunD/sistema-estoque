#######################################################################
# 1) STAGE: BUILD ASSETS (Node + Vite)
#######################################################################
FROM node:20-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-progress

COPY vite.config.js ./
# (removidas as cópias opcionais)
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

# Extensões p/ Postgres no Alpine
RUN apk add --no-cache postgresql-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo pdo_pgsql \
 && apk del $PHPIZE_DEPS

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# Código + vendors
COPY . .
COPY --from=vendor /app/vendor ./vendor

# ===== Assets do Vite =====
# Caminho "correto" (public/build gerado pelo vite.config.js)
COPY --from=assets /app/public/build ./public/build
# Fallback: se por qualquer motivo o outDir cair em "dist", copia também
COPY --from=assets /app/dist ./public/build

# Remove "hot" para não tentar HMR em produção
RUN rm -f public/hot || true

# Permissões
RUN mkdir -p storage/framework/{cache,data,sessions,views} storage/logs bootstrap/cache \
 && chown -R application:application storage bootstrap/cache public/build \
 && chmod -R ug+rwX storage bootstrap/cache

# Entrypoint (key, migrate/seed, APP_URL/ASSET_URL, caches)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["docker-entrypoint.sh"]
