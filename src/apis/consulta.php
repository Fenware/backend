<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/consulta.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear consultas
*/
class ConsultaAPI extends API{
    private $res;
    private $consulta;
    private $user;
    private $group;
    private $subject;
    function __construct()
    {
        $this->group = new GroupModel();
        $this->subject = new SubjectModel();
        $this->res = new Response();
        $this->user = new UserModel();
        $this->consulta = new ConsultaModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'student'){
            if(parent::isTheDataCorrect($data,['materia'=>'is_int','asunto'=>'is_string'])){
                $student_group = $this->user->getUserGroups($token->user_id,'student');
                $grupo = $student_group[0]['id_group'];
                $teacher = $this->subject->getTeacherFromSubjectInGroup($data['materia'],$grupo);
                if(is_int($teacher)){
                    $datosArray = $this->consulta->createConsulta($token->user_id,$teacher,$grupo,$data['materia'],$data['asunto']);
                }else{
                    //si no es un  numero entonces capte un error 
                    $datosArray = $this->res->error($teacher);
                }
                
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }
    
    

    public function GET($token,$data){
        if($token->user_type == 'administrator'){ //al ser admini no comprueba el acceso a la consulta
            if(parent::isTheDataCorrect($data,['consulta'=>'is_string'])){
                $datosArray = $this->consulta->getConsultaById($data['consulta']);
            }elseif(parent::isTheDataCorrect($data,['user'=>'is_string'])){
                $type = $this->user->getUserType($data['user']);
                $datosArray = $this->consulta->getConsultasFromUser($data['user'],$type);
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{ 
            if(parent::isTheDataCorrect($data,['consulta'=>'is_string'])){
                if($this->user->UserHasAccesToConsulta($token->user_id,$data['consulta'])){
                    $datosArray = $this->consulta->getConsultaById($data['consulta']);
                }else{
                    $datosArray = $this->res->error_403();
                }
            }else{
                if(isset($data['all'])){
                    $datosArray = $this->consulta->getAllConsultasFromUser($token->user_id,$token->user_type);
                }else{
                    $datosArray = $this->consulta->getConsultasFromUser($token->user_id,$token->user_type);
                }
                
            }
    }
        echo json_encode($datosArray);
    }

    

    public function PUT($token,$data){
        
    }

    public function DELETE($token,$data){
        if($token->user_type == 'student'){
            if(parent::isTheDataCorrect($data,['consulta'=>'is_int'])){
                $acces = $this->user->StudentIsAutorOfConsulta($token->user_id,$data['consulta']);
                if($acces){
                    $datosArray = $this->consulta->closeConsulta($data['consulta']);
                }else{
                    $datosArray = $this->res->error_403();
                }
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }

}