FROM php:8.3-fpm

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
    libpq-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        gd \
        bcmath \
        exif \
        pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --from=node:22 /usr/local /usr/local

WORKDIR /var/www/html

COPY . .

RUN sed -i \
    -e 's/^;listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' \
    /usr/local/etc/php-fpm.d/www.conf

RUN chmod +x docker-entrypoint.sh

ENTRYPOINT ["./docker-entrypoint.sh"]

CMD ["php-fpm"]