FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        imagemagick \
        libmagick++-dev \
        libmagickwand-dev \
        libmcrypt-dev \
        libpng-dev \
        libzip-dev \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        zip \
    && pecl install apcu_bc \
    && pecl install imagick  \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/app

COPY docker/php/custom.ini docker/php/opcache.ini docker/php/apcu.ini /usr/local/etc/php/conf.d/

RUN chown -R 1000:1000 /var/www/app