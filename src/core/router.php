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
header("Access-Control-Allow-Methods: *");
header('Content-type: application/json');
class Router{

    private $url;
    private $token;
    private $res;
    public function __construct()
    {
        $this->res = new Response();
        $middleware = new Middleware();
        //Valido token
        $this->token = $middleware->validate();
        //URL 
        $this->url = $_GET['url'];
        $this->url = rtrim($this->url,'/');
        $this->url = explode('/',$this->url);

        if($this->token != false){
            $this->route($this->url);
        }elseif($this->url[0] == 'login'){
            $this->route(['login']);
        }else{
            echo json_encode( $this->res->auth_error() );
        }
        
    }


    private function route($url){
        switch($url[0]){
            case 'login':
                //Autenticacion
                $login = new LoginController($this->token);
                echo json_encode($login->login());
                break;
            case 'subject';
                //Materias
                echo json_encode($this->subjectRouter($url[1]));
                break;
            case 'orientation':
                //Orientaciones
                echo json_encode($this->orientationRouter($url[1]));
                break;
            case 'group':
                //Grupos
                echo json_encode($this->groupRouter($url[1]));
                break;
            case 'user':
                //Usuarios
                echo json_encode($this->userRouter($url[1]));
                break;
            case 'user-group':
                //ETC
                echo json_encode($this->userGroupRouter($url[1]));
                break;
            case 'schedule':
                //Horarios
                echo json_encode($this->scheduleRouter($url[1]));
                break;
            case 'consultation':
                //Consultas
                echo json_encode($this->consultationRouter($url[1]));
                break;
            case 'chat':
                //Salas de chat
                echo json_encode($this->chatRouter($url[1]));
                break;
            default:
                echo json_encode( $this->res->error_404() );
                break;
        }
    }



    private function subjectRouter($pro){
        $subject = new SubjectController($this->token);
        switch($pro){
            case 'createSubject':
                return $subject->createSubject();
                break;
            case 'getSubjects':
                return $subject->getSubjects();
                break;
            case 'getSubjectById':
                return $subject->getSubjectById();
                break;
            case 'modifySubject':
                return $subject->modifySubject();
                break;
            case 'deleteSubject':
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
            case 'createOrientation':
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
            case 'modifyOrientation':
                return $orientation->modifyOrientation();
                break;
            case 'deleteOrientation':
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
            case 'createGroup':
                return $group->createGroup();
                break;
            case 'getGroups':
                return $group->getGroups();
                break;
            case 'getGroupById':
                return $group->getGroupById();
                break;
            case 'modifyGroup':
                return $group->modifyGroup();
                break;
            case 'deleteGroup':
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
            case 'createUser':
                return $user->createUser();
                break;
            case 'getActiveUsers':
                return $user->getActiveUsers();
                break;
            case 'getUserById':
                return $user->getUserById();
                break;
            case 'modifyUser':
                return $user->modifyUser();
                break;
            case 'deleteUser':
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
            case 'createConsulta':
                return $c->createConsulta();
                break;
            case 'getActiveConsultas':
                return $c->getActiveConsultas();
                break;
            case 'getConsultaById':
                return $c->getConsultaById();
                break;
            case 'getAllConsultas':
                return $c->getAllConsultas();
                break;
            case 'closeConsulta':
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
            case 'createChat':
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