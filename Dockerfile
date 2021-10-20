# FROM php:8.0-apache
FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install git -y

COPY src/php.ini /usr/local/etc/php/php.ini 

COPY src/ /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

EXPOSE 80

CMD sh /var/www/html/init-script.sh
#CMD /usr/sbin/apache2ctl -D FOREGROUND
