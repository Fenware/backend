#!/bin/bash


# Me muevo a la carpeta /var/www/html
cd "/var/www/html"

# Instalo todas las dependencias de composer
composer install
# Ejecuto el script de php para poder utilizar los websockets
php /var/www/html/ws/bin/crear-chat-server.php &
php /var/www/html/ws/bin/mensaje-chat-server.php &

# Inicio el servidor de apache
/usr/sbin/apache2ctl -D FOREGROUND