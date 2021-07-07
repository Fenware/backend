<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class UserGroupAPI extends API{
    private $res;
    private $group;
    private $user;
    function __construct()
    {
        $this->res = new Response();
        $this->group = new GroupModel();
        $this->user = new UserModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['code'=>'is_string'])){
                $query = $this->user->giveUserGroup($token->user_id,$data['code'],$token->user_type);
                echo json_encode($query);
            }
        }
        if($token->user_type == 'student'){
            if(parent::isTheDataCorrect($data,['code'=>'is_string'])){
                if($this->user->userHasGroup($token->user_id)){
                    
                    $datosArray =  $this->res->error('Un estudiante solo puede estar en un grupo a la vez');
                }else{
                    $datosArray = $this->user->giveUserGroup($token->user_id,$data['code'],$token->user_type);
                }
                echo json_encode($datosArray);
            }
        }
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            //TODO
        }else{
            $grupos = $this->user->getUserGroups($token->user_id,$token->user_type);
            echo json_encode($grupos);
        }
        
    }

    public function PUT($token,$data){
        
    }

    public function DELETE($token,$data){
        //Chequeo que este intentando modificarse a si mismo
        if(parent::isTheDataCorrect($data,['grupo'=>'is_int'])){
            $grupo = $data['grupo'];
            $datosArray = $this->user->deleteUserGroup($token->user_id,$grupo,$token->user_type);
        }else{
            $grupo = 0;
            $datosArray = $this->user->deleteUserGroup($token->user_id,$grupo,$token->user_type);
        }
        echo json_encode($datosArray);
    }
}