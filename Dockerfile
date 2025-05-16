# 1) STAGE NODE: instalar deps JS e compilar assets
FROM node:18-alpine AS node-build

WORKDIR /app

# Cache de deps JS
COPY package.json package-lock.json ./
RUN npm ci

# Copia e builda o Vite
COPY . .
RUN npm run build


# 2) STAGE PHP + NGINX
FROM webdevops/php-nginx:8.2

# Documento raiz do Nginx/PHP
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

# 2.1) Copia todo o código do Laravel (antes de instalar o Composer)
COPY . .

# 2.2) Garante que as pastas de cache existam e sejam próprias
RUN mkdir -p storage bootstrap/cache \
    && chown -R application:application storage bootstrap/cache

# 2.3) Instala deps PHP + roda scripts (package:discover, etc)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 2.4) Copia somente os assets compilados pelo stage node
COPY --from=node-build /app/public/build public/build

# 2.5) Ajusta permissões finais
RUN chown -R application:application storage bootstrap/cache public/build

# 2.6) Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080
CMD ["docker-entrypoint.sh"]