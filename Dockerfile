############################
# 1) STAGE: BUILD ASSETS   #
############################
FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-progress

COPY vite.config.js .
COPY resources resources
RUN npm run build          # gera public/build/*

############################
# 2) STAGE: COMPOSER       #
############################
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

############################
# 3) STAGE: RUNTIME        #
############################
FROM webdevops/php-nginx:8.2-alpine

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# código-fonte
COPY . .

# vendor + assets dos stages anteriores
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# permissões para nginx/php-fpm (usuário "application")
RUN chown -R application:application storage bootstrap/cache public/build

# entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh


# porta que o Render mapeia
EXPOSE 80                 
CMD ["docker-entrypoint.sh"]