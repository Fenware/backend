<?php

include_once 'core/api.php';
include_once 'core/iAPI.php';
include_once 'model/user.model.php';
include_once 'core/response.php';

class UserAPI extends API{
    
    private $user;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->user = new UserModel();
        parent::__construct($this->res);
    }

    public function POST(){
        //TODO
    }

    public function GET(){
        $token = parent::checkToken($this->res);
        //Chequeo que el token sea valido
        if (!parent::validToken($token)){
            //Token invalido
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }else{
            //Todo correcto/Haga el trabajo
            $postBody = file_get_contents('php://input');
            if($token->user_type == 'administrador'){
                $datosArray = $this->user->getAllUsers();
                echo json_encode($datosArray);
            }else{
                echo json_encode($this->res->error('No tienes los permisos para acceder a este recurso'));
            }
        }
    }

    public function PUT(){

    }

    public function DELETE(){

    }

}

