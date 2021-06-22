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
        if(!isset($datos['user']) || !isset($datos['password'])){
            //Faltan datos
            return $res->error_400();
        }else{
            //Datos completos
            $userLogin = $datos['user'];
            $this->user = new UserModel();
            $this->user->setPassword($datos['password']);
            $data = $this->user->getUser($userLogin);

            if($datos){
                
                //Chequeo si el usuario esta activo {0:inactivo;1:activo;2:pendiente}
                if($data[0]['estado_cuenta'] == 1){
                    //Si la contraseña del usuario en la base de datos es igual a la que me mando el usuario
                    if(password_verify($this->user->getPassword(),$data[0]['password'])){
                        return $this->generateToken($data[0]['ci']);
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

    public function register($json){

    }

    function generateToken($user){
        $res = new Response();
        $secret_key = SECRET_KEY;
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+8 hour')->getTimestamp();
        $serverName = URL;
        $username   = $user;      

        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,                           // Expire
            'userName' => $username,                     // User name
        ];

        try {
            $jwt =  JWT::encode(
                $data,
                $secret_key,
                'HS512'
            );
        } catch (\Throwable $th) {
            $jwt = $res->OOPSIE();
        }
        return $jwt;

    }
}