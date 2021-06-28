<?php

include_once 'core/api.php';
include_once 'model/orientation.model.php';
include_once 'core/response.php';

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
            if(!isset($data['name']) || !isset($data['year']) || !isset($data['subjects'])){
                $datosArray = $this->res->error_400();
            }else{
                //Check data state 
                if(!is_string($data['name']) || !is_int($data['year']) || !is_array($data['subjects'])){
                    //Data  is not correct
                    $datosArray = $this->res->error_400();
                }else{
                    //check if all values in subjects are numbers
                    $count = count($data['subjects']);
                    $valid = true;
                    //Loop the array searching for items that are not numbers
                    for($i = 0;$i < $count; $i++){
                        if(!is_int($data['subjects'][$i])){
                           //The item was not a number
                           $valid = false;
                        }
                    }
                    if(!$valid){
                        //Datos invalidos
                        $datosArray = $this->res->error_400();
                    }else{
                        //Datos validos
                        
                        $rows = $this->orientation->postOrientation($data['name'],$data['year'],$data['subjects'],$count);
                        //If rows > 0 it means that everything went right
                        if($rows > 0){
                            $datosArray = $rows;
                        }else{
                            //Something wrong happend during postOrientation()
                            $datosArray = $this->res->error('Something went wrong in the server');
                        }
                    }
                    
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }

    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            if(isset($data['id'])){
                if(isset($data['subjects'])){
                    $datosArray = $this->orientation->getOrientationSubjects($data['id']);
                }else{
                    $datosArray = $this->orientation->getOrientationById($data['id']);
                }
            }elseif(isset($data['name'])){
                $datosArray = $this->orientation->getOrientationByName($data['name']);
            }else{
                $datosArray = $this->orientation->getOrientations();
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }

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
                        if(isset($data['subjects'])){
                            $count = count($data['subjects']);
                            $valid = true;
                            //Loop the array searching for items that are not numbers
                            for($i = 0;$i < $count; $i++){
                                if(!is_int($data['subjects'][$i])){
                                   //The item was not a number
                                   $valid = false;
                                }
                            }
                            if(!$valid){
                                $datosArray = $this->res->error_400();
                            }else{

                            }
                        }   
                    }
                    
                    $datosArray = $this->orientation->putOrientation($data['id'],$data['name'],$data['year']);
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            
        }else{
            echo json_encode($this->res->error_403());
        }
    }
    
}