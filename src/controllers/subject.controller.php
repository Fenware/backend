<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class SubjectController extends Controller{
    
    private $materia;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->materia = new SubjectModel();
        parent::__construct();
    }

    public function postSubject($data){
        if(parent::isTheDataCorrect($this->data, ['name'=>'is_string'] )){
            $subject =  $this->materia->getSubjectByName($this->data['name']); 
            if($subject){
                if($subject['state'] == 0){
                    return $this->materia->changeSubjectState($this->data['name'],1);
                }else{
                    return $this->res->error('La materia ya existe',1010);
                }
            }else{
                return $this->materia->postSubject($this->data['name']);
            }
        }else{
            return $this->res->error_400();
        }
    }

    public function getSubjects(){
        return $this->materia->getSubjects();
    }

    public function getSubjectById(){
        if(parent::isTheDataCorrect($this->data , ['id'=>'is_int'] )){
            return $this->materia->getSubjectById($this->data['subject']);
        }else{
            return $this->res->error_400();
        }
    }

    public function getSubjectByName(){
        if(parent::isTheDataCorrect($this->data , ['name'=>'is_string'] )){
            return $this->materia->getSubjectById($this->data['subject']);
        }else{
            return $this->res->error_400();
        }
    }

    public function modifySubject(){
        if(parent::isTheDataCorrect($this->data , ['id'=>'is_int','name'=>'is_string'])){
            return $this->materia->putSubject($this->data['id'],$this->data['name']);
        }else{
            return $this->res->error_400();
        }
    }


    public function deleteSubject(){
        if(parent::isTheDataCorrect($this->data , ['id'=>'is_int'] )){
            return $this->materia->deleteSubject($this->data['id']);
        }else{
            return $this->res->error_400();
        }
    }

}

