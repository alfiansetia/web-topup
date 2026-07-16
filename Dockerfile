# --- Stage 1: Build Assets & Vendor ---
FROM php:8.3-fpm AS builder

# Install zip & unzip (dibutuhkan Composer)
RUN apt-get update && apt-get install -y unzip git zip && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# 1. Jalankan composer install dulu agar folder /vendor/tightenco/ziggy tercipta
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# 2. Beralih ke Node.js untuk build asset
FROM node:22-alpine AS asset-builder
WORKDIR /app

# Ambil seluruh file dari stage builder (termasuk folder vendor yang baru dibuat)
COPY --from=builder /app /app
RUN npm ci
RUN npm run build

# --- Stage 2: Production App (PHP Server) ---
FROM php:8.3-fpm

# Install sistem dependencies untuk production
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        intl \
        gd \
        bcmath \
        opcache \
        exif \
        pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-enable opcache

WORKDIR /var/www/html

# Ambil source code lengkap + vendor + hasil build asset dari stage sebelumnya
COPY --from=asset-builder /app /var/www/html

# Set permission dasar untuk storage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Konfigurasi FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm", "-F"]