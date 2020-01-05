FROM php:7.4-fpm

COPY php.ini  "$PHP_INI_DIR/php.ini"

RUN pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug


RUN docker-php-ext-install mysqli pdo pdo_mysql