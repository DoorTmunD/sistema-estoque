FROM webdevops/php-nginx:8.2

WORKDIR /app
COPY . .

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get update && \
    apt-get install -y nodejs && \
    composer install --no-dev --optimize-autoloader && \
    npm install && \
    npm run build && \
    php artisan key:generate && \
    php artisan migrate --force && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 8080