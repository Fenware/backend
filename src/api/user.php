<?php

include_once 'core/api.php';
include_once 'model/user.model.php';
include_once 'core/response.php';

class UserAPI extends API{
    
    private $user;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->user = new UserModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        //TODO
    }

    public function GET($token,$data){
        if($token->user_type == 'administrador'){
            $datosArray = $this->user->getAllUsers();
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function PUT($token,$data){

    }

    public function DELETE($token,$data){

    }

}

