<?php
    require '/var/www/html/vendor/autoload.php';
    require '/var/www/html/ws/chat.php';
    $loop   = React\EventLoop\Factory::create();
    $pusher = new Chat;

    // Listen for the web server to make a ZeroMQ push after an ajax request
    $context = new React\ZMQ\Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    /*


    Cambiar 'tcp://127.0.0.1:5555' al ip del contenedor backend


    */
    $pull->bind('tcp://127.0.0.1:5556'); // Binding to 127.0.0.1 means the only client that can connect is itself
    $pull->on('message', array($pusher, 'onMessageEntry'));

    // Set up our WebSocket server for clients wanting real-time updates
    $webSock = new React\Socket\Server('0.0.0.0:8086', $loop); // Binding to 0.0.0.0 means remotes can connect
    $webServer = new Ratchet\Server\IoServer(
        new Ratchet\Http\HttpServer(
            new Ratchet\WebSocket\WsServer(
                new Ratchet\Wamp\WampServer(
                    $pusher
                )
            )
        ),
        $webSock
    );

    $loop->run();