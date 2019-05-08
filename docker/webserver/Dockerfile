FROM php:7.1-apache

RUN apt-get -y update \
    && apt-get install -y libpng-dev \
    && docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) curl \
    && apt-get install -y zlib1g-dev \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git, zip, wget, vim
RUN apt-get update \
    && apt-get -y install git \
    && apt-get -y install zip \
    && apt-get -y install wget \
    && apt-get -y install vim \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer global require hirak/prestissimo --no-plugins --no-scripts

