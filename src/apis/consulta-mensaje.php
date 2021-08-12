<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/consulta.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para enviar mensajes a una consulta
*/
class ConsultaMessageAPI extends API{
    private $res;
    private $consulta;
    private $group;
    private $user;
    function __construct()
    {
        $this->group = new GroupModel();
        $this->res = new Response();
        $this->user = new UserModel();
        $this->consulta = new ConsultaModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if(parent::isTheDataCorrect($data,['consulta'=>'is_int','msg'=>'is_string'])){
            if($this->user->UserHasAccesToConsulta($token->user_id,$data['consulta'])){
                $datosArray = $this->consulta->postMessagge($token->user_id,$data['consulta'],$data['msg']);
                if($token->user_type == 'teacher'){
                    $this->consulta->setQueryToAnswered($data['consulta']);
                }
            }else{
                $datosArray = $this->res->error_403();
            }
        }else{
            $datosArray =$this->res->error_400();
        }
        echo json_encode($datosArray);
    }
    
    public function GET($token,$data){
        if(parent::isTheDataCorrect($data,['consulta'=>'is_string'])){
            if($this->user->UserHasAccesToConsulta($token->user_id,$data['consulta'])){
                $datosArray = $this->consulta->getMessageFromQuery($data['consulta']);
            }else{
                $datosArray = $this->res->error_403();
            }
        }else{
            $datosArray =$this->res->error_400();
        }
        echo json_encode($datosArray);
    }

    public function PUT($token,$data){
        //Nothing here
    }

    public function DELETE($token,$data){
        //Nothing here
    }

}