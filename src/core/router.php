<?php

class Router{

    function __construct(){

        $url = isset($_GET['url']) ? $_GET['url'] : null;

        $url = rtrim($url, '/');

        // ej. user/home 
        // ej. ['user', 'home']
        $url = explode('/', $url);

        // ej. user
        $url_controller = $url[0];

        if (empty($url_controller)) {
            // Requiriendo el controlador por defecto
            $this->redirectToController('main');

            return false;
        }

        $this->redirectToController($url_controller);
    }

    // Cambia controller_name por $url_controller
    function redirectToController($controller_name){
        $controller_route = 'controllers/' . $controller_name . '.php';

        if (file_exists($controller_route)) {

            require_once $controller_route;

            $controller_name .= 'Controller';

            $controller = new $controller_name();
        }else {
            include_once 'views/errors/404.php';
        }
    }
}
