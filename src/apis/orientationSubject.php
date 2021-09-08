<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para agregar materias a una orientacion
*/
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
            if(parent::isTheDataCorrect($data,['id'=>'is_int','subjects'=>'is_array'])){
                $valid = parent::isArrayDataCorrect($data['subjects'],'is_int');
                if(!$valid){
                    //Datos invalidos
                    $datosArray = $this->res->error_400();
                }else{
                    $datosArray = $this->orientation->postSubjectsInOrientation($data['id'],$data['subjects']);
                }
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }
    }

    public function GET($token,$data){
        if(parent::isTheDataCorrect($data,['id'=>'is_string'])){
            $datosArray = $this->orientation->getOrientationSubjects($data['id']);
        }else{
            $datosArray = $this->res->error_400();
        }
        echo json_encode($datosArray);
        
    }

    public function PUT($token,$data){
        exit;
    }   

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($data,['id'=>'is_int','subjects'=>'is_array'])){
                //id is a  number and subjects an  array
                $valid = parent::isArrayDataCorrect($data['subjects'],'is_int');
                if(!$valid){
                    //Datos invalidos
                    $datosArray = $this->res->error_400();
                }else{
                    $datosArray = $this->orientation->deleteSubjectsInOrientation($data['id'],$data['subjects']);
                }
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

}


