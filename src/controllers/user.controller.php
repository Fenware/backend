<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/query.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear manejar usuarios
*/
class UserController extends Controller{
    
    private $user;
    private $res;
    private $query;
    function __construct($token)
    {
        $this->res = new Response();
        $this->user = new UserModel();
        $this->query = new QueryModel();
        parent::__construct($token);
    }

    public function createUser(){
        if(!$this->isDataCorrect($this->data)){
            $datosArray = $this->res->error_400();
        }else{
            $type = $this->filterType($this->data['type']);
            if($type != 'error'){
                $exists = $this->userExists($this->data);
                if($exists == false){
                    $id = $this->user->postUser($this->data);
                    if($id != 'error'){
                        $this->insertOptionalData($id,$this->data);
                        $this->user->setUserType($id,$type);
                        if($this->token && $this->token->user_type == 'administrator'){
                            $this->user->patchUser($id,'state_account',1);
                        }
                        return 1;
                    }else{
                        return $this->res->error_500();
                    }
                }else{
                    return $exists;
                }
            }else{
                return $this->res->error_403();
            }
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
            || strlen($data['password']) < 8
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


    public function getActiveUsers(){
        if($this->token->user_type == 'administrator'){
            return $this->user->getAllUsers();
        }else{
            return $this->res->error_403();
        }
    }

    public function getUserById(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data, ['user'=>'is_int'] )){
                $type = $this->user->getUserType($this->data['user']);
                if($type != 'administrator'){
                    $user = $this->user->getUserByIdSafe($this->data['user']);
                    if($user){
                        $user['type'] = $this->user->getUserType($this->data['user']);
                    }
                    return $user;
                }else{
                    return $this->res->error_403(); 
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            $user = $this->user->getUserByIdSafe($this->token->user_id);
            if($this->token->user_type == 'teacher'){
                $user['max_rooms_per_gs'] = $this->user->getMaxRoomsPerGs($this->token->user_id);
            }
            return $user;
        }
    }
    

    //Modifico a un usuario
    public function modifyUser(){
        if($this->token->user_type == 'administrator'){
            //Esto es largo asi q lo mando aca
            return $this->administradorEditaUsuario($this->data);
        }else{
            //El usuario no es un administradors
            if(parent::isTheDataCorrect($this->data,['time'=>'is_string'])){
                $this->user->actualizeLastConnectionTime($this->token->user_id);
            }
            if(parent::isTheDataCorrect($this->data,['avatar'=>'is_string','nickname'=>'is_string'])){
                $this->user->patchUser($this->token->user_id,'avatar',$this->data['avatar']);
                $this->user->patchUser($this->token->user_id,'nickname',$this->data['nickname']);
                return "";
            }elseif($this->token->user_type == 'teacher'){
                if(parent::isTheDataCorrect($this->data,['max_rooms_per_gs'=>'is_int'])){
                    return $this->user->setMaxRoomsPerGs($this->token->user_id,$this->data['max_rooms_per_gs']);
                }else{
                    return $this->res->error_400();
                }
            }else{
                return $this->res->error_400();
            }
        }
    }
    private function administradorEditaUsuario($data){
        if(parent::isTheDataCorrect($data,['user'=>'is_int'])){
            $type = $this->user->getUserType($data['user']);
            //Me aseguro de que el usuario que quiero modificar no sea un administrador
            if($type != 'administrator'){
                $rows = 0;
                if(parent::isTheDataCorrect($data,['name'=>'is_string'])){
                    $rows = $this->user->patchUser($data['user'],'name',$data['name']);
                }
                if(parent::isTheDataCorrect($data,['middle_name'=>'is_string'])){

                    $rows = $this->user->patchUser($data['user'],'middle_name',$data['middle_name']);
                    
                }
                if(parent::isTheDataCorrect($data,['surname'=>'is_string'])){

                    $rows = $this->user->patchUser($data['user'],'surname',$data['surname']);

                }
                if(parent::isTheDataCorrect($data,['second_surname'=>'is_string'])){

                    $rows = $this->user->patchUser($data['user'],'second_surname',$data['second_surname']);

                }
                if(parent::isTheDataCorrect($data,['email'=>'is_string']) && $this->is_email($data['email']) ){

                    $rows = $this->user->patchUser($data['user'],'email',$data['email']);

                }

                if(parent::isTheDataCorrect($data,['avatar'=>'is_string'])){

                    $rows = $this->user->patchUser($data['user'],'email',$data['email']);

                }

                if(parent::isTheDataCorrect($data,['nickname'=>'is_string'])){

                    $rows = $this->user->patchUser($data['user'],'nickname',$data['nickname']);

                }
                return $rows;
            }else{
                return $this->res->error_403();
            }
            
        }
    }


    //Borro a un usuario
    public function deleteUser(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['user'=>'is_int'])){
                $type = $this->user->getUserType($this->data['user']);
                if($type != 'administrator'){
                    $rows = $this->user->removeUser($this->data['user'],$type);
                    $this->query->closeAllUserQuerys($this->data['user']);
                    return $rows;

                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            // me aseguro que que quiere modificarse a si mismo
            return $this->user->removeUser($this->token->user_id,$this->token->user_type);
        }
    }


    public function acceptUser(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
                $type = $this->user->getUserType($this->data['id']);
                if($type != 'administrator'){
                    
                    return $this->user->patchUser($this->data['id'],'state_account',1);
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }

    public function getPendantUsers(){
        if($this->token->user_type == 'administrator'){
            $users = $this->user->getPendentUsers();
            foreach($users as &$user){
                $type = $this->user->getUserType($user['id']);
                $user['type'] = $type;
            }
            return $users;
        }else{
            return $this->res->error_403();
        }
    }


    public function rejectUser(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
                $type = $this->user->getUserType($this->data['id']);
                if($type != 'administrator'){
                    return $this->user->patchUser($this->data['id'],'state_account',0);
                }else{
                    return $this->res->error_403();
                }
            }
        }else{
            return $this->res->error_403();
        }
    }

}

