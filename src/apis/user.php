<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

class UserAPI extends API{
    
    private $user;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->user = new UserModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'administrator'){
            if(!$this->isDataCorrect($data)){
                $datosArray = $this->res->error_400();
            }else{ 
                $exists = $this->userExists($data);
                if($exists == false){
                    $id = $this->user->postUser($data);
                    if($id != 'error'){
                        $type = $data['type'];
                        $type = $this->filterType($type);
                        if($type != 'error'){
                            $this->insertOptionalData($id,$data);
                            $datosArray = $this->user->setUserType($id,$type);
                            $datosArray = $this->user->patchUser($id,'state_account',1);
                        }else{
                            $datosArray = $this->res->error_400();
                        }
                    }else{
                        $datosArray = $this->res->OOPSIE();
                    }
                }else{
                    $datosArray = $exists;
                }
            }
            echo json_encode($datosArray);
        }else{
            echo json_encode($this->res->error_403());
        }
    }

    private function userExists($data){ 
        $array = $this->user->getUser($data['email']);
        if(count($array) > 0){
            return $this->res->error('Este email ya esta tomado');
        }else{
            $array = $this->user->getUser($data['nickname']);
            if(count($array) > 0){
                return $this->res->error('Este nickname ya esta tomado');
            }else{
                $array = $this->user->getUserByCi($data['ci']);
                if(count($array) > 0){
                    return $this->res->error('Esta cedula ya esta tomada');
                }else{
                    return false;
                }
            }
            
        }
    }
    private function insertOptionalData($id,$data){
        if(isset($data['middle_name'])){
            $this->user->patchUser($id,'middle_name',$data['middle_name']);
        }

        if(isset($data['second_surname'])){
            $this->user->patchUser($id,'second_surname',$data['second_surname']);
        }

        if(isset($data['avatar'])){
            $this->user->patchUser($id,'avatar',$data['avatar']);
        }
    }
    private function filterType($type){
        switch($type){
            //admin  no vo por que nadie se puede autoregistrar como admin
            case 'teacher':
                $type = 'teacher';
                break;
            case 'student':
                $type = 'student';
                break;
            default:
                $type = 'error';
                break;
        }
        return $type;
    }

    private function isObligatoryDataCorrect($data){
        if(    !isset($data['ci'])
            || strlen($data['ci']) != 8
            || empty($data['ci'])
            || !isset($data['name']) 
            || empty($data['name'])
            || !isset($data['surname']) 
            || empty($data['surname'])
            || !isset($data['email']) 
            || empty($data['email'])
            || !isset($data['nickname'])
            || empty($data['nickname'])
            || !isset($data['password'])
            || empty($data['password'])
            || !isset($data['type'])){
            //No existen
            return false;
        }else
            //Existen los campos obligatorios
            //Los compos estan  correctos?
            if(    !is_string($data['ci'])
                || !is_string($data['name'])
                || !is_string($data['surname'])
                || !$this->is_email($data['email'])
                || !is_string($data['nickname'])
                || !is_string($data['password'])
                || !is_string($data['type'])){
                    //No son correctos
                    return false;
                }else{
                    //Son correctos
                    return true;
                }
    }

    private function isOptionalDataCorrect($data){
        $status = true;
        //Cada chequeo es si se intento enviar infromacion opcional y en ese caso si la informacion esta bien
        if(isset($data['middle_name']) && !is_string($data['middle_name'])){
            $status = false;
        }

        if(isset($data['second_surname']) && !is_string($data['second_surname'])){
            $status = false;
        }

        if(isset($data['avatar']) && !is_string($data['avatar'])){
            $status = false;
        }

        return $status;
    }
    private function isDataCorrect($data){
        if(!$this->isObligatoryDataCorrect($data) || !$this->isOptionalDataCorrect($data)){
            return false;
        }else{
            return true;
        }
    }
    private function setUserType($id,$type){
        if($type == 'teacher'){

        }elseif($type == 'student'){

        }
    }

    private function is_email($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            $datosArray = $this->user->getAllUsers();
            echo json_encode($datosArray);
        }else{
            //HAY QUE CAMBIARLO PARA PODES PEDIR OTROS USUARIOS
            $datosArray = $this->user->getUserByIdSafe($token->user_id);
            echo json_encode($datosArray);
            //echo json_encode($this->res->error_403());
        }
    }

    public function PUT($token,$data){
        if($token->user_type == 'administrator'){
            //TODO
        }else{
            //me aseguro de que el id esta bien
            if(parent::isTheDataCorrect($data,['avatar'=>'is_string','nickname'=>'is_string'])){
                $this->user->patchUser($token->user_id,'avatar',$data['avatar']);
                $this->user->patchUser($token->user_id,'nickname',$data['nickname']);
                $datosArray = 1;
            }else{
                $datosArray = $this->res->error_400();
            }
            echo json_encode($datosArray);
        }
    }

    public function DELETE($token,$data){
        if($token->user_type == 'administrator'){
            //TODO
        }else{
            // me aseguro que que quiere modificarse a si mismo
            //me aseguro de que el id esta bien
            $datosArray = $this->user->patchUser($token->user_id,'state_account',0);
            echo json_encode($datosArray);
        }
    }

}

