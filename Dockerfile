FROM webdevops/php-nginx:8.2

WORKDIR /app
COPY . .

# 1️⃣ Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2️⃣ Composer (sem scripts, sem progress bar → menos RAM)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 3️⃣ NPM (gera package-lock.json se não existir)
RUN npm install && npm run build

# 4️⃣ Artisan – chave + migrations
RUN php artisan key:generate && php artisan migrate --force

EXPOSE 8080