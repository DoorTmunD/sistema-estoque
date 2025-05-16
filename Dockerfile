#######################################################################
# 1) STAGE: BUILD ASSETS (Node + Vite)                                #
#######################################################################
FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-progress

COPY vite.config.js .
COPY resources resources
RUN npm run build                 # gera public/build/*


#######################################################################
# 2) STAGE: COMPOSER (instala vendor)                                 #
#######################################################################
FROM composer:2 AS vendor

WORKDIR /app

# 2.1  Composer precisa do artisan + bootstrap para package:discover
COPY composer.json composer.lock artisan ./
COPY bootstrap ./bootstrap

# 2.2  Cria diretórios de cache/log/views antes de rodar composer
RUN mkdir -p storage/framework/{cache/data,sessions,views} \
           storage/logs bootstrap/cache

# 2.3  Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader \
      --no-interaction --no-progress


#######################################################################
# 3) STAGE: RUNTIME (PHP-FPM + Nginx)                                 #
#######################################################################
FROM webdevops/php-nginx:8.2-alpine

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# 3.1  Copia código-fonte
COPY . .

# 3.2  Copia vendor + assets compilados
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# 3.3  Permissões corretas
RUN chown -R application:application \
        storage bootstrap/cache public/build

# 3.4  Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["docker-entrypoint.sh"]