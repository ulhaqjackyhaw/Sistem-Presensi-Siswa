# Stage 1: deps & build
FROM php:8.2-fpm AS builder

ARG APP_ENV=production
LABEL maintainer="you@example.com" version="1.0" env="$APP_ENV"

# install extensions dan bersihkan cache
RUN apt-get update \
    && apt-get install -y \
       git zip unzip libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-install zip pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# copy project dan cache konfigurasi
COPY . .
RUN composer dump-autoload --optimize \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Stage 2: runtime
FROM php:8.2-fpm

# install ekstensi runtime
RUN apt-get update \
    && apt-get install -y \
       libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-install zip pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
COPY --from=builder /var/www /var/www

# buat non-root user untuk keamanan
RUN groupadd -g 1000 appuser \
 && useradd -u 1000 -g appuser -m appuser \
 && chown -R appuser:appuser /var/www
USER appuser

EXPOSE 9000
CMD ["php-fpm"]
