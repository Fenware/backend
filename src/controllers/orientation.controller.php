<?php

use JetBrains\PhpStorm\Internal\ReturnTypeContract;

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear orientaciones
*/
class OrientationController extends Controller{
    private $res;
    private $orientation;
    private $subject;
    function __construct()
    {
        $this->res = new Response();
        $this->orientation = new OrientationModel();
        $this->subject = new SubjectModel();
        parent::__construct($this->res);
    }


    public function postOrientation(){
        if(parent::isTheDataCorrect($this->data, ['name'=>'is_string','year'=>'is_int'])){
            if(isset($this->data['subjects']) && parent::isArrayDataCorrect($this->data['subjects'],'is_int')){
                $orientacion = $this->orientation->getOrienation($this->data['name'],$this->data['year']);
                if($orientacion){
                    if($orientacion['state'] == 0){
                        return $this->orientation->changeOrientationState($orientacion['id'],1);
                    }else{
                        return $this->res->error('La orientacion ya existe',1020);
                    }   
                }else{
                    $id = $this->orientation->postOrientation($this->data['name'],$this->data['year']);
                    if($id > 0){
                        $this->addOrientationSubjects($id,$this->data['subjects']);
                        return $this->orientation->getOrientationById($id);
                    }else{
                        return $this->res->error_500();
                    }
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_400();
        }
    }

    public function addOrientationSubjects($orientation,$subjects){
        foreach($subjects as $s){
            $materia = $this->subject->getSubjectById($s);
            if($materia['state'] == 1){
                $so = $this->orientation->getSubjectInOrientation($orientation,$materia['id']);
                if($so['state'] == 0){
                    $this->orientation->reAddSubject($orientation,$materia['id']);
                }else{
                    $this->orientation->postSubjectInOrientation($orientation,$materia['id']);
                }
            }
        }
    }



    public function getOrientations(){
        return $this->orientation->getOrientations();
    }

    public function getOrientationById(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_string'])){
            return $this->orientation->getOrientationById($this->data['id']);
        }else{
            return $this->res->error_400();
        }
    }

    public function modifyOrientation(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_int','name'=>'is_string','year'=>'is_int'])){
            return $this->orientation->putOrientation($this->data['id'],$this->data['name'],$this->data['year']);
        }else{
            return $this->res->error_400();
        }
    }

    public function deleteOrientation(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
            return $this->orientation->deleteOrientation($this->data['id']);
        }else{
            $datosArray = $this->res->error_400();
        }
    }


    public function removeOrienationSubjects(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
            if(isset($this->data['subjects']) && parent::isArrayDataCorrect($this->data['subjects'],'is_int')){
                return $this->orientation->deleteSubjectsInOrientation($this->data['id'],$this->data['subjects']);
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_400();
        }
    }
    
    public function getOrienationSubjects(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
            return $this->orientation->getOrientationSubjects($this->data['id']);
        }else{
            return $this->res->error_400();
        }
        
    }
}