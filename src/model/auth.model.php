<?php

declare(strict_types=1);
use Firebase\JWT\JWT;
require_once '/var/www/html/vendor/autoload.php';
include_once '/var/www/html/core/model.php';
include_once '/var/www/html/model/user.model.php';
include_once '/var/www/html/core/response.php';


/*
Modelo para autenticar logins
Al logearse de forma exitosa este genera el token para el usuario
*/
class AuthModel extends Model{

    private $user;
    private $res;
    function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    

    /*
    Genera un token(esto sera movido a class Token en un futuro)
    */
    function generateToken($user,$type){
        $res = new Response();
        $secret_key = SECRET_KEY;
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+8 hour')->getTimestamp();
        $serverName = URL;
        $user_id  = $user;  

        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,                           // Expire
            'user_id' => $user_id, 
            'user_type' => $type                    // User name
        ];

        try {
            $jwt =  JWT::encode(
                $data,
                $secret_key,
                'HS512'
            );
        } catch (\Throwable $th) {
            $jwt = $res->OOPSIE();
            print_r($res->OOPSIE());
        }
        return $jwt;

    }
}