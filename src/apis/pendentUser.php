<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para acceptar usuarios pendientes
*/
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
            if(parent::isTheDataCorrect($data,['id'=>'is_int'])){
                $datosArray = $this->user->patchUser($data['id'],'state_account',1);
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            $datosArray = $this->user->getPendentUsers();
            foreach($datosArray as &$user){
                $type = $this->user->getUserType($user['id']);
                $user['type'] = $type;
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function PUT($token,$data){
        
    }

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($data,['id'=>'is_int'])){
                $datosArray = $this->user->patchUser($data['id'],'state_account',0);
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }
}
