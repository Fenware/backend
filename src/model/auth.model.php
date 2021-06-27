<?php

declare(strict_types=1);
use Firebase\JWT\JWT;
require_once 'vendor/autoload.php';
include_once 'core/model.php';
include_once 'model/user.model.php';
include_once 'core/response.php';


class AuthModel extends Model{

    private $user;
    function __construct()
    {
        parent::__construct();
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
                    $type = 'administrador';
                    break;
                case 'alumno':
                    $type = 'alumno';
                    break;
                case 'docente':
                    $type = 'docente';
                    break;
                default:
                    $type = 'error';
                    break;
            }
            if($type == 'error'){
                echo $type;
                return $res->OOPSIE();
            }else{
                
                if($datos){
                    //Chequeo si el usuario esta activo {0:inactivo;1:activo;2:pendiente}
                    if($data[0]['state_account'] == 1){
                        //Si la contraseña del usuario en la base de datos es igual a la que me mando el usuario
                        if(password_verify($this->user->getPassword(),$data[0]['password'])){
                            $userType = $this->user->getUserType($data[0]['id'],$type);
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
                                return $res->error('El usuario no es '.$type);
                            }
                            
                        }else{
                            return $res->error('Contrasenna Incorrecta');
                        }
                    }else{
                        return $res->error('El usuario no existe');
                    }
                }else{
                    return $res->error('El usuario no existe');
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