FROM php:8.2-fpm

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    nginx curl supervisor \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# 2. Node.js (Layer terpisah agar tidak di-rebuild terus)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. PHP configuration
RUN echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# 4. Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 5. Optimasi Node Dependencies (Caching)
# Copy hanya package file dulu agar 'npm install' tidak jalan ulang setiap ada perubahan kode PHP
COPY package.json package-lock.json ./
RUN npm install

# 6. Optimasi PHP Dependencies (Caching)
# Tambahkan --no-scripts agar tidak mencari file 'artisan' yang belum di-copy
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# 7. Copy Full Project
COPY . .

# 8. Jalankan Script yang tertunda tadi setelah artisan tersedia
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# 9. Build Frontend Assets
RUN rm -f public/hot && rm -rf public/build \
    && npm run build

# 10. File Management & Permissions
# Pastikan folder ada dan dimiliki oleh www-data
RUN mkdir -p storage/app/public/certificates \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
             /var/log/supervisor \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# 11. Configuration Files
# Pastikan path ini sesuai dengan letak file config di repo Anda
COPY ./docker/nginx.conf /etc/nginx/sites-available/default
# Jika nginx menggunakan sites-enabled, buat symlink (tergantung isi config nginx Anda)
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

COPY ./docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# 12. Environment Setup
RUN php -r "file_exists('.env') || copy('.env.example', '.env');"
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

EXPOSE 8080

# 13. Execution
# storage:link dipindah ke CMD agar berjalan saat container dinyalakan (runtime)
CMD ["sh", "-c", "php artisan storage:link && supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
