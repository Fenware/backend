<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
/*
API para auto registrarse
*/
class SelfRegisterAPI{
    
    private $user;
    private $res;
    function __construct()
    {

        $this->user = new UserModel();
        //obtengo el body
        $data = file_get_contents('php://input');
        //lo convierto de json a array
        $data = json_decode($data,true);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->POST($data);
        }elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
            echo json_encode($this->res->error_405());
        }elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
            echo json_encode($this->res->error_405());
        }elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
            echo json_encode($this->res->error_405());
        }else{
            header('Content-Type: applicaton/json');
            $datosArray = $this->res->error_405();
            echo json_encode($datosArray);
        }
    }
        
    

    //Creo el usuario
    private function POST($data){
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
                $array = $this->user->getUserByCiSafe($data['ci']);
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


    private function is_email($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

}
