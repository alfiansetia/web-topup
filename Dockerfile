# --- Stage 1: Build Assets & Vendor ---
FROM php:8.3-fpm AS builder

RUN apt-get update && apt-get install -y unzip git zip && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# --- Stage 2: Node.js untuk Build Asset ---
FROM node:22-alpine AS asset-builder
WORKDIR /app

# Ambil seluruh file dari stage builder (termasuk folder vendor)
COPY --from=builder /app /app
RUN npm ci
RUN npm run build

# --- Stage 3: Production App (PHP Server) ---
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

# ==================== PERBAIKAN DI SINI ====================
# 1. Salin source code awal dan vendor PHP dari stage builder pertama
COPY --from=builder /app /var/www/html

# 2. TIMPA folder public dengan hasil build asset (Vite) dari stage asset-builder
COPY --from=asset-builder /app/public /var/www/html/public
# ===========================================================

# Set permission dasar untuk storage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Konfigurasi FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm", "-F"]