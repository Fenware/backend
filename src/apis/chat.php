<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/chat.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear chats
*/
class ChatAPI extends API{
    private $res;
    private $chat;
    private $user;
    private $group;
    private $subject;
    function __construct()
    {
        $this->group = new GroupModel();
        $this->subject = new SubjectModel();
        $this->res = new Response();
        $this->user = new UserModel();
        $this->chat = new ChatModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'student'){
            if(parent::isTheDataCorrect($data,['materia'=>'is_int','asunto'=>'is_string'])){
                $student_group = $this->user->getUserGroups($token->user_id,'student');
                $grupo = $student_group[0]['id_group'];
                $teacher = $this->subject->getTeacherFromSubjectInGroup($data['materia'],$grupo);
                if(is_int($teacher)){
                    $this->chat->setStudent($token->user_id);
                    $this->chat->setTeacher($teacher);
                    $this->chat->setGroup($grupo);
                    $this->chat->setSubject($data['materia']);
                    $this->chat->setTheme($data['asunto']);
                    $chat = $this->chat->createQuery();
                    $datosArray = $this->chat->createChat($chat[0]['id']);
                    $context = new ZMQContext();
                    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
                    $socket->connect("tcp://localhost:5555");
                    $entryData = array(
                        'category' => $student_group[0]['id_group'],
                        'chat' => $chat[0]
                    );
                    $socket->send(json_encode($entryData));
                }else{
                    //si no es un  numero entonces capte un error 
                    $datosArray = $this->res->error($teacher);
                }
                
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }
    
    public function GET($token,$data){
        if($token->user_type == 'administrator'){
            $datosArray = $this->res->error('Work In Progress');
        }else{
            if(parent::isTheDataCorrect($data,['chat'=>'is_string'])){
                if($this->user->UserHasAccesTochat($token->user_id,$data['chat'])){
                    $datosArray = $this->chat->getQueryById($data['chat']);
                }else{
                    $datosArray = $this->res->error_403();
                }
            }else{
                if(isset($data['all'])){
                    $datosArray = $this->chat->getAllChatsFromUser($token->user_id,$token->user_type);
                }else{
                    $datosArray = $this->chat->getChatsFromUser($token->user_id,$token->user_type);
                }
                
            }
        }
        
        echo json_encode($datosArray);
    }

    public function PUT($token,$data){
        
    }

    public function DELETE($token,$data){
        if($token->user_type == 'student'){
            if(parent::isTheDataCorrect($data,['chat'=>'is_int'])){
                $access = $this->user->StudentIsAutorOfQuery($token->user_id,$data['chat']);
                if($access){
                    $datosArray = $this->chat->closeQuery($data['chat']);
                    //TODO push message to chat so all user now 
                }else{
                    $datosArray = $this->res->error_403();
                }
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }

}