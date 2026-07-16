# === STAGE 1: Install Composer & Node Modules ===
FROM php:8.3-fpm-alpine AS backend-builder
RUN apk add --no-cache git unzip zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-interaction --no-dev --ignore-platform-reqs

# === STAGE 2: Build Frontend (Vite) ===
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY --from=backend-builder /app /app
# Ganti npm ci dengan npm install agar lebih toleran jika lockfile absen/berbeda platform
RUN npm install
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

COPY --from=backend-builder /app/vendor ./vendor
COPY --from=frontend-builder /app/public/build ./public/build

# TAMBAHAN: Daftarkan direktori aman untuk Git agar tidak memicu error dubious ownership
RUN git config --global --add safe.directory /var/www

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]