<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/api.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/response.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/model/schedule.model.php';
/*
API para chequear el token
*/
class ScheduleAPI extends API{

    private $res;
    private $schedule_model;
    function __construct()
    {
        $this->res = new Response();
        $this->schedule_model = new ScheduleModel();
        parent::__construct($this->res);
    }

    public function POST($token,$data){
        if($token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['day'=>'is_int','start_hour'=>'is_string','end_hour'=>'is_string'])){
                /*
                Tengo que ver como chequeo que start_hour y end_hour este en el formato correcto
               */
                //Esta variablela tengo para unos chequeos
                $day = $data['day'];
                $start_h = $data['start_hour'];
                $end_h = $data['end_hour'];
                $checked = false;
                //Chequeo que las horas esten en el formato correcto
                if (preg_match('/^\d{2}:\d{2}$/', $start_h)) {
                    if (preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9])/", $start_h)) {
                        $checked = true;
                    }
                }
                //Chequeo que las horas esten en el formato correcto
                if (preg_match('/^\d{2}:\d{2}$/', $end_h)) {
                    if (preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9])/", $end_h)) {
                        $checked = true;
                    }
                }

                $day_exists = $this->schedule_model->teacherHasDayCreated($token->user_id,$day);
                $this->schedule_model->setTeacher($token->user_id);
                $this->schedule_model->setDay($day);
                $this->schedule_model->setStartHour($start_h);
                $this->schedule_model->setEndHour($end_h);
                
                if($checked == true){
                    if(!$day_exists){
                        //Chequeo que el dia sea entre [1,7] (7 dias de la semana ,1 = lunes,7 = domingo)
                        if( $day > 0 && $day < 8 ){
                            $rows = $this->schedule_model->createScheduleForDay();
                            if($rows == 0){
                                $datosArray = $this->res->error_500();
                            }else{
                                $datosArray = 1;
                            }
                        }else{
                            $datosArray = $this->res->error_400();
                        }
                        
                    }else{
                        $rows = $this->schedule_model->modifyScheduleForDay();
                        if($rows == 0){
                            $datosArray = $this->res->error_500();
                        }else{
                            $datosArray = 1;
                        }
                    }
                }else{
                    $datosArray = $this->res->error_400();
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
        if($token->user_type == 'teacher'){
            $datosArray = $this->schedule_model->getTeacherSchedule($token->user_id);
        }elseif(parent::isTheDataCorrect($data, ['teacher'=>'is_int'] )){
            $datosArray = $this->schedule_model->getTeacherSchedule($data['teacher']);
        }else{
            $datosArray = $this->res->error_400();
        }
        echo json_encode($datosArray);
    }
    public function PUT($token,$data){
        if($token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['day'=>'is_int','start_hour'=>'is_string','end_hour'=>'is_string'])){
                /*
                Tengo que ver como chequeo que start_hour y end_hour este en el formato correcto
               */
                $day = $data['day'];
                //Chequeo que el dia sea entre [1,7] (7 dias de la semana ,1 = lunes,7 = domingo)
                if( $day > 0 && $day < 8 ){
                    $this->schedule_model->setTeacher($token->user_id);
                    $this->schedule_model->setDay($day);
                    $this->schedule_model->setStartHour($data['start_hour']);
                    $this->schedule_model->setEndHour($data['end_hour']);
                    $rows = $this->schedule_model->modifyScheduleForDay();
                    if($rows == 0){
                        $datosArray = $this->res->error_500();
                    }else{
                        $datosArray = 1;
                    }
                }else{
                    $datosArray = $this->res->error_400();
                }
            }else{
                $datosArray = $this->res->error_400();
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }

    public function DELETE($token,$data){
        if($token->user_type == 'teacher'){
            if(parent::isTheDataCorrect($data,['day'=>'is_int'])){
                $rows = $this->schedule_model->deleteTeacherScheduleForDay($token->user_id,$data['day']);
                if($rows == 0){
                    $datosArray = $this->res->error_500();
                }else{
                    $datosArray = 1;
                }
            }
        }else{
            $datosArray = $this->res->error_403();
        }
        echo json_encode($datosArray);
    }

}