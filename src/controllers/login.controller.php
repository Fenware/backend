<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/model/controller.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");
header('Content-type: application/json');
/*
Api para logearse
*/
class LoginController extends Controller{
    private $auth;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->auth = new AuthModel();
        parent::__construct();
    }

    //LOGIN - IN POST FOR SECURITY

    public function login(){
        $res = new Response();
        $datos = $this->data;
        if(!parent::isTheDataCorrect($datos , ['user'=>'is_string','password'=>'is_string','type'=>'is_string'] )){
            //Faltan datoss
            return $res->error_400();
        }else{
            //Datos completos
            $userLogin = $datos['user'];
            $this->user = new UserModel();
            $this->user->setPassword($datos['password']);
            $data = $this->user->getUser($userLogin);
            
            switch($datos['type']){
                case 'admin':
                    $type = 'administrator';
                    break;
                case 'student':
                    $type = 'student';
                    break;
                case 'teacher':
                    $type = 'teacher';
                    break;
                default:
                    $type = 'error';
                    break;
            }
            if($type == 'error'){
                return $res->OOPSIE();
            }else{
                
                if($data){
                    //Chequeo si el usuario esta activo {0:inactivo;1:activo;2:pendiente}
                    if($data[0]['state_account'] == 1){
                        //Si la contraseña del usuario en la base de datos es igual a la que me mando el usuario
                        if(password_verify($this->user->getPassword(),$data[0]['password'])){
                            $userType = $this->user->checkUserType($data[0]['id'],$type);
                            if($userType){
                                $token = $this->auth->generateToken($data[0]['id'],$type);
                                if($token){
                                    $result = $res->response;
                                    $result['result'] = array(
                                        'token' => $token
                                    );
                                    return $result;
                                }
                            }else{
                                return $res->error('El usuario no es un '.$type,1004);
                            }
                            
                        }else{
                            return $res->error('Contraseña Incorrecta',1003);
                        }
                    }else{
                        if($data[0]['state_account'] == 2){
                            return $res->error('Tu cuenta aun no fue aceptada por un administrador',1001);
                        }else{
                            return $res->error('El usuario no existe',1002);
                        }
                    }
                }else{
                    return $res->error('El usuario no existe',1002);
                }
            }
            
        }
    }
    
    

}