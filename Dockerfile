# 1) STAGE NODE: instalar deps JS e compilar assets
FROM node:18-alpine AS node-build

WORKDIR /app

# Copia apenas package.json + lock para cache de camada
COPY package.json package-lock.json ./

# Instala deps JS
RUN npm ci

# Copia todo o código e executa o build do Vite
COPY . .
RUN npm run build


# 2) STAGE PHP + NGINX
FROM webdevops/php-nginx:8.2

# Define a pasta pública do Laravel
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

# 2.1) Copia apenas composer.json + lock e instala deps PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 2.2) Copia os assets gerados pelo Vite
COPY --from=node-build /app/public/build public/build

# 2.3) Copia o restante do código do Laravel
COPY . .

# Ajusta permissões (incluindo a pasta de assets)
RUN chown -R application:application storage bootstrap/cache public/build

# Copia e deixa executável o entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Porta padrão do nginx neste container
EXPOSE 8080

# Inicia pelo entrypoint
CMD ["docker-entrypoint.sh"]