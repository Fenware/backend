<?php

declare(strict_types=1);
use Firebase\JWT\JWT;
require_once '/var/www/html/vendor/autoload.php';
include_once '/var/www/html/core/model.php';
include_once '/var/www/html/model/user.model.php';
include_once '/var/www/html/core/response.php';


/*
Modelo para autenticar logins
Al logearse de forma exitosa este genera el token para el usuario
*/
class AuthModel extends Model{

    private $user;
    private $res;
    function __construct()
    {
        parent::__construct();
        $this->res = new Response();
    }

    public function login($json){
        $res = new Response();
        $datos = json_decode($json,true);
        if(!isset($datos['user']) || !isset($datos['password']) || !isset($datos['type'])){
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
                echo $type;
                return $res->OOPSIE();
            }else{
                
                if($data){
                    //Chequeo si el usuario esta activo {0:inactivo;1:activo;2:pendiente}
                    if($data[0]['state_account'] == 1){
                        //Si la contraseña del usuario en la base de datos es igual a la que me mando el usuario
                        if(password_verify($this->user->getPassword(),$data[0]['password'])){
                            $userType = $this->user->checkUserType($data[0]['id'],$type);
                            if($userType){
                                $token = $this->generateToken($data[0]['id'],$type);
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

    function generateToken($user,$type){
        $res = new Response();
        $secret_key = SECRET_KEY;
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+8 hour')->getTimestamp();
        $serverName = URL;
        $user_id  = $user;  

        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,                           // Expire
            'user_id' => $user_id, 
            'user_type' => $type                    // User name
        ];

        try {
            $jwt =  JWT::encode(
                $data,
                $secret_key,
                'HS512'
            );
        } catch (\Throwable $th) {
            $jwt = $res->OOPSIE();
            print_r($res->OOPSIE());
        }
        return $jwt;

    }
}