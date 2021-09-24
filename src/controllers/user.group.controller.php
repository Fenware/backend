<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
/*
API para asignar grupos a usuarios
*/
class UserGroupController extends Controller{
    private $res;
    private $group;
    private $user;
    private $materia;
    private $orientation;
    function __construct($token)
    {
        $this->res = new Response();
        $this->group = new GroupModel();
        $this->user = new UserModel();
        $this->materia = new SubjectModel();
        $this->orientation = new OrientationModel();
        parent::__construct($token);
    }

    public function giveUserGroup(){
        if($this->token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($this->data,['code'=>'is_string'])){
                $group = $this->group->getGroupByCode($this->data['code']);
                if($group){
                    return $this->user->giveTeacherGroup($this->token->user_id,$group['id']);
                }else{
                    return $this->res->error('El grupo no existe',1052);
                }
            }else{
                return $this->res->error_400();
            }
        }
        if($this->token->user_type == 'student'){
            if(parent::isTheDataCorrect($$this->data,['code'=>'is_string'])){
                if($this->user->userHasGroup($this->token->user_id)){
                    return $this->res->error('Un estudiante solo puede estar en un grupo a la vez',1050);
                }else{
                    $group = $this->group->getGroupByCode($this->data['code']);
                    if($group){
                        return $this->user->giveStudentGroup($this->token->user_id,$group['id']);
                    }else{
                        return $this->res->error('El grupo no existe',1052);
                    }
                }
            }else{
                return $this->res->error_400();
            }
        }
        
    }

    

    public function getUserGroups(){
        if($this->token->user_type == 'administrator'){
            //TODO
        }else{
            $grupos = $this->user->getUserGroups($this->token->user_id,$this->token->user_type);
            return $grupos;
        }
        
    }


    public function leaveGroup(){
        //Chequeo que este intentando modificarse a si mismo
        if(parent::isTheDataCorrect($this->data,['grupo'=>'is_int'])){
            $grupo = $this->data['grupo'];
            return $this->user->deleteUserGroup($this->token->user_id,$grupo,$this->token->user_type);
        }else{
            $grupo = 0;
            return $this->user->deleteUserGroup($this->token->user_id,$grupo,$this->token->user_type);
        }
    }


    public function takeSubject(){
        if($this->token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($this->data,['grupo'=>'is_int','materia'=>'is_int'])){
                if($this->group->IsSubjectInGroup($this->data['grupo'],$this->data['materia'])){
                    $taken = $this->materia->IsSubjectInGroupTaken($this->data['grupo'],$this->data['materia']);
                    if($taken){
                        return $this->res->error('Esta materia ya tiene un docente',1060);
                    }else{
                        $in_group = $this->user->IsUserInGroup($this->token->user_id,$this->data['grupo'],$this->token->user_type);
                        if($in_group){
                            $result = $this->materia->GiveSubjectInGroupToTeacher($this->token->user_id,$this->data['grupo'],$this->data['materia']);
                            if($result == 0){
                                return $this->res->error_500();
                            }else{
                                return 1;
                            }
                        }else{
                            return $this->res->error('No perteneces a este grupo',1061);
                        }
                    }
                }else{
                    return $this->res->error('Esta materia no pertenece a este grupo',1062);
                }
            }else{
                return $this->res->error_400();
            }
        }
    }


    public function getUserSubjects(){
        if($this->token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($this->data,['group'=>'is_int'])){
                return $this->materia->getTeacherSubjectsInGroup($this->token->user_id, $this->data['group']);
            }else{
                return $this->res->error_400();
            }
        }
    }


    public function leaveSubject(){
        if($this->token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($this->data,['grupo'=>'is_int','materia'=>'is_int'])){
                return $this->materia->removeTeacherFromSubjectInGroup($this->token->user_id,$this->data['grupo'],$this->data['materia']);
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }

    public function getGroupSubjects(){
        if(parent::isTheDataCorrect($this->data, ['group'=>'is_int'] )){
            $ori = $this->group->getGroupOrientation($this->data['group']);
            if($ori){
                $subjects = $this->orientation->getOrientationSubjects($ori['id']);
                foreach($subjects as &$s){
                    $s['taken'] = $this->materia->IsSubjectInGroupTaken($this->data['group'],$s);
                }
                return $subjects;
            }else{
                return $this->res->error_500();
            }
        }else{
            return $this->res->error_400();
        }

    }
}