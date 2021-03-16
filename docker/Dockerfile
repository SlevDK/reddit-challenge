FROM php:7.4-apache-buster

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"

WORKDIR /var/www

# install the PHP extensions we need (https://laravel.com/docs/7.x/installation#server-requirements)
RUN apt-get update && apt-get install --no-install-recommends -y \
    zip \
    unzip \
    git \
    zlib1g-dev \
    mariadb-client \
    libmagickwand-dev \
    libjpeg-dev \
    libpng-dev \
    libzip-dev \
    && pecl install imagick-3.4.4 \
    && pecl install xdebug-2.9.7 \
    && pecl install redis-5.3.1 \
    && pecl install pcov-1.0.6 \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-enable imagick redis \
    && docker-php-ext-install -j "$(nproc)" \
    bcmath \
    pdo_mysql \
    opcache \
    zip \
    gd

RUN a2enmod rewrite remoteip

COPY --from=composer:1.10 /usr/bin/composer /usr/bin/composer
COPY --from=composer:1.10 /tmp/keys* /root/.composer/

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --no-ansi \
    --no-interaction \
    --no-autoloader \
    --no-dev

COPY docker/apache2/apache2.conf /etc/apache2/
COPY docker/apache2/000-default.conf /etc/apache2/sites-available/
COPY docker/apache2/mpm_prefork.conf /etc/apache2/mods-enabled/
COPY docker/apache2/status.conf  /etc/apache2/mods-enabled/

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
