<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/group.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/subject.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/user.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/schedulo.model.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
/*
API para asignar grupos a usuarios
*/
class GroupUserAPI extends API{
    private $res;
    private $group;
    private $user;
    private $schedule_model;
    private $subject_model;
    function __construct()
    {
        $this->res = new Response();
        $this->group = new GroupModel();
        $this->user = new UserModel();
        $this->schedule_model = new ScheduleModel();
        $this->subject_model = new SubjectModel();
        parent::__construct($this->res);
    }


    public function POST($token,$data){
        //Nada aca
    }

    public function GET($token,$data){
        if(parent::isTheDataCorrect($data, ['type'=>'is_string','group'=>'is_string'] )){
            if($data['type'] == 'teacher'){
                $teachers = $this->group->getTeachersInGroup($data['group']);
                foreach($teachers as &$t){
                    $t['schedule'] = $this->schedule_model->getTeacherSchedule($t['id']);
                    $t['subjects'] = $this->subject_model->getTeacherSubjectsInGroup($t['id'],$data['group']);
                }
                $datosArray = $teachers;
            }elseif($data['type'] == 'student'){
                $datosArray = $this->group->getStudentsInGroup($data['group']);
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_400();
        }
        echo json_encode($datosArray);
    }

    public function PUT($token,$data){
        //Nada aca
    }
  
    public function DELETE($token,$data){
        //Nada aca
    }
}