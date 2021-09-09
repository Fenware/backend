FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql


RUN apt-get update && apt-get install libzmq3-dev git -y \
&& git clone git://github.com/mkoppanen/php-zmq.git \
&& cd php-zmq \
&& phpize \
&& ./configure \
&& make \
&& make install

RUN docker-php-ext-enable zmq

COPY src/php.ini /usr/local/etc/php/php.ini 

COPY src/ /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

EXPOSE 80 5555 5556 8085 8086

CMD sh /var/www/html/init-script.sh
#CMD /usr/sbin/apache2ctl -D FOREGROUND
