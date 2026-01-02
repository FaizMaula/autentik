FROM php:8.2-fpm

# Install system dependencies + Node.js & NPM
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev nginx \
    curl nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy package.json & package-lock.json first (for caching npm install)
COPY package*.json ./

# Install JS dependencies & build assets
RUN npm install
RUN npm run build

# Copy Laravel app
COPY . .

# Install PHP dependencies & clear cache
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Copy Nginx config
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# Expose port
EXPOSE 8080

# Start PHP-FPM + Nginx
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
