FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev nginx curl \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Laravel + frontend source
COPY . .

# PHP dependencies
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# JS dependencies & build assets
RUN npm install
RUN npm run build

# Set permission
RUN chown -R www-data:www-data /var/www

# Copy Nginx config
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

EXPOSE 8080

# Start PHP-FPM + Nginx
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
