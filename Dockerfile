# 1) STAGE NODE: instalar deps JS e compilar assets
FROM node:18-alpine AS node-build

WORKDIR /app

# Copia apenas package.json + package-lock.json para cache de camada
COPY package.json package-lock.json ./

# Instala deps JS
RUN npm ci

# Copia o restante do código e executa o build do Vite
COPY . .
RUN npm run build


# 2) STAGE PHP + NGINX
FROM webdevops/php-nginx:8.2

# Define document root do Nginx/PHP
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

# 2.1) Copia apenas composer.json + composer.lock e instala deps PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 2.2) Copia o código do Laravel (não inclui node_modules nem public/build)
COPY . .

# 2.3) Copia os assets gerados pelo Vite
COPY --from=node-build /app/public/build public/build

# Ajusta permissões para storage, cache e assets
RUN chown -R application:application storage bootstrap/cache public/build

# Copia e torna executável o entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expõe a porta padrão do nginx
EXPOSE 8080

# Inicia o container
CMD ["docker-entrypoint.sh"]