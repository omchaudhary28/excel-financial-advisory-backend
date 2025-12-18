FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN a2enmod rewrite headers env

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html
