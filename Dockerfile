FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Laravel-specific setup
RUN touch /var/www/html/database/database.sqlite
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan migrate --force

# Ensure storage and bootstrap/cache are writable
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]