<?php
use Firebase\JWT\JWT;
include_once 'vendor/autoload.php';
header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");


abstract class API{
    //Chequeo que me llegue el token
    function __construct($res)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->POST();
        }elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->GET();
        }elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
            $this->PUT();
        }elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
            $this->DELETE();
        }else{
            header('Content-Type: applicaton/json');
            $datosArray = $res->error_405();
            echo json_encode($datosArray);
        }
    }

    abstract protected function POST();
    abstract protected function GET();
    abstract protected function PUT();
    abstract protected function DELETE();

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