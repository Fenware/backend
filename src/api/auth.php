<?php

include_once 'core/api.php';
include_once 'core/iAPI.php';
include_once 'model/auth.model.php';
include_once 'core/response.php';

class AuthAPI extends API implements iAPI{
    private $auth;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->auth = new AuthModel();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->POST();
        }else{
            $datosArray = $this->res->error_405();
            echo json_encode($datosArray);
        }
    }

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

    }

    public function PUT(){

    }

    public function DELETE(){

    }

    

}