FROM php:8.2-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    nginx curl supervisor \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Node (modern)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# PHP upload limits
RUN echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# PHP deps
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    && php artisan config:clear || true \
    && php artisan route:clear || true \
    && php artisan view:clear || true

# Frontend build
RUN rm -f public/hot && rm -rf public/build \
    && npm install \
    && npm run build

# Directories
RUN mkdir -p \
    /var/www/storage/app/public/certificates \
    /var/www/bootstrap/cache \
    /var/log/supervisor \
    /var/run

# Permissions (simple & safe)
RUN chown -R root:root /var/www \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache


# Configs
COPY ./docker/nginx.conf /etc/nginx/sites-available/default
COPY ./docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["sh", "-c", "php artisan storage:link && supervisord -n"]
