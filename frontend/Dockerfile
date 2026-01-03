FROM php:8.2-fpm

# 1. Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev nginx curl \
    nodejs npm \
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

# 7. FIX PERMISSIONS & DIRECTORY (Solusi Error "Unable to create directory")
# Membuat folder certificates secara eksplisit
RUN mkdir -p /var/www/storage/app/public/certificates \
    && mkdir -p /var/www/bootstrap/cache

# Mengatur kepemilikan dan izin akses agar bisa ditulis oleh PHP (www-data)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 8. Nginx Configuration
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

EXPOSE 8080

# 9. Start Services
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
