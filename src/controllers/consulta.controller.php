<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/consulta.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear consultas
*/
class ConsultaController extends Controller{
    private $res;
    private $consulta;
    private $user;
    private $group;
    private $subject;
    function __construct($token)
    {
        $this->group = new GroupModel();
        $this->subject = new SubjectModel();
        $this->res = new Response();
        $this->user = new UserModel();
        $this->consulta = new ConsultaModel();
        parent::__construct($token);
    }

    public function createConsulta(){
        if($this->token->user_type == 'student'){
            if(parent::isTheDataCorrect($this->data,['materia'=>'is_int','asunto'=>'is_string'])){
                $student_group = $this->user->getUserGroups($this->token->user_id,'student');
                if(isset($student_group[0]['id_group'])){
                    $grupo = $student_group[0]['id_group'];
                    $teacher = $this->subject->getTeacherFromSubjectInGroup($this->data['materia'],$grupo);
                    if(is_int($teacher)){
                        $this->consulta->setStudent($this->token->user_id);
                        $this->consulta->setTeacher($teacher);
                        $this->consulta->setGroup($grupo);
                        $this->consulta->setSubject($this->data['materia']);
                        $this->consulta->setTheme($this->data['asunto']);
                        $consulta = $this->consulta->createQuery();
                        if($consulta != 0){
                            $this->consulta->createConsulta($consulta[0]['id']);
                            return $consulta;
                        }else{
                            return $this->res->error_500();
                        }
                    }else{
                    //si no es un  numero entonces capte un error 
                        return $this->res->error($teacher);
                    }
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }
    

    public function getActiveConsultas(){
        return $this->consulta->getConsultasFromUser($this->token->user_id,$this->token->user_type);
    }

    public function getConsultaById(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int'])){
                return $this->consulta->getConsultaById($this->data['consulta']);
            }else{
                return $this->res->error_400();
            }
        }else{
            if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int'])){
                if($this->user->UserHasAccesToConsulta($this->token->user_id,$this->data['consulta'])){
                    return $this->consulta->getConsultaById($this->data['consulta']);
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }
    }

    public function getAllConsultas(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['user'=>'is_int'])){
                return $this->consulta->getAllConsultasFromUser($this->data['user'], $this->user->getUserType($this->data['user']) );
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->consulta->getAllConsultasFromUser($this->token->user_id,$this->token->user_type);
        }
    }



    public function closeConsulta(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int'])){
                return $this->consulta->closeQuery($this->data['consulta']);
            }else{
                return $this->res->error_403();
            }
        }else{
            if($this->user->UserHasAccesToConsulta($this->token->user_id,$this->data['consulta'])){
                if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int'])){
                    return $this->consulta->closeQuery($this->data['consulta']);
                }else{
                    return $this->res->error_400();
                }
            }else{
                return $this->res->error_403();
            }
        }
    }


    public function postMessage(){
        if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int','msg'=>'is_string'])){
            if($this->user->UserHasAccesToConsulta($this->token->user_id,$this->data['consulta'])){
                $chat = $this->consulta->getQueryById($this->data['consulta']);
                if($chat['state'] != 0){
                    return $this->consulta->postMessagge($this->token->user_id,$this->data['consulta'],$this->data['msg']);
                    if($this->token->user_type == 'teacher'){
                        $this->consulta->setQueryToAnswered($this->data['consulta']);
                    }
                }else{
                    return $this->res->error('No puedes enviar mensajes a consultas cerradas',1085);
                }
            }else{
                return $this->res->error_403();
            }
        }else{
            return $this->res->error_400();
        }

    }
    
    public function getMessages(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int'])){
                return $this->consulta->getMessageFromQuery($this->data['consulta']);
            }else{
                return $this->res->error_400();
            }
        }else{
            if(parent::isTheDataCorrect($this->data,['consulta'=>'is_int'])){
                if($this->user->UserHasAccesToConsulta($this->token->user_id,$this->data['consulta'])){
                    return $this->consulta->getMessageFromQuery($this->data['consulta']);
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }
    }

}