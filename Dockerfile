# 1) STAGE NODE: instalar deps JS e compilar assets
FROM node:18-alpine AS node-build

WORKDIR /app

# Copia apenas package.json e package-lock.json para cache de camada
COPY package.json package-lock.json ./

# Instala dependências JS
RUN npm ci

# Copia todo o restante do código e executa o build do Vite
COPY . .
RUN npm run build


# 2) STAGE PHP + NGINX
FROM webdevops/php-nginx:8.2

# Define a pasta pública do Nginx/PHP
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

# 2.1) Copia apenas os arquivos necessários antes de rodar o Composer
COPY composer.json composer.lock ./
COPY artisan ./
COPY bootstrap bootstrap

# Instala dependências PHP e executa os scripts de pós-autoload
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 2.2) Copia o restante do código do Laravel
COPY . .

# 2.3) Copia os assets gerados pelo Vite
COPY --from=node-build /app/public/build public/build

# Ajusta permissões para storage, cache e assets compilados
RUN chown -R application:application storage bootstrap/cache public/build

# Copia e torna executável o entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expõe a porta padrão do nginx
EXPOSE 8080

# Comando padrão para iniciar o container
CMD ["docker-entrypoint.sh"]