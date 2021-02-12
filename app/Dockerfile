###########
# build dependencies in a separate container
###########
FROM composer as builder

WORKDIR /app/

COPY composer.json composer.lock ./

RUN composer install

###########
# build main image
###########
FROM php:7.4-fpm-alpine

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install fileinfo

WORKDIR /var/www

COPY --from=builder /app/vendor /var/www/vendor