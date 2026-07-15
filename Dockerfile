# ── Stage 1: Install PHP dependencies ──
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# ── Stage 2: Build frontend assets ──
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY resources/ resources/
COPY vite.config.js ./
COPY --from=vendor /app/vendor vendor
RUN npm run build

# ── Stage 3: PHP application ──
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    xml \
    bcmath \
    zip \
    gd \
    intl \
    opcache \
    pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy vendor from Stage 1 & built frontend from Stage 2
COPY --from=vendor /app/vendor vendor
COPY --from=frontend /app/public/build public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Ensure .env exists for artisan commands
RUN cp .env.example .env 2>/dev/null || true

# Generate app key if not set
RUN php artisan key:generate 2>/dev/null || true

# Copy entrypoints
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/entrypoint-worker.sh /usr/local/bin/entrypoint-worker.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/entrypoint-worker.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
