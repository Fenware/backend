<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/controller.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/schedule.model.php';
/*
API para chequear el token
*/
class ScheduleController extends Controller{

    private $res;
    private $schedule_model;
    function __construct($token)
    {
        $this->res = new Response();
        $this->schedule_model = new ScheduleModel();
        parent::__construct($token);
    }

    public function addDayToSchedule(){
        if($this->token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($this->data,['day'=>'is_int','start_hour'=>'is_string','end_hour'=>'is_string'])){
                /*
                Tengo que ver como chequeo que start_hour y end_hour este en el formato correcto
               */
                //Esta variablela tengo para unos chequeos
                $day = $this->data['day'];
                $start_h = $this->data['start_hour'];
                $end_h = $this->data['end_hour'];
                $check_1 = false;
                $check_2 = false;
                //Chequeo que las horas esten en el formato correcto
                if (preg_match('/^\d{2}:\d{2}$/', $start_h)) {
                    if (preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9])/", $start_h)) {
                        $check_1 = true;
                    }
                }
                //Chequeo que las horas esten en el formato correcto
                if (preg_match('/^\d{2}:\d{2}$/', $end_h)) {
                    if (preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9])/", $end_h)) {
                        $check_2 = true;
                    }
                }

                $day_exists = $this->schedule_model->teacherHasDayCreated($this->token->user_id,$day);
                $this->schedule_model->setTeacher($this->token->user_id);
                $this->schedule_model->setDay($day);
                $this->schedule_model->setStartHour($start_h);
                $this->schedule_model->setEndHour($end_h);
                if($check_1 && $check_2){
                    if(!$day_exists){
                        //Chequeo que el dia sea entre [1,7] (7 dias de la semana ,1 = lunes,7 = domingo)
                        if( $day >= 1 && $day <= 5 ){
                            $rows = $this->schedule_model->createScheduleForDay();
                            if($rows > 0){
                                return 1;
                            }else{
                                return $this->res->error_500();
                            }
                        }else{
                            return $this->res->error_400();
                        }
                    }else{
                        $rows = $this->schedule_model->modifyScheduleForDay();
                        if($rows > 0){
                            return $rows;
                        }else{
                            return $this->res->error_500();
                        }
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

    public function getTeacherSchedule(){
        if($this->token->user_type == 'teacher'){
            return $this->schedule_model->getTeacherSchedule($this->token->user_id);
        }elseif(parent::isTheDataCorrect($this->data, ['teacher'=>'is_int'] )){
            return $this->schedule_model->getTeacherSchedule($this->data['teacher']);
        }else{
            return $this->res->error_400();
        }
    }

    public function removeDayForSchedule(){
        if($this->token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($this->data,['day'=>'is_int'])){
                $rows = $this->schedule_model->deleteTeacherScheduleForDay($this->token->user_id,$this->data['day']);
                if($rows == 0){
                    return $this->res->error_500();
                }else{
                    return 1;
                }
            }
        }else{
            return $this->res->error_403();
        }
    }

}