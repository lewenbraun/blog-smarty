FROM php:8.5-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
        mysql-client \
        unzip \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

RUN mkdir -p /var/www/storage/cache/smarty/compile /var/www/storage/cache/smarty/cache \
    && chown -R www-data:www-data /var/www/storage/cache

CMD ["php-fpm"]
