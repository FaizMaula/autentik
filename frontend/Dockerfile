FROM php:8.2-fpm

# 1. Install system dependencies & Supervisor
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev nginx curl \
    nodejs npm supervisor \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure PHP for Large Uploads (Solusi Error 413)
RUN echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# 3. Setup Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Copy Source Code
COPY . .

# 5. PHP Dependencies & Optimization
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# 6. Build Frontend Assets (Solusi Error Vite/localhost)
RUN rm -f public/hot && rm -rf public/build
RUN npm install && npm run build

# 7. FIX PERMISSIONS & DIRECTORY
RUN mkdir -p /var/www/storage/app/public/certificates \
    && mkdir -p /var/www/bootstrap/cache \
    && mkdir -p /var/log/supervisor

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 8. Nginx & Supervisor Configuration
COPY ./docker/nginx.conf /etc/nginx/sites-available/default
COPY ./docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

# 9. Start Application with Supervisor
# Menjalankan storage:link sebelum memulai supervisor
CMD ["sh", "-c", "php artisan storage:link && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
