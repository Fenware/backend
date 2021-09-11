docker exec -t php_chathink composer install
docker exec php_chathink php /var/www/html/ws/bin/crear-chat-server.php &
docker exec php_chathink php /var/www/html/ws/bin/mensaje-chat-server.php &

