#!/bin/bash

# Me muevo a la carpeta /var/www/html
cd "/var/www/html"

# Instalo todas las dependencias de composer
composer install

# Inicio el servidor de apache
/usr/sbin/apache2ctl -D FOREGROUND