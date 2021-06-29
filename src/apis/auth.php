<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/model/auth.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");
header('Content-type: application/json');

class AuthAPI{
    private $auth;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->auth = new AuthModel();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            echo 'post';
            $this->POST();
        }elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->GET();
        }elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
            $this->PUT();
        }elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
            $this->DELETE();
        }else{
            header('Content-Type: applicaton/json');
            $datosArray = $this->res->error_405();
            echo json_encode($datosArray);
        }
    }

    //LOGIN - IN POST FOR SECURITY
    public function POST(){
        $postBody = file_get_contents('php://input');
        $datosArray = $this->auth->login($postBody);
        if(isset($datosArray['result']['error_id'])){
        $response_code = $datosArray['result']['error_id'];
        http_response_code($response_code);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);
    }

    public function GET(){
        exit;
    }

    public function PUT(){
        exit;
    }

    public function DELETE(){
        exit;
    }

    
    

}