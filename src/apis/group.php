<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear grupos
*/
class GroupAPI extends API{
    private $res;
    private $group;
    function __construct()
    {
        $this->res = new Response();
        $this->group = new GroupModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'administrator'){
            //Cheque la informacion
            if(parent::isTheDataCorrect($data,['name'=>'is_string','orientacion'=>'is_int'])){
                $grupito = $this->group->postGroup($data['name'],$data['orientacion']);
                if(!is_string($grupito)){
                    $datosArray = $grupito;
                }else{
                    $datosArray = $this->res->error($grupito);
                }
            }else{
                
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }
    
    public function GET($token,$data){
        if($token->user_type == 'administrator' || $token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['id'=>'is_string'])){
                $datosArray = $this->group->getGroupById($data['id']);
            }elseif(parent::isTheDataCorrect($data,['name'=>'is_string'])){
                if(is_string($data['name'])){
                    $datosArray = $this->group->getGroupByName($data['name']);
                }else{
                    $datosArray = $this->res->error_400();
                }
            }else{
                $datosArray = $this->group->getGroups();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function PUT($token,$data){
        if($token->user_type == 'administrator'){
            if(!isset($data['name']) || !isset($data['orientacion']) || !isset($data['id'])){
                $datosArray = $this->res->error_400();
            }else{
                if(!is_string($data['name']) || !is_int($data['orientacion']) || !is_int($data['id'])){
                    $datosArray = $this->res->error_400();
                }else{
                    $datosArray = $this->group->putGroup($data['id'],$data['name'],$data['orientacion']);
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }
    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            if(isset($data['id'])){
                if(is_int($data['id'])){
                    $datosArray = $this->group->deleteGroup($data['id']);
                }else{
                    $datosArray = $this->res->error_400();
                }
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

}