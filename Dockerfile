# Imagem base com PHP 8.2 + Nginx
FROM webdevops/php-nginx:8.2-alpine

WORKDIR /app
COPY . .

# Instala dependências e prepara o Laravel
RUN apk add --no-cache nodejs npm && \
    composer install --no-dev --optimize-autoloader && \
    npm ci && \
    npm run build && \
    php artisan key:generate && \
    php artisan migrate --force

EXPOSE 8080