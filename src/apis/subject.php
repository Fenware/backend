<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class SubjectAPI extends API{
    
    private $materia;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->materia = new SubjectModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'administrator'){
            if($this->isPostDataCorrect($data)){
                $name = $data['name'];
                $id = $this->materia->postSubject($name);
                $datosArray = $id;
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    private function isPostDataCorrect($data){
        return parent::isTheDataCorrect($data,['name' => 'is_string']);
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            if($this->isGetDataCorrectId($data)){
                $id = $data['id'];
                $datosArray = $this->materia->getSubjectById($id);
            }elseif($this->isGetDataCorrectName($data)){
                $name = $data['name'];
                $datosArray = $this->materia->getSubjectByName($name);
            }else{
                $datosArray = $this->materia->getSubjects();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error('No tienes los permisos para acceder a este recurso'));
        }
    }

    private function isGetDataCorrectId($data){
        return parent::isTheDataCorrect($data,['id' => 'is_int']);
    }

    private function isGetDataCorrectName($data){
        return parent::isTheDataCorrect($data,['name' => 'is_string']);
    }

    public function PUT($token,$data){
        if($token->user_type == 'administrator'){
            //$datosArray = $this->user->getAllUsers();
            if($this->isPutDataCorrect($data)){
                $id = $data['id'];
                $name = $data['name'];
                $rows = $this->materia->putSubject($id,$name);
                $datosArray = $rows;
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
             echo json_encode($this->res->error('No tienes los permisos para acceder a este recurso'));
        }
    }

    private function isPutDataCorrect($data){
        return parent::isTheDataCorrect($data,
        ['id' => 'is_int',
         'name' => 'is_string']);
    }
    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            //$datosArray = $this->user->getAllUsers();s
            if($this->isDeleteDataCorrectId($data)){
                $id = $data['id'];
                $rows = $this->materia->deleteSubject($id);
                if($rows>=1){
                    http_response_code(200);    
                }else{
                    // http_response_code(500);
                }
                $datosArray = $rows;
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error('No tienes los permisos para acceder a este recurso'));
        }
    }

    private function isDeleteDataCorrectId($data){
        return parent::isTheDataCorrect($data,['id' => 'is_int']);
    }

}

