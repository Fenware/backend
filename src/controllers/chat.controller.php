<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/chat.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear chats
*/
class ChatController extends Controller{
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
        parent::__construct();
    }



    public function createChat(){
        if($this->token->user_type == 'student'){
            if(parent::isTheDataCorrect($this->data,['materia'=>'is_int','asunto'=>'is_string'])){
                $student_group = $this->user->getUserGroups($this->token->user_id,'student');
                $grupo = $student_group[0]['id_group'];
                $teacher = $this->subject->getTeacherFromSubjectInGroup($this->data['materia'],$grupo);
                if(is_int($teacher)){
                    $max_rooms_per_gs = $this->user->getMaxRoomsPerGs($teacher);
                    $active_rooms = $this->chat->amountOfActiveChatsFromSubjecGroup($this->data['materia'],$grupo);
                    if($max_rooms_per_gs > $active_rooms){
                        $this->chat->setStudent($this->token->user_id);
                        $this->chat->setTeacher($teacher);
                        $this->chat->setGroup($grupo);
                        $this->chat->setSubject($this->data['materia']);
                        $this->chat->setTheme($this->data['asunto']);
                        $chat = $this->chat->createQuery();
                        if($chat != 0){
                            $this->chat->createChat($chat[0]['id']);
                            return $this->chat->getChatById($chat[0]['id']);
                        }else{
                            return $this->res->error_500();
                        }
                    }else{
                        return $this->res->error('Ya hay demasiadas salas de chat abiertas en esta materia',1080);
                    }
                    
                }else{
                    //si no es un  numero entonces capte un error 
                    return $this->res->error($teacher);
                }
                
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }
    

    public function getActiveChats(){
        return $this->chat->getChatsFromUser($this->token->user_id,$this->token->user_type);
    }

    public function getChatById(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['chat'=>'is_int'])){
                return $this->chat->getChatById($this->data['chat']);
            }else{
                return $this->res->error_400();
            }
        }else{
            if(parent::isTheDataCorrect($this->data,['chat'=>'is_int'])){
                if($this->user->UserHasAccesToChat($this->token->user_id,$this->data['chat'])){
                    return $this->chat->getChatById($this->data['chat']);
                }else{
                    return  $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }
    }

    public function getAllChats(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['user'=>'is_int'])){
                $type = $this->user->getUserType($this->data['user']);
                if($type != 'administrator' ){
                    return $this->chat->getAllChatsFromUser($this->data['user'],$type);
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->chat->getAllChatsFromUser($this->token->user_id,$this->token->user_type);
        }
    }


    public function closeChat(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['chat'=>'is_int'])){
                $this->chat->closeQuery($this->data['chat']);
            }else{
                return $this->res->error_400();
            }
        }else{
            if(parent::isTheDataCorrect($this->data,['chat'=>'is_int'])){
                if($this->chat->userHasHighAccessToChat($this->token->user_id,$this->data['chat'])){
                    return $this->chat->closeQuery($this->data['chat']);
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }
        
    }

    public function postMessage(){
        if($this->token->user_type == 'administrator'){
            return $this->res->error_403();
        }else{
            if(parent::isTheDataCorrect($this->data,['chat'=>'is_int','msg'=>'is_string'])){
                if($this->user->UserHasAccesToChat($this->token->user_id,$this->data['chat'])){
                    $chat = $this->chat->getChatById($this->data['chat']);
                    if($chat['state' != 0]){
                        $msg =  $this->chat->postMessagge($this->token->user_id,$this->data['chat'],$this->data['msg']);
                        if($this->token->user_type == 'teacher'){
                            $this->chat->setQueryToAnswered($this->data['chat']);
                        }
                        return $msg;
                    }else{
                        return $this->res->error('No puedes enviar mensajes a chats cerrados',1095);
                    }
                }else{
                    return $this->res->error_403();
                }
            }else{
                return $this->res->error_400();
            }
        }
        
    }

    public function getMessages(){
        if(parent::isTheDataCorrect($this->data,['chat'=>'is_string'])){
            if($this->token->user_type == 'administrator'){
                return $this->consulta->getMessageFromQuery($this->data['chat']);
            }else{
                if($this->user->UserHasAccesToChat($this->token->user_id,$this->data['chat'])){
                    return $this->consulta->getMessageFromQuery($this->data['chat']);
                }else{
                    return $this->res->error_403();
                }
            }
        }else{
            return $this->res->error_400();
        }
    }

    public function addParticipant(){
        if($this->token->user_type == 'administrator'){
            return $this->res->error_403();
        }else{
            if(parent::isTheDataCorrect($this->data,['chat'=>'is_string'])){
                return $this->chat->addParticipant($this->data['chat'],$this->token->user_id);
            }else{
                return $this->res->error_400();
            }
        }
    }


}