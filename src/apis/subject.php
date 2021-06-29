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
            if(isset($data['name'])){
                $name = $data['name'];
                $rows = $this->materia->postSubject($name);
                if($rows>=1){
                    http_response_code(200);   
                }else{
                    //http_response_code(500);
                }
                $datosArray = $rows;
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            if(isset($data['id'])){
                $id = $data['id'];
                $datosArray = $this->materia->getSubjectById($id);
            }elseif(isset($data['name'])){
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

    public function PUT($token,$data){
        if($token->user_type == 'administrator'){
            //$datosArray = $this->user->getAllUsers();
            if(isset($data['id']) && isset($data['name'])){
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

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            //$datosArray = $this->user->getAllUsers();s
            if(isset($data['id'])){
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

}

