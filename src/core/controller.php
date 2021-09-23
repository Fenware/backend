<?php
use Firebase\JWT\JWT;
include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-type: application/json');

/*
Clase API :
Todas las api que requieran el uso de token heredan de esta clase
*/
class Controller{
    
    private $data;

    private $token;

    function __construct()
    {
        $this->data = $this->getJson();
    }

    /*
    Creo metodos abstractos cosa de que todo API los tenga
    */ 
    /*
    checkToken , validToken ,HasValidToken se usan para validar un token. 
    */
    private function checkToken($res){
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            // header('HTTP/1.0 400 Bad Request');
            echo json_encode($res->error('Capo te falta el token'));
            exit;
        }
        $jwt = $matches[1];
        //Veo si el token es extraible
        if (! $jwt) {
            // Token no extraible
            // header('HTTP/1.0 400 Bad Request');
            echo json_encode($res->error('No pudimos extraer tu token pa'));
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
    
    private function validToken($token){
        $now = new DateTimeImmutable();
        $serverName = URL;
        if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
            return false;
        }else{
            return true;
        }
    }

    private function HasValidToken($res){
        $token = $this->checkToken($res);
        $foo  = $this->validToken($token);
        if($foo == true){
            return $token;
        }else{
            return false;
        }
    }

    //Gets the body of the request and converts it from json to array
    private function getJson(){
        $postBody = file_get_contents('php://input');
        $data = json_decode($postBody,true);
        return $data;
    }



    /*
    Chequea que la informacion especificada de un array exista ,no este vacia y que sea del tipo correcto
    El primer parametro es el array, el segundo se usa para indicar el nombre del campo y su tipo
    Ej:
    isTheDataCorrect($array_con_datos,['ci'=>'is_string','edad'=>'is_int'])
    */
    public function isTheDataCorrect($data,$vars){
        $correct = true;
        foreach($vars as $key => $value){
            if(    !isset($data[$key])
                || !$value($data[$key])
                || empty($data[$key])){
                $correct = false;
            }
        }
        return $correct;
    }

    /*
    Chequea si el contenido de un array es de cierto tipo
    Ej:
    isArrayDataCorrect(['Uruguay','Argentina','Chile'],is_int)
    Devuelve falso
    isArrayDataCorrect(['Uruguay','Argentina','Chile'],is_string)
    Devuelve verdadero
    */
    public function isArrayDataCorrect($array,$type){
        $correct = true;
        foreach($array as $value){
            if(!$type($value)){
                $correct = false;
            }
        }
        return $correct;
    }
   

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
}