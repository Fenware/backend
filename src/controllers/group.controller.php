<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/orientation.model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/model/schedule.model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';

/*
API para crear grupos
*/
class GroupController extends Controller{
    private $res;
    private $group;
    private $orientation;
    private $user;
    private $schedule_model;
    private $subject_model;
    function __construct($token)
    {
        $this->res = new Response();
        $this->group = new GroupModel();
        $this->orientation = new OrientationModel();
        $this->user = new UserModel();
        $this->schedule_model = new ScheduleModel();
        $this->subject_model = new SubjectModel();
        parent::__construct($token);
    }

    public function createGroup(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['name'=>'is_string','orientation'=>'is_int'])){
                $length = strlen($this->data['name']);
                $this->data['name'] = strtoupper($this->data['name']);
                if($length == 2){
                    $ori = $this->orientation->getOrientationById($this->data['orientation']);
                    if($ori){
                        $groupExists = $this->group->getGroupInYear($this->data['name'],$ori['year']);
                        if($groupExists){
                            if($groupExists['state'] == 1){
                                return $this->res->error('El grupo ya existe',1030);
                            }else{
                                $rows = $this->group->setGroupActive($groupExists['id']);
                                if($rows == 0){
                                    return $this->res->error_500();
                                }else{
                                    return $groupExists['id'];
                                }
                            }
                        }else{
                            $id = $this->group->postGroup($this->data['name'],$this->data['orientation']);
                            return $this->group->getGroupById($id);
                        }
                    }else{
                        return $this->res->error('La orientacion no existe o fue borrada',1031);
                    }
                }else{
                    return $this->res->error_400();
                }
            }else{
                return $this->res->error_400();
            }
        }else{
            return $this->res->error_403();
        }
    }
    
    public function getGroups(){
        return $this->group->getGroups();
    }

    public function getGroupById(){
        if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
            return $this->group->getGroupById($this->data['id']);
        }else{
            return $this->res->error_400();
        }
    }

    public function getGroupByCode(){
        if(parent::isTheDataCorrect($this->data,['code'=>'is_string'])){
            $group = $this->group->getGroupByCode($this->data['code']);
            if($group){
                $group = $this->group->getGroupById($group['id']);
            }
            return $group;
        }else{
            return $this->res->error_400();
        }
    }

    public function modifyGroup(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['name'=>'is_string','id'=>'is_int'])){
                $length = strlen($this->data['name']);
                if($length == 2){
                    $ori = $this->group->getGroupOrientation($this->data['id']);
                    $ori = $this->orientation->getOrientationById($ori);
                    if($ori){
                        $groupExists = $this->group->getGroupInYear($this->data['name'],$ori['year']);
                        if($groupExists){
                            return $this->res->error('El grupo ya existe',1030);
                        }else{
                            return $this->group->putGroup($this->data['id'],$this->data['name']);
                        }
                    }else{
                        return $this->res->error_500();
                    }
                }else{
                    return $this->res->error_400();
                }
            }
        }else{
            return $this->res->error_403();
        }
    }

    public function deleteGroup(){
        if($this->token->user_type == 'administrator'){
            if(parent::isTheDataCorrect($this->data,['id'=>'is_int'])){
                $rows = $this->group->deleteGroup($this->data['id']);
                $this->group->removeAllTeachersFromGroup($this->data['id']);
                $this->group->removeAllStudentsFromGroup($this->data['id']);
                $this->group->closeAllQuerysInGroup($this->data['id']);
                return $rows;
            }
        }else{
            return $this->res->error_403();
        }
    }


    public function getTeachersFromGroup(){
        if(parent::isTheDataCorrect($this->data, ['group'=>'is_int'] )){
                if($this->token->user_type == 'administrator'){

                    $teachers = $this->group->getTeachersInGroup($this->data['group']);
                    foreach($teachers as &$t){
                        $t['schedule'] = $this->schedule_model->getTeacherSchedule($t['id']);
                        $t['subjects'] = $this->subject_model->getTeacherSubjectsInGroup($t['id'],$this->data['group']);
                    }
                    return $teachers;

                }elseif($this->token->user_type == 'student'){

                    if($this->user->IsUserInGroup($this->token->user_id,$this->data['group'],'student')){
                        
                        $teachers = $this->group->getTeachersInGroup($this->data['group']);
                        foreach($teachers as &$t){
                            $t['schedule'] = $this->schedule_model->getTeacherSchedule($t['id']);
                            $t['subjects'] = $this->subject_model->getTeacherSubjectsInGroup($t['id'],$this->data['group']);
                        }
                        return $teachers;

                    }else{  
                        return $this->res->error_403();
                    }
                }else{
                    return $this->res->error_403();
                }
                
        }else{
            return $this->res->error_400();
        }
    }

    public function getStudentsFromGroup(){
        if($this->token->user_type == 'student'){
            return $this->res->error_403();
        }else{
            if(parent::isTheDataCorrect($this->data, ['group'=>'is_int'] )){
                if($this->token->user_type == 'teacher'){
                    if($this->user->IsUserInGroup($this->token->user_id,$this->data['group'],'teacher')){
                        return $this->group->getStudentsInGroup($this->data['group']);
                    }else{
                        return $this->res->error_403();
                    }
                }elseif($this->token->user_type == 'administrator'){
                    return $this->group->getStudentsInGroup($this->data['group']);
                }
            }else{
                return $this->res->error_400();
            }
            
        }
        
    }


}