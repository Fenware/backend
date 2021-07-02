<?php
use Firebase\JWT\JWT;
include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-type: application/json');


abstract class API{
    //Chequeo que me llegue el token
    
    function __construct($res)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->operate('POST',$res);
        }elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->operate('GET',$res);
        }elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
            $this->operate('PUT',$res);
        }elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
            $this->operate('DELETE',$res);
        }else{
            header('Content-Type: applicaton/json');
            $datosArray = $res->error_405();
            echo json_encode($datosArray);
        }
    }

    abstract protected function POST($token,$data);
    abstract protected function GET($token,$data);
    abstract protected function PUT($token,$data);
    abstract protected function DELETE($token,$data);

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


    //Validates token and  if so runs the function $function
    public function operate($function,$res){
        $token = $this->HasValidToken($res);
        if($token == false){
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode($res->error('Not a valid token'));
        }else{
            $data = $this->getJson();
            $this->$function($token,$data);
        }
    }

    //$data is  the array of data,$vars is writen like so ['id' => 'is_int','name' => 'is_string']
    //Checks if all the data requested is setted ,the correct variable type and if it is not empty
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

    //Checks if the data inside and array is the correct type
    //Ej : $type = 'is_int'
    public function isArrayDataCorrect($array,$type){
        $correct = true;
        foreach($array as $value){
            if(!$type($value)){
                $correct = false;
            }
        }
        return $correct;
    }
   
}