<?php

require_once '/var/www/html/core/token.php';

class Middleware{

    public function __construct(){
        
            
    }

    /*
    Funcion de validacion general
    */
    public function validate(){
        if($this->verifyRequestMethods()){
            $token = $this->verifyToken();
            if($token){
                return $token;
            }else{
                throw new Exception('No token found');
            }
        }else{
            throw new Exception('Metodo no permitido');
        }
    }

    /*
    Verifica los tokens
    */
    private function verifyToken(){
        $token = new Token();
        return $token->hasValidToken();
    }

    /*
    Se acegura de que solo se este usando post
    */
    private function verifyRequestMethods(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return true;
        }else{
            return false;
        }
    }
}