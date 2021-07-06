<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class OrientacionSubjectAPI extends API{
    private $res;
    private $orientation;
    function __construct()
    {
        $this->res = new Response();
        $this->orientation = new OrientationModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'administrator'){
            
            if(!isset($data['id']) || !isset($data['subjects'])){
                echo json_encode($this->res->error_400());
            }else{
                //Exists id  and subjects
                if(!is_int($data['id']) || !is_array($data['subjects'])){
                    echo json_encode($this->res->error_400());
                }else{
                    //id is a  number and subjects an  array
                    $valid = parent::isArrayDataCorrect($data['subjects'],'is_int');
                    if(!$valid){
                        //Datos invalidos
                        $datosArray = $this->res->error_400();
                    }else{
                        $datosArray = $this->orientation->postSubjectsInOrientation($data['id'],$data['subjects']);
                    }
                }
            }
            echo json_encode($datosArray);
        }
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($data,['id'=>'is_string'])){
                $datosArray = $this->orientation->getOrientationSubjects($data['id']);
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
        
    }

    public function PUT($token,$data){
        exit;
    }   

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            
            if(!isset($data['id']) || !isset($data['subjects'])){
                echo json_encode($this->res->error_400());
            }else{
                //Exists id  and subjects
                if(!is_int($data['id']) || !is_array($data['subjects'])){
                    echo json_encode($this->res->error_400());
                }else{
                    //id is a  number and subjects an  array
                    $valid = parent::isArrayDataCorrect($data['subjects'],'is_int');
                    if(!$valid){
                        //Datos invalidos
                        $datosArray = $this->res->error_400();
                    }else{
                        $datosArray = $this->orientation->deleteSubjectsInOrientation($data['id'],$data['subjects']);
                    }
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

}


