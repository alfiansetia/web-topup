# === STAGE 1: Install Composer & Node Modules ===
FROM php:8.3-fpm-alpine AS backend-builder

# Install dependensi minimal untuk composer install
RUN apk add --no-cache git unzip zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
# Bikin folder vendor dulu
RUN composer install --no-interaction --no-dev --ignore-platform-reqs

# === STAGE 2: Build Frontend (Vite) ===
FROM node:20-alpine AS frontend-builder
WORKDIR /app
# Salin semua source code beserta folder vendor dari STAGE 1
COPY --from=backend-builder /app /app
RUN npm ci
RUN npm run build

# === STAGE 3: Image Final (PHP-FPM) ===
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    curl-dev \
    netcat-openbsd

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip exif pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Salin folder vendor utuh dari STAGE 1
COPY --from=backend-builder /app/vendor ./vendor
# Salin hasil build dari STAGE 2
COPY --from=frontend-builder /app/public/build ./public/build

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]