<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class PendentUserAPI extends API{
    private $user;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->user = new UserModel();
        parent::__construct($this->res);
    }
    
    public function POST($token,$data){
        if($token->user_type == 'administrator'){
            if(isset($data['id']) && is_int($data['id'])){
                $datosArray = $this->user->patchUser($id,'`state`',1);
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            $datosArray = $this->user->getPendentUsers();
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function PUT($token,$data){
        
    }

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            if(isset($data['id']) && is_int($data['id'])){
                $datosArray = $this->user->patchUser($data['id'],'`state`',0);
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }
}
