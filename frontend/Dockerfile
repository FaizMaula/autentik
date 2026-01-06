FROM php:8.2-fpm

# -----------------------------------
# Install system dependencies
# -----------------------------------
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    nginx curl supervisor \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------
# Node.js (modern)
# -----------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# -----------------------------------
# PHP limits
# -----------------------------------
RUN echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# -----------------------------------
# Composer
# -----------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -----------------------------------
# Set working directory
# -----------------------------------
WORKDIR /var/www

# -----------------------------------
# Copy composer files first (caching)
# -----------------------------------
COPY composer.json composer.lock ./

# Install PHP dependencies first (before full copy)
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# -----------------------------------
# Copy full project
# -----------------------------------
COPY . .

# -----------------------------------
# Ensure .env exists
# -----------------------------------
RUN php -r "file_exists('.env') || copy('.env.example', '.env');"

# -----------------------------------
# Clear Laravel caches
# -----------------------------------
RUN php artisan config:clear || true \
    && php artisan route:clear || true \
    && php artisan view:clear || true

# -----------------------------------
# Build frontend assets
# -----------------------------------
RUN rm -f public/hot && rm -rf public/build \
    && npm install \
    && npm run build

# -----------------------------------
# Create storage & cache directories
# -----------------------------------
RUN mkdir -p storage/app/public/certificates bootstrap/cache /var/log/supervisor /var/run \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# -----------------------------------
# Copy Nginx & Supervisor configs
# -----------------------------------
COPY ./docker/nginx.conf /etc/nginx/sites-available/default
COPY ./docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# -----------------------------------
# Expose port
# -----------------------------------
EXPOSE 8080

# -----------------------------------
# Start container
# -----------------------------------
CMD ["sh", "-c", "php artisan storage:link && supervisord -n"]
