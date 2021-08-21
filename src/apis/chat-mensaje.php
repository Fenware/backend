<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/chat.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para enviar mensajes a una consulta
*/
class ChatMessageAPI extends API{
    private $res;
    private $consulta;
    private $group;
    private $user;
    function __construct()
    {
        $this->group = new GroupModel();
        $this->res = new Response();
        $this->user = new UserModel();
        $this->consulta = new ChatModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if(parent::isTheDataCorrect($data,['chat'=>'is_int','msg'=>'is_string'])){
            if($this->user->UserHasAccesToChat($token->user_id,$data['chat'])){
                $datosArray = $this->consulta->postMessagge($token->user_id,$data['chat'],$data['msg']);

                $context = new ZMQContext();
                $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
                $socket->connect("tcp://localhost:5556");
                $entryData = array(
                    'category' => $data['chat'],
                    'msg' => $datosArray
                );
                $socket->send(json_encode($entryData));

                if($token->user_type == 'teacher'){
                    $this->consulta->setQueryToAnswered($data['chat']);
                }
            }else{
                $datosArray = $this->res->error_403();
            }
        }else{
            $datosArray = $this->res->error_400();
        }
        echo json_encode($datosArray);
    }
    
    public function GET($token,$data){
        if(parent::isTheDataCorrect($data,['chat'=>'is_string'])){
            if($this->user->UserHasAccesToChat($token->user_id,$data['chat'])){
                $datosArray = $this->consulta->getMessageFromQuery($data['chat']);
            }else{
                $datosArray = $this->res->error_403();
            }
        }else{
            $datosArray =$this->res->error_400();
        }
        echo json_encode($datosArray);
    }

    public function PUT($token,$data){
        //Nothing here
    }

    public function DELETE($token,$data){
        //Nothing here
    }

}