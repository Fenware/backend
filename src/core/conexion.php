<?php

require_once '/var/www/html/config/config.php';
class Conexion{

    private $host;
    private $database;
    private $user;
    private $password;

    public function __construct(){
        $this->host = HOST;
        $this->database = DATABASE;
        $this->user = USER_DB;
        $this->password = PASS_DB;
    }

    function connect(){
        try {
            $conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database , $this->user, $this->password);
            return $conn;
        } catch (PDOException $error) {
            echo 'Se nos cayo el servidor,perdon <3';
            return false;
        }
    }
}