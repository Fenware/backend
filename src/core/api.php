<?php
use Firebase\JWT\JWT;
include_once 'vendor/autoload.php';
header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");


abstract class API{
    //Chequeo que me llegue el token
    function __construct()
    {
        
    }

    public function checkToken($res){
        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        
            header('HTTP/1.0 400 Bad Request');
            echo json_encode($res->error('Capo te falta el token'));
            exit;
        }
        $jwt = $matches[1];
        //Veo si el token es extraible
        if (! $jwt) {
            // Token no extraible
            header('HTTP/1.0 400 Bad Request');
            echo json_encode($res->error('No pudimos estraer tu token pa'));
            exit;
        }
        $secret_key  = SECRET_KEY;
        //Des encripto el token
        try {
            $token =  JWT::decode(
                $jwt,
                $secret_key,
                ['HS512']
            );
        } catch (\Throwable $th) {
            echo json_encode($res->error('Tu token no se pudo des-encriptar'));
            exit;
        }
        return $token;
    }
    
    public function validToken($token){
        $now = new DateTimeImmutable();
        $serverName = URL;
        if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
            return false;
        }else{
            return true;
        }
    }

   
}