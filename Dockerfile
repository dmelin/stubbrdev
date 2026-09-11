# syntax=docker/dockerfile:1

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
RUN npm run build


FROM php:8.3-apache AS app

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=8080

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl unzip libzip-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql bcmath opcache pcntl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite headers \
    && sed -ri 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-app.ini"
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && chmod -R a+rX . \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && rm -f public/hot

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -fsS "http://localhost:${PORT}/up" >/dev/null || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]
