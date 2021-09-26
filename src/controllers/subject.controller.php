<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class SubjectController extends Controller{
    
    private $materia;
    private $res;
    function __construct($token)
    {
        $this->res = new Response();
        $this->materia = new SubjectModel();
        parent::__construct($token);
    }

    public function createSubject(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data, ['name'=>'is_string'] )){
                $subject =  $this->materia->getSubjectByName($this->data['name']); 
                if($subject){
                    if($subject['state'] == 0){
                        $rows = $this->materia->changeSubjectState($subject['id'],1);
                        if($rows > 0){
                            return $this->materia->getSubjectById($subject['id']);
                        }else{
                            return 0;
                        }
                    }else{
                        return $this->res->error('La materia ya existe',1010);
                    }
                }else{
                    $id = $this->materia->postSubject($this->data['name']);
                    return $this->materia->getSubjectById($id);
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
        
    }

    public function getSubjects(){
        return $this->materia->getSubjects();
    }

    public function getSubjectById(){
        if(parent::isTheDataCorrect($this->data , ['id'=>'is_int'] )){
            return $this->materia->getSubjectById($this->data['id']);
        }else{
            return $this->res->error_400();
        }
    }


    public function modifySubject(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data , ['id'=>'is_int','name'=>'is_string'])){
                return $this->materia->putSubject($this->data['id'],$this->data['name']);
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }


    public function deleteSubject(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data , ['id'=>'is_int'] )){
                $this->materia->deleteSubjectFromAllOrientations($this->data['id']);
                $this->materia->deleteTeachersFromSubject($this->data['id']);
                $this->materia->closeQuerysFromSubject($this->data['id']);
                return $this->materia->deleteSubject($this->data['id']);
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }

}

