<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/consulta.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class ConsultaAPI extends API{
    private $res;
    private $consulta;
    private $group;
    function __construct()
    {
        $this->group = new GroupModel();
        $this->res = new Response();
        $this->user = new UserModel();
        $this->consulta = new ConsultaModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'student'){
            if(parent::isTheDataCorrect($data,['id_teacher'=>'is_int','id_subject'=>'is_int','theme'=>'is_string'])){
                $student_group = $this->user->getUserGroups($token->user_id,'student');
                $grupo = $student_group[0]['id_group'];
                $datosArray = $this->consulta->createConsulta($token->user_id,$data['id_teacher'],$grupo,$data['id_subject'],$data['theme']);
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }
    
    public function GET($token,$data){
        $datosArray = $this->consulta->getConsultasFromUser($token->user_id);
        echo json_encode($datosArray);
    }

    public function PUT($token,$data){
        
    }

    public function DELETE($token,$data){
        
    }

}