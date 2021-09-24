<?php
use Firebase\JWT\JWT;
include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
class Token{
    
    public function __construct()
    {
        
    }

    private function checkToken(){
        if ( isset($_SERVER['HTTP_AUTHORIZATION']) && !preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            // header('HTTP/1.0 400 Bad Request');
            return false;
            exit;
        }
        if(isset($matches)){
            $jwt = $matches[1];
            //Veo si el token es extraible
            if (! $jwt) {
                // Token no extraible
                // header('HTTP/1.0 400 Bad Request');
                return false;
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
                return false;
                exit;
            }
            return $token;
        }
    }
    
    private function validToken($token){
        $now = new DateTimeImmutable();
        $serverName = URL;
        if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
            return false;
        }else{
            return true;
        }
    }

    public function hasValidToken(){
        $token = $this->checkToken();
        if($token != false){
            $foo  = $this->validToken($token);
            if($foo){
                return $token;
            }
            return false;
        }else{
            return false;
        }
        
        
    }
}