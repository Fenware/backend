<?php

use JetBrains\PhpStorm\Internal\ReturnTypeContract;

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear orientaciones
*/
class OrientationController extends Controller{
    private $res;
    private $orientation;
    private $subject;
    private $group;
    function __construct($token)
    {
        $this->res = new Response();
        $this->orientation = new OrientationModel();
        $this->subject = new SubjectModel();
        $this->group = new GroupModel();
        parent::__construct($token);
    }


    public function createOrientation(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data, ['name'=>'is_string','year'=>'is_int'])){
                if(isset($this->data['subjects']) && parent::isArrayDataCorrect($this->data['subjects'],'is_int')){
                    $orientacion = $this->orientation->getOrienation($this->data['name'],$this->data['year']);
                    if($orientacion){
                        if($orientacion['state'] == 0){
                            $rows =  $this->orientation->changeOrientationState($orientacion['id'],1);
                            $this->addOrientationSubjects($orientacion['id'],$this->data['subjects']);
                            return $rows;
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
                    echo 'mal subj';
                    return $this->res->error_400();
                }
            }else{
                echo 'mal gen';
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }

    
    public function addOrientationSubjects_(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data, ['orientation'=>'is_int','subjects'=>'is_array'] )){
                if(parent::isArrayDataCorrect($this->data['subjects'],'is_int')){
                    foreach($this->data['subjects'] as $s){
                        $materia = $this->subject->getSubjectById($s);
                        if($materia && $materia['state'] == 1){
                            $so = $this->orientation->getSubjectInOrientation($this->data['orientation'],$materia['id']);
                            if($so['state'] == 0){
                                $this->orientation->reAddSubject($this->data['orientation'],$materia['id']);
                            }else{
                                $this->orientation->postSubjectInOrientation($this->data['orientation'],$materia['id']);
                            }
                        }   
                }
                }else{
                    return $this->res->error_400();
                }
            }else{
                return $this->res->error_400();
            }
            
        }else{
            return $this->res->error_403();
        }
    }

    private function addOrientationSubjects($orientation,$subjects){
        if($this->token->user_type == 'administrator'){
            foreach($subjects as $s){
                $materia = $this->subject->getSubjectById($s);
                if($materia && $materia['state'] == 1){
                    $so = $this->orientation->getSubjectInOrientation($orientation,$materia['id']);
                    if($so && $so['state'] == 0){
                        $this->orientation->reAddSubject($orientation,$materia['id']);
                    }else{
                        $this->orientation->postSubjectInOrientation($orientation,$materia['id']);
                    }
                }
            }
        }else{
            return $this->res->error_403();
        }
    }



    public function getOrientations(){
        return $this->orientation->getOrientations();
    }

    public function getOrientationById(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
            return $this->orientation->getOrientationById($this->data['id']);
        }else{
            return $this->res->error_400();
        }
    }

    public function modifyOrientation(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['id'=>'is_int','name'=>'is_string','year'=>'is_int'])){
                return $this->orientation->putOrientation($this->data['id'],$this->data['name'],$this->data['year']);
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }

    public function deleteOrientation(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
                $rows = $this->orientation->deleteOrientation($this->data['id']);
                $grupos = $this->orientation->getOrientationGroups($this->data['id']);
                foreach($grupos as $g){
                    $this->group->deleteGroup($g['id']);
                    $this->group->removeAllTeachersFromGroup($g['id']);
                    $this->group->removeAllStudentsFromGroup($g['id']);
                    $this->group->closeAllQuerysInGroup($g['id']);
                }
                return $rows;
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }


    public function removeOrienationSubjects(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
                if(isset($this->data['subjects']) && parent::isArrayDataCorrect($this->data['subjects'],'is_int')){
                    foreach($this->data['subjects'] as $s){
                        $this->orientation->closeQuerysInSubjectOrientation($this->data['id'],$s);
                        $this->orientation->removeTeachersFromSubject($this->data['id'],$s);
                    }
                    return $this->orientation->deleteSubjectsInOrientation($this->data['id'],$this->data['subjects']);
                }else{
                    return $this->res->error_400();
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
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