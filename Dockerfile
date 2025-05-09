# PHP 8.2 + Nginx (Debian) — não-Alpine
FROM webdevops/php-nginx:8.2

WORKDIR /app
COPY . .

# Instala Node.js 18 LTS e constrói assets
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get update && \
    apt-get install -y nodejs && \
    composer install --no-dev --optimize-autoloader && \
    npm ci && \
    npm run build && \
    php artisan key:generate && \
    php artisan migrate --force && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 8080