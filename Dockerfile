FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

COPY src/php.ini /usr/local/etc/php/php.ini

EXPOSE 80