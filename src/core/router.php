<?php

require_once '/var/www/html/controllers/login.controller.php';
require_once '/var/www/html/controllers/chat.controller.php';
require_once '/var/www/html/controllers/consulta.controller.php';
require_once '/var/www/html/controllers/group.controller.php';
require_once '/var/www/html/controllers/orientation.controller.php';
require_once '/var/www/html/controllers/schedule.controller.php';
require_once '/var/www/html/controllers/subject.controller.php';
require_once '/var/www/html/controllers/user.controller.php';
require_once '/var/www/html/controllers/user.group.controller.php';

require_once '/var/www/html/core/middleware.php';

require_once '/var/www/html/core/response.php';

header("Access-Control-Allow-Origin: *");//Cambiar el * por el dominio del frontend
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");//Solo admito el metodo POST
header('Content-type: application/json');
class Router{

    private $url;
    private $token;
    private $res;
    public function __construct()
    {
        $this->res = new Response();
        $middleware = new Middleware();
        //URL 
        $this->url = $_GET['url'];
        $this->url = rtrim($this->url,'/');
        $this->url = explode('/',$this->url);
        //Valido token
        try {
            $this->token = $middleware->validate();
            try {
                echo json_encode( $this->route($this->url) );
            } catch (PDOException $e) {
                echo json_encode( $this->res->error_NO_DB() );
            }
            
        } catch (Exception $e) {
            switch($e->getMessage()){
                case 'Metodo no permitido':
                    echo json_encode( $this->res->error_405() );
                    break;
                case 'No token found':
                    if(
                        $this->url[0] == 'login'
                        || ($this->url[0] == 'user' && $this->url[1] == 'create')
                        || ($this->url[0] == 'group' && $this->url[1] == 'getGroupByCode')
                        || ($this->url[0] == 'user' && $this->url[1] == 'isNicknameTaken')
                        || ($this->url[0] == 'user' && $this->url[1] == 'isEmailTaken')
                    ){
                        try {
                            echo json_encode( $this->route($this->url) );
                        } catch (PDOException $e) {
                            echo json_encode( $this->res->error_NO_DB() );
                        }
                    }else{
                        echo json_encode( $this->res->auth_error() );
                    }
                    break;
            }
        }
    }


    private function route($url){
        //url[1] seria el segundo parametro ,solo login no tiene segundo parametro
        if(empty($url[1]) && $url[0] != 'login' && $url[0] != 'token'){
            return $this->res->error_404();
        }
        switch($url[0]){
            case 'login':
                //Autenticacion
                $login = new LoginController($this->token);
                return $login->login();
                break;
            case 'subject';
                //Materias
                return $this->subjectRouter($url[1]);
                break;
            case 'orientation':
                //Orientaciones
                return $this->orientationRouter($url[1]);
                break;
            case 'group':
                //Grupos
                return $this->groupRouter($url[1]);
                break;
            case 'user':
                //Usuarios
                return $this->userRouter($url[1]);
                break;
            case 'user-group':
                //ETC
                return $this->userGroupRouter($url[1]);
                break;
            case 'schedule':
                //Horarios
                return $this->scheduleRouter($url[1]);
                break;
            case 'consultation':
                //Consultas
                return $this->consultationRouter($url[1]);
                break;
            case 'chat':
                //Salas de chat
                return $this->chatRouter($url[1]);
                break;
            case 'token':
                //Verificacion de token
                //El token se verifica antes de entrar aca, este endpoint esta para que el frontend verifique si el token de un usuario es valido y en caso de que no lo sea,deslogearlo
                return "OK";
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }



    private function subjectRouter($pro){
        $subject = new SubjectController($this->token);
        if(!$pro){
            return $this->res->error_404();
        }
        switch($pro){
            case 'create':
                return $subject->createSubject();
                break;
            case 'getSubjects':
                return $subject->getSubjects();
                break;
            case 'getSubjectById':
                return $subject->getSubjectById();
                break;
            case 'modify':
                return $subject->modifySubject();
                break;
            case 'delete':
                return $subject->deleteSubject();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function orientationRouter($pro){
        $orientation = new OrientationController($this->token);
        switch($pro){
            case 'create':
                return $orientation->createOrientation();
                break;
            case 'addOrientationSubjects':
                return $orientation->addOrientationSubjects_();
                break;
            case 'getOrientations':
                return $orientation->getOrientations();
                break;
            case 'getOrientationById':
                return $orientation->getOrientationById();
                break;
            case 'modify':
                return $orientation->modifyOrientation();
                break;
            case 'delete':
                return $orientation->deleteOrientation();
                break;
            case 'removeOrienationSubjects':
                return $orientation->removeOrienationSubjects();
                break;
            case 'getOrienationSubjects':
                return $orientation->getOrienationSubjects();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function groupRouter($pro){
        $group = new GroupController($this->token);
        switch($pro){
            case 'create':
                return $group->createGroup();
                break;
            case 'getGroups':
                return $group->getGroups();
                break;
            case 'getGroupById':
                return $group->getGroupById();
                break;
            case 'getGroupByCode':
                return $group->getGroupByCode();
                break;
            case 'modify':
                return $group->modifyGroup();
                break;
            case 'delete':
                return $group->deleteGroup();
                break;
            case 'getTeachersFromGroup':
                return $group->getTeachersFromGroup();
                break;
            case 'getStudentsFromGroup':
                return $group->getStudentsFromGroup();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function userRouter($pro){
        $user = new UserController($this->token);
        switch($pro){
            case 'create':
                return $user->createUser();
                break;
            case 'getActiveUsers':
                return $user->getActiveUsers();
                break;
            case 'getUserById':
                return $user->getUserById();
                break;
            case 'getUserByNickname':
                return $user->getUserByNickname();
                break;
            case 'modify':
                return $user->modifyUser();
                break;
            case 'delete':
                return $user->deleteUser();
                break;
            case 'acceptUser':
                return $user->acceptUser();
                break;
            case 'rejectUser':
                return $user->rejectUser();
                break;
            case 'getPendantUsers':
                return $user->getPendantUsers();
                break;
            case 'isNicknameTaken':
                return $user->nicknameIsTaken();
                break;
            case 'isEmailTaken':
                return $user->emailIsTaken();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function userGroupRouter($pro){
        $ug = new UserGroupController($this->token);
        switch($pro){
            case 'takeGroup':
                return $ug->giveUserGroup();
                break;
            case 'getGroups':
                return $ug->getUserGroups();
                break;
            case 'leaveGroup':
                return $ug->leaveGroup();
                break;
            case 'takeSubject':
                return $ug->takeSubject();
                break;
            case 'leaveSubject':
                return $ug->leaveSubject();
                break;
            case 'getUserSubjects':
                return $ug->getUserSubjects();
                break;
            case 'getGroupSubjects':
                return $ug->getGroupSubjects();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function scheduleRouter($pro){
        $sh = new ScheduleController($this->token);
        switch($pro){
            case 'addDayToSchedule':
                return $sh->addDayToSchedule();
                break;
            case 'getTeacherSchedule':
                return $sh->getTeacherSchedule();
                break;
            case 'removeDayForSchedule':
                return $sh->removeDayForSchedule();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function consultationRouter($pro){
        $c = new ConsultaController($this->token);
        switch($pro){
            case 'create':
                return $c->createConsulta();
                break;
            case 'getActiveConsultations':
                return $c->getActiveConsultas();
                break;
            case 'getConsultationById':
                return $c->getConsultaById();
                break;
            case 'getAllConsultations':
                return $c->getAllConsultas();
                break;
            case 'close':
                return $c->closeConsulta();
                break;
            case 'postMessage':
                return $c->postMessage();
                break;
            case 'getMessages':
                return $c->getMessages();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

    private function chatRouter($pro){
        $c = new ChatController($this->token);
        switch($pro){
            case 'create':
                return $c->createChat();
                break;
            case 'getActiveChats':
                return $c->getActiveChats();
                break;
            case 'getChatById':
                return $c->getChatById();
                break;
            case 'getAllChats':
                return $c->getAllChats();
                break;
            case 'closeChat':
                return $c->closeChat();
                break;
            case 'postMessage':
                return $c->postMessage();
                break;
            case 'getMessages':
                return $c->getMessages();
                break;
            case 'addParticipant':
                return $c->addParticipant();
                break;
            default:
                return $this->res->error_404();
                break;
        }
    }

}