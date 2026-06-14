FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libsqlite3-dev libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-install pdo_sqlite zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && cp .env.example .env \
    && touch database/database.sqlite \
    && php artisan key:generate \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan config:cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
