<?php

include_once 'core/api.php';
include_once 'core/iAPI.php';
include_once 'model/materia.model.php';
include_once 'core/response.php';

class MateriaAPI extends API implements iAPI{
    
    private $materia;
    private $res;
    function __construct()
    {
        $this->res = new Response();
        $this->materia = new MateriaModel();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->POST();
        }elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
            $this->GET();
        }elseif($_SERVER['REQUEST_METHOD'] == 'PUT'){
            $this->PUT();
        }elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'){
            $this->DELETE();
        }else{
            header('Content-Type: applicaton/json');
            $datosArray = $this->res->error_405();
            echo json_encode($datosArray);
        }
    }

    public function POST(){
        $token = parent::checkToken($this->res);
        //Chequeo que el token sea valido
        if (!parent::validToken($token)){
            //Token invalido
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }else{
            //Todo correcto/Haga el trabajo
            if($token->user_type == 'administrador'){
                //$datosArray = $this->user->getAllUsers();
                $postBody = file_get_contents('php://input');
                $data = json_decode($postBody,true);
                if(isset($data['nombre'])){
                    $rows = $this->materia->postMateria($data['nombre']);
                    if($rows){
                        http_response_code(200);
                        
                    }else{
                        http_response_code(500);
                    }
                    $datosArray = $rows;
                }else{
                    $datosArray = $this->res->error_400();
                }
                echo json_encode($datosArray);
            }else{
                echo json_encode($this->res->error('No tienes los permisos para acceder a este recurso'));
            }
            
        }
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
            //TODO
            
        }
    }

    public function PUT(){

    }

    public function DELETE(){

    }

}

