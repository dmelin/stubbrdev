FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy the rest of the application
COPY . .

# Run post-install scripts
RUN composer dump-autoload --optimize

# Laravel-specific setup
RUN mkdir -p database
RUN touch database/database.sqlite

# Ensure storage and bootstrap/cache are writable
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Cache config at startup when env vars are present, then start server
CMD ["sh", "-c", "php artisan config:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"]