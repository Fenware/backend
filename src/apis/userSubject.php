<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class UserSubjectAPI extends API{
    
    private $materia;
    private $res;
    private $user;
    private $grupo;
    function __construct()
    {
        $this->res = new Response();
        $this->materia = new SubjectModel();
        $this->user = new UserModel();
        $this->grupo = new GroupModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['grupo'=>'is_int','materia'=>'is_int'])){
                if($this->grupo->IsSubjectInGroup($data['grupo'],$data['materia'])){
                    $taken = $this->materia->IsSubjectInGroupTaken($data['grupo'],$data['materia']);
                    if($taken){
                        $datosArray = $this->res->error('Esta materia ya tiene un docente',1060);
                    }else{
                        $in_group = $this->user->IsUserInGroup($token->user_id,$data['grupo'],$token->user_type);
                        if($in_group){
                            $result = $this->materia->GiveSubjectInGroupToTeacher($token->user_id,$data['grupo'],$data['materia']);
                            $datosArray = $result;
                        }else{
                            $datosArray = $this->res->error('No perteneces a este grupo',1061);
                        }
                    }
                }else{
                    $datosArray = $this->res->error('Esta materia no pertenece a este grupo',1062);
                }
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }
    }
    public function GET($token,$data){
        if($token->user_type == 'teacher'){
            $datosArray = $this->materia->getTeacherSubjects($token->user_id);
            echo json_encode($datosArray);
        }
    }
    public function PUT($token,$data){
        if($token->user_type == 'teacher'){
            
        }
    }
    public function DELETE($token,$data){
        if($token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['grupo'=>'is_int','materia'=>'is_int'])){
                $datosArray = $this->materia->removeTeacherFromSubjectInGroup($token->user_id,$data['grupo'],$data['materia']);
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }
    }
}