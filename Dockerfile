FROM webdevops/php-nginx:8.2

# Aponta o Nginx/PHP-FPM para a pasta public do Laravel
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app
COPY . .

# 1) Instala Node.js 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# 3) Assets front-end
RUN npm install && npm run build

# 4) Ajusta permissões para storage e cache
RUN chown -R application:application storage bootstrap/cache

# 5) Copia entrypoint e dá permissão
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 6) Comando padrão
CMD ["docker-entrypoint.sh"]

EXPOSE 8080