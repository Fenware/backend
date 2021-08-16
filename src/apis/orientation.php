<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear orientaciones
*/
class OrientacionAPI extends API{
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
            if(!$this->isPostDataCorrect($data)){
                $datosArray = $this->res->error_400();
            }else{
                    //check if all values in subjects are numbers
                $valid = parent::isArrayDataCorrect($data['subjects'],'is_int');
                if(!$valid){
                    //Datos invalidos
                    $datosArray = $this->res->error_400();
                }else{
                    //Datos validos
                    $orientacion = $this->orientation->postOrientation($data['name'],$data['year'],$data['subjects']);
                    //Si devuelvo un string es por que hubo un error
                    if(!is_string($orientacion)){
                        $datosArray = $orientacion;
                    }else{
                        //Something wrong happend during postOrientation()
                        $datosArray = $this->res->error($orientacion);
                    }
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }

    }


    private function isPostDataCorrect($data){
        return parent::isTheDataCorrect($data,['name' => 'is_string',
                                               'year' => 'is_int',
                                               'subjects' => 'is_array']);
    }

    public function GET($token,$data){
        //El id solo nos llega por string
        if(parent::isTheDataCorrect($data,['id'=>'is_string'])){
            $datosArray = $this->orientation->getOrientationById($data['id']);
        }elseif(parent::isTheDataCorrect($data,['name'=>'is_string'])){
            $datosArray = $this->orientation->getOrientationByName($data['name']);
        }else{
            $datosArray = $this->orientation->getOrientations();
        }
        echo json_encode($datosArray);

    }

    public function PUT($token,$data){
        if($token->user_type == 'administrator'){
            //All the necesary inputs sent?
            if(!isset($data['id'])){
                //No :/
                $datosArray = $this->res->error_400();
            }else{
                //Yes :D
                //Is the id a number?
                if(!is_int($data['id'])){
                    //No :/
                    $datosArray = $this->res->error_400();
                }else{
                    //Yes :D
                    //Exists name o year?
                    if(isset($data['name']) && isset($data['year'])){
                        //Yes :D
                        //Is the information correct?
                        if(is_string($data['name']) && is_int($data['year'])){
                            //Yes
                            $datosArray = $this->orientation->putOrientation($data['id'],$data['name'],$data['year']);
                            
                        }else{
                            //Is the information correct? No :/
                            $datosArray = $this->res->error_400();
                        }
                    }else{
                        //Exists the array of subjects?
                        $datosArray = $this->res->error_400();
                    }
                    
                    echo json_encode($datosArray);
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($data,['id'=>'is_int'])){
                $result = $this->orientation->deleteOrientation($data['id']);
                if(is_int($result)){
                    $datosArray = $result;
                }else{
                    $datosArray = $this->res->error($result);
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