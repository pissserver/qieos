FROM php:7.4-apache

RUN printf '%s\n' \
        'deb https://snapshot.debian.org/archive/debian/20221114T000000Z bullseye main' \
        'deb https://snapshot.debian.org/archive/debian-security/20221114T000000Z bullseye-security main' \
        'deb https://snapshot.debian.org/archive/debian/20221114T000000Z bullseye-updates main' \
        > /etc/apt/sources.list \
    && apt-get -o Acquire::Check-Valid-Until=false update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mbstring mysqli xml xmlwriter zip \
    && a2enmod headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
