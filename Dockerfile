#######################################################################
# 1) STAGE: BUILD ASSETS (Node + Vite)                                #
#######################################################################
FROM node:20-alpine AS assets

WORKDIR /app

# 1.1  Cache das dependências JS
COPY package.json package-lock.json ./
RUN npm ci --no-progress

# 1.2  Copia só o necessário para o build
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
RUN composer install --no-dev --optimize-autoloader \
      --no-interaction --no-progress


#######################################################################
# 3) STAGE: RUNTIME (PHP-FPM + Nginx)                                 #
#######################################################################
FROM webdevops/php-nginx:8.2-alpine

# Diretório raiz do Nginx/PHP
ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production \
    APP_DEBUG=false

WORKDIR /app

# 3.1  Copia todo o código-fonte
COPY . .

# 3.2  Adiciona vendor e assets gerados nos stages anteriores
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# 3.3  Permissões corretas para storage/cache/assets
RUN chown -R application:application \
        storage bootstrap/cache public/build

# 3.4  Entrypoint (seu script já existente)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

 # porta padrão que o Render mapeia
EXPOSE 80                   
CMD ["docker-entrypoint.sh"]